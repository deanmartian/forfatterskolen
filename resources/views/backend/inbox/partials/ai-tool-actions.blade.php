{{--
    Viser AI-foreslåtte handlinger som klikkbare knapper under et AI-utkast.

    Forventer:
    - $aiMessage : InboxMessage (AI-draft)
    - $conversation : InboxConversation
--}}
@php
    $actions = $aiMessage->aiToolActions()->orderBy('id')->get();
@endphp

@if($actions->isNotEmpty())
    <div style="margin-top:14px; padding:12px 14px; background:#f0f7ff; border-left:3px solid #3b82f6; border-radius:6px;">
        <div style="font-size:12px; font-weight:600; color:#1e40af; margin-bottom:8px;">
            <i class="fa fa-magic"></i> AI-foreslåtte handlinger
        </div>

        @foreach($actions as $action)
            @php
                $isDone = $action->status === \App\Enums\AiToolActionStatus::Executed;
                $isFailed = $action->status === \App\Enums\AiToolActionStatus::Failed;
                $isExpired = $action->status === \App\Enums\AiToolActionStatus::Expired
                    || ($action->expires_at && $action->expires_at->isPast());
                $clickable = $action->isClickable() && !$isExpired;
            @endphp

            <div style="margin-bottom:6px; display:flex; align-items:center; gap:10px;">
                @if($clickable)
                    <form action="{{ route('admin.inbox.execute-tool', ['id' => $conversation->id, 'actionId' => $action->id]) }}"
                          method="POST" style="display:inline; margin:0;">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Utfør handling: {{ addslashes($action->ui_label) }}?');"
                                class="btn btn-sm"
                                style="background:#3b82f6; color:#fff; border:none; padding:6px 12px; border-radius:5px; font-size:12px; font-weight:500;">
                            @if($isFailed)
                                <i class="fa fa-redo"></i> Prøv igjen
                            @else
                                <i class="fa fa-bolt"></i>
                            @endif
                            {{ $action->ui_label }}
                        </button>
                    </form>
                    @if($isFailed && $action->error_message)
                        <span style="font-size:11px; color:#dc2626;">
                            <i class="fa fa-exclamation-circle"></i> {{ $action->error_message }}
                        </span>
                    @endif
                @elseif($isDone)
                    <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; background:#dcfce7; color:#166534; border-radius:5px; font-size:12px;">
                        <i class="fa fa-check-circle"></i>
                        Utført: {{ $action->ui_label }}
                        @if($action->executed_at)
                            <span style="color:#999; font-size:10px;">({{ $action->executed_at->diffForHumans() }})</span>
                        @endif
                    </span>
                @elseif($isExpired)
                    <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; background:#f3f4f6; color:#9ca3af; border-radius:5px; font-size:12px; text-decoration:line-through;">
                        <i class="fa fa-clock"></i>
                        Utløpt: {{ $action->ui_label }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>
@endif
