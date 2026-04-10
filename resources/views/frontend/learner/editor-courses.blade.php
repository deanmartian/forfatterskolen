@extends('frontend.layout')

@section('page_title', 'Velg kurs - Forfatterskolen')

@section('content')
<div class="container-fluid" style="padding: 2rem;">
    <h2 style="font-family: 'Source Sans 3', sans-serif; font-weight: 700; margin-bottom: 0.5rem;">Velg kurs å følge</h2>
    <p style="color: #666; margin-bottom: 2rem;">Meld deg på kurs som redaktør. Du får tilgang i 1 år.</p>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 8px; margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 8px; margin-bottom: 1.5rem;">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info" style="border-radius: 8px; margin-bottom: 1.5rem;">{{ session('info') }}</div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
        @forelse($courses as $course)
            @php
                $isEnrolled = collect($enrolledCourseIds)->contains($course->id);
            @endphp
            <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; transition: box-shadow 0.15s; {{ $isEnrolled ? 'border-color: #862736;' : '' }}">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h4 style="font-family: 'Source Sans 3', sans-serif; font-weight: 600; font-size: 1.05rem; margin: 0; line-height: 1.3;">{{ $course->title }}</h4>
                        @if($isEnrolled)
                            <span style="background: #862736; color: #fff; font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 20px; white-space: nowrap; margin-left: 0.5rem;">Aktiv</span>
                        @endif
                    </div>
                    <span style="display: inline-block; font-size: 0.75rem; color: #666; background: #f3f4f6; padding: 0.15rem 0.5rem; border-radius: 4px; margin-bottom: 0.75rem;">
                        {{ $course->packages->first()->variation ?? 'Editor' }}
                    </span>
                </div>
                <div style="margin-top: 1rem;">
                    @if($isEnrolled)
                        <span style="color: #862736; font-weight: 600; font-size: 0.85rem;">Du følger dette kurset</span>
                    @else
                        <form method="POST" action="{{ route('learner.editor-courses.enroll') }}">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <button type="submit" style="width: 100%; background: #862736; color: #fff; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-family: 'Source Sans 3', sans-serif; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.15s;">
                                Følg dette kurset
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p style="color: #999;">Ingen kurs med Editor-pakke er tilgjengelige.</p>
        @endforelse
    </div>
</div>
@endsection
