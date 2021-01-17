@if(count($transaction_checklist_item_notes) > 0)

    <div class="notes-container bg-white px-1">

        @foreach($transaction_checklist_item_notes as $transaction_checklist_item_note)
            @php
            $user = $users -> where('id', $transaction_checklist_item_note -> note_user_id) -> first();
            $username = $user -> name;

            if($user -> group == 'admin') {
                $emp_photo_location = $admin_details -> photo_location ?? null;
                $avatar_bg = 'bg-orange';
            } else if($user -> group == 'agent') {
                $emp_photo_location = $agent_details -> photo_location ?? null;
                $avatar_bg = 'bg-primary';
            }
            if(!$emp_photo_location) {
                $initials = substr($user -> name, 0, 1);
                $initials .= substr($user -> name, strpos($user -> name, ' ') + 1, 1);
            }

            $unread = null;
            if($transaction_checklist_item_note -> note_status == 'unread' && $transaction_checklist_item_note -> note_user_id != auth() -> user() -> id) {
                $unread = 'unread';
            }

            $created_at = $transaction_checklist_item_note -> created_at;
            $date_added = $created_at -> format('n/j/Y g:iA');
            if($created_at -> format('Y-m-d') == date('Y-m-d')) {
                $date_added = 'Today at '.$created_at -> format('g:iA');
            } else if($created_at -> format('Y-m-d') == date('Y-m-d', strtotime('-1 day'))) {
                $date_added = 'Yesterday at '.$created_at -> format('g:iA');
            }
            @endphp

            <div class="p-2 mb-3 note-div rounded @if($unread) bg-orange-light animate__animated animate__shakeX @else bg-blue-light @endif">

                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="emp_photo mr-2">
                            <div class="rounded-pill avatar-initials {{ $avatar_bg }} p-2">
                                @if($emp_photo_location)
                                    <img src="{{ $emp_photo_location }}" class="avatar rounded-circle d-flex align-self-center mr-2 shadow">
                                @else
                                    <span class="text-white">{{ $initials }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-primary font-italic">{{ $username }}</div>
                    </div>
                    <div>
                        @if($transaction_checklist_item_note -> note_status == 'unread')
                            @if($transaction_checklist_item_note -> note_user_id != auth() -> user() -> id)
                                <button class="btn btn-success btn-sm mark-read-button mb-0" data-note-id="{{ $transaction_checklist_item_note -> id }}" data-notes-collapse="notes_div_{{ $checklist_item_id }}"><i class="fal fa-check mr-2"></i> Mark Read</button>
                            @else
                                <div class="d-flex justify-content-end align-items-center">
                                    <span class="text-gray small">Not Read</span>
                                    @if($transaction_checklist_item_note -> note_user_id == auth() -> user() -> id)
                                        <a href="javascript: void(0)" class="delete-note-button ml-2" data-note-id={{ $transaction_checklist_item_note -> id }}"><i class="fal fa-times-circle text-danger"></i></a>
                                    @endif
                                </div>
                            @endif
                        @else
                            <span class="text-success small"><i class="fal fa-check"></i> Read</span>
                        @endif
                    </div>
                </div>

                <div class="text-gray bg-white p-2 rounded">
                    {!! $transaction_checklist_item_note -> notes !!}
                </div>

                <div class="text-gray font-7 mt-1">{{ $date_added }}</div>

            </div>



        @endforeach

    </div>
@endif
