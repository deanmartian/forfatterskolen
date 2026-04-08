{{--
    Viser AI-foreslåtte handlinger som klikkbare knapper under et AI-utkast.

    Forventer:
    - $aiMessage : InboxMessage (AI-draft)
    - $conversation : InboxConversation
--}}
@php
    $actions = $aiMessage->aiToolActions()->orderBy('id')->get();

    // Map fra tool-navn til ikon (font awesome) og fargekode
    $toolMeta = [
        // Lookup (grå — read-only)
        'get_user_courses' => ['icon' => 'fa-book', 'color' => '#6b7280'],
        'get_invoice_status' => ['icon' => 'fa-file-invoice-dollar', 'color' => '#6b7280'],
        'get_assignment_status' => ['icon' => 'fa-pen-nib', 'color' => '#6b7280'],
        'get_upcoming_webinars' => ['icon' => 'fa-video', 'color' => '#6b7280'],

        // Action — blå (standard)
        'add_internal_note' => ['icon' => 'fa-sticky-note', 'color' => '#3b82f6'],
        'send_login_link' => ['icon' => 'fa-key', 'color' => '#059669'],
        'send_password_reset' => ['icon' => 'fa-unlock-alt', 'color' => '#059669'],
        'extend_assignment_deadline' => ['icon' => 'fa-calendar-plus', 'color' => '#d97706'],
        'approve_extension_request' => ['icon' => 'fa-check-circle', 'color' => '#d97706'],
        'register_for_webinar' => ['icon' => 'fa-user-plus', 'color' => '#3b82f6'],
        'assign_editor_to_manuscript' => ['icon' => 'fa-user-edit', 'color' => '#7c3aed'],
        'mark_conversation_done' => ['icon' => 'fa-check-double', 'color' => '#059669'],
    ];
@endphp

@if($actions->isNotEmpty())
    <div style="margin-top:14px; padding:12px 14px; background:#f0f7ff; border-left:3px solid #3b82f6; border-radius:6px;">
        <div style="font-size:12px; font-weight:600; color:#1e40af; margin-bottom:10px;">
            <i class="fa fa-magic"></i> AI-foreslåtte handlinger ({{ $actions->count() }})
        </div>

        @foreach($actions as $action)
            @php
                $isDone = $action->status === \App\Enums\AiToolActionStatus::Executed;
                $isFailed = $action->status === \App\Enums\AiToolActionStatus::Failed;
                $isExpired = $action->status === \App\Enums\AiToolActionStatus::Expired
                    || ($action->expires_at && $action->expires_at->isPast());
                $clickable = $action->isClickable() && !$isExpired;

                $meta = $toolMeta[$action->tool_name] ?? ['icon' => 'fa-bolt', 'color' => '#3b82f6'];
                $icon = $meta['icon'];
                $color = $meta['color'];
            @endphp

            <div style="margin-bottom:6px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                @if($clickable)
                    <form action="{{ route('admin.inbox.execute-tool', ['id' => $conversation->id, 'actionId' => $action->id]) }}"
                          method="POST" style="display:inline; margin:0;">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Utfør handling:\n\n{{ addslashes($action->ui_label) }}\n\nEr du sikker?');"
                                class="btn btn-sm"
                                style="background:{{ $color }}; color:#fff; border:none; padding:7px 14px; border-radius:6px; font-size:12px; font-weight:500; display:inline-flex; align-items:center; gap:6px; box-shadow:0 1px 2px rgba(0,0,0,0.08);">
                            @if($isFailed)
                                <i class="fa fa-redo"></i> Prøv igjen: {{ $action->ui_label }}
                            @else
                                <i class="fa {{ $icon }}"></i> {{ $action->ui_label }}
                            @endif
                        </button>
                    </form>
                    @if($isFailed && $action->error_message)
                        <span style="font-size:11px; color:#dc2626;">
                            <i class="fa fa-exclamation-circle"></i> {{ $action->error_message }}
                        </span>
                    @endif
                @elseif($isDone)
                    <span style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#dcfce7; color:#166534; border-radius:6px; font-size:12px; font-weight:500;">
                        <i class="fa fa-check-circle"></i>
                        Utført: {{ $action->ui_label }}
                        @if($action->executed_at)
                            <span style="color:#6b7280; font-size:10px; font-weight:normal;">({{ $action->executed_at->diffForHumans() }})</span>
                        @endif
                    </span>
                @elseif($isExpired)
                    <span style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#f3f4f6; color:#9ca3af; border-radius:6px; font-size:12px; text-decoration:line-through;">
                        <i class="fa fa-clock"></i>
                        Utløpt: {{ $action->ui_label }}
                    </span>
                @endif
            </div>
        @endforeach

        <div style="margin-top:8px; font-size:11px; color:#6b7280;">
            <i class="fa fa-info-circle"></i> Ingen handlinger kjøres automatisk — klikk en knapp for å godkjenne. Forslag utløper etter 7 dager.
        </div>
    </div>
@endif
