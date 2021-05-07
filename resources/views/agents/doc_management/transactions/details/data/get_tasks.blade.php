<div class="row m-1 m-sm-4">

    <div class="col-12 col-xl-10 mx-auto">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <button class="btn btn-primary add-task-button" data-type="task" data-action="add"><i class="fal fa-plus mr-2"></i> Add Task <i class="fal fa-tasks ml-2"></i></button>
                <button class="btn btn-primary add-task-button" data-type="reminder" data-action="add"><i class="fal fa-plus mr-2"></i> Add Reminder <i class="fal fa-clock ml-2"></i></button>
            </div>

            <div>
                <div class="btn-group" role="group" aria-label="Status">
                    <button type="button" class="btn btn-primary filter-tasks active" data-toggle="button" data-show="active">Active</button>
                    <button type="button" class="btn btn-primary filter-tasks" data-toggle="button" data-show="completed">Completed</button>
                </div>
            </div>

        </div>




        <div class="relative">

            <div class="task-container collapse">

                <form id="task_form">

                    <div class="task-div shadow">

                        <div class="row">

                            <div class="col-12">

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-primary font-11 mt-2 ml-2 task-header"></div>
                                    <a href="javascript: void(0)" class="text-danger font-12 close-collapse">
                                        <i class="fal fa-times mt-2 fa-lg"></i>
                                    </a>
                                </div>

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col-12">
                                <select class="custom-form-element form-select form-select-no-search" multiple id="task_members" name="task_members[]" data-label="Members">
                                    <option value=""></option>
                                    @foreach($members as $member)
                                        @if($member -> Agent_ID > 0 || $member -> TransactionCoordinator_ID > 0)
                                            <option value="{{ $member -> id }}" @if(count($members) == 1) selected @endif>{{ ucwords(strtolower($member -> first_name.' '.$member -> last_name)) }}</option>
                                        @endif
                                    @endforeach
                                </select>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12">

                                <textarea class="custom-form-element form-textarea" id="task_title" name="task_title" data-label="Description"></textarea>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12">

                                <div class="w-100 text-center text-orange font-10 mt-1 mb-1">Select trigger event or enter date below</div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12">

                                <div class="p-2 border rounded">

                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="w-15">
                                            <input type="text" class="custom-form-element form-input numbers-only date-change-trigger text-center px-0" min="0" id="task_option_days" name="task_option_days" value="0" data-label="">
                                        </div>
                                        <div class="mx-3 text-gray">
                                            Days
                                        </div>
                                        <div class="w-40">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel date-change-trigger" id="task_option_position" name="task_option_position" data-label="Before/After">
                                                <option value="after" selected>After</option>
                                                <option value="before">Before</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-0">

                                        <select class="custom-form-element form-select form-select-no-search date-change-trigger" id="task_action" name="task_action" data-label="Event">
                                            <option value=""></option>
                                            @foreach($task_actions -> where('has_db_column', 'yes') as $task_action)
                                                <option value="{{ $task_action -> resource_id }}" data-has-db-column="{{ $task_action -> has_db_column }}" data-date="{{ $task_action -> event_date ? $task_action -> event_date : '' }}">{{ $task_action -> resource_name }} - {{ $task_action -> event_date ? date_mdy($task_action -> event_date) : 'Pending' }}</option>
                                            @endforeach

                                            {{-- @if(count($tasks -> where('task_date', '>=', date('Y-m-d'))) > 0) --}}
                                                @foreach($task_actions -> where('has_db_column', 'no') as $task_action)
                                                    <option value="{{ $task_action -> resource_id }}" data-has-db-column="{{ $task_action -> has_db_column }}" data-date="{{ $task_action -> event_date }}">{{ $task_action -> resource_name }}</option>
                                                @endforeach
                                            {{-- @endif --}}
                                        </select>

                                    </div>

                                    <div class="task-action-task hidden mt-0">

                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel date-change-trigger" id="task_action_task" name="task_action_task" data-label="Other Task">
                                            @foreach($tasks /*  -> where('task_date', '>=', date('Y-m-d')) */ as $task)
                                                @php
                                                $task_date_completed = null;
                                                $task_completed = null;

                                                $task_date = $task -> task_date ? $task -> task_date : null;
                                                if($task -> status == 'completed') {
                                                    $task_date = $task -> task_date_completed;
                                                    $task_date_completed = $task -> task_date_completed;
                                                    $task_completed = 'yes';
                                                }
                                                @endphp
                                                <option value="{{ $task -> id }}" data-date="{{ $task_date }}" data-task-completed="{{ $task_completed }}" data-date-completed="{{ $task_date_completed }}" @if($loop -> first) selected @endif>{{ $task_date ? date_mdy($task_date) : 'Pending' }} - {{ $task -> task_title }}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row mt-2">

                            <div class="col-6">

                                <input type="date" class="custom-form-element form-input date-field task-date" id="task_date" name="task_date" data-label="Reminder Date">

                            </div>

                            <div class="col-4 reminder-ele">

                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel" id="task_time" name="task_time" data-label="Time">
                                    <option value="00:00:00">12:00am</option>
                                    <option value="00:15:00">12:15am</option>
                                    <option value="00:30:00">12:30am</option>
                                    <option value="00:45:00">12:45am</option>
                                    @php
                                    for($h=1; $h<24; $h++) {
                                        $ampm = 'am';
                                        $h_value = $h;
                                        if($h < 10) {
                                            $h_value = '0'.$h;
                                        }
                                        $h_display = $h;
                                        if($h > 12) {
                                            $h_display -= 12;
                                        }
                                        if($h > 11) {
                                            $ampm = 'pm';
                                        }
                                        for($m=0; $m<46; $m+=15) {
                                            if($m == 0) {
                                                $m = '00';
                                            }
                                            echo '<option value="'.$h_value.':'.$m.':00">'.$h_display.':'.$m.$ampm.'</option>';
                                        }
                                    }
                                    @endphp
                                </select>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12">

                                <div class="alert alert-danger no-date-info-div hidden font-9 mt-2">
                                    Since there is no date yet for <span class="no-date-event"></span> the task date will remain empty.<br>The task date will be added once the event date is set.
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12">

                                <hr>

                            </div>

                        </div>

                        <div class="row pb-3">

                            <div class="col-12">

                                <div class="d-flex justify-content-around align-items-center">
                                    <a href="javascript: void(0)" class="btn btn-primary" id="save_task_button"><i class="fal fa-plus mr-2"></i> Save <span class="task-type"></span></a>
                                </div>

                            </div>

                        </div>

                        <div class="row pb-5 pb-sm-3 mt-2 delete-div hidden">

                            <div class="col-12">

                                <div class="d-flex justify-content-around align-items-center">
                                    <a href="javascript: void(0)" class="text-danger" id="delete_task_button"><i class="fal fa-times mr-2"></i> Delete Task</a>
                                </div>

                            </div>

                        </div>

                    </div>

                    <input type="hidden" id="task_id" name="task_id">
                    <input type="hidden" id="reminder" name="reminder">

                </form>

            </div>

        </div>

    </div>

</div>


<div class="row mt-3">

    <div class="col-12 col-xl-10 mx-auto">

        <div class="list-group tasks-list-group mx-0 mx-sm-2 mx-md-3 mb-3">

            @foreach($tasks as $task)

            @php
            $task_members = $task -> members -> pluck('member_id') -> toArray();
            $task_members = implode(',', $task_members);
            @endphp

                <div class="list-group-item list-group-item-action task @if(!$task -> task_date) pending @endif" data-status="{{ $task -> status }}">

                    <div class="d-flex flex-wrap justify-content-between align-items-center text-gray">

                        <div class="d-flex justify-content-start align-items-center">

                            <div class="mr-3">
                                @if($task -> reminder == 0)
                                    <i class="fal fa-tasks text-primary fa-lg"></i>
                                @else
                                    <i class="fal fa-clock text-gray fa-lg"></i>
                                @endif
                            </div>

                            <div class="mr-3">
                                <button class="btn btn-primary btn-sm edit-task-button"
                                data-action="edit"
                                data-task-id="{{ $task -> id }}"
                                data-reminder="{{ $task -> reminder }}"
                                data-type="{{ $task -> reminder == 0 ? 'task' : 'reminder' }}"
                                data-task-title="{{ $task -> task_title }}"
                                data-task-option-days="{{ $task -> task_option_days }}"
                                data-task-option-position="{{ $task -> task_option_position }}"
                                data-task-action="{{ $task -> task_action }}"
                                data-task-action-task="{{ $task -> task_action_task }}"
                                data-task-date="{{ $task -> task_date }}"
                                data-task-time="{{ $task -> task_time }}"
                                data-task-members="{{ $task_members }}"
                                ><i class="fa fa-pencil"></i></button>
                            </div>

                            <div class="font-9 mr-4 wpx-120 @if($task -> task_date < date('Y-m-d')) text-danger @endif">
                                @if($task -> task_date)
                                    {{ date('D - M jS', strtotime($task -> task_date)) }}
                                    @if($task -> reminder == 1)
                                        <br>
                                        <span class="font-8">{{ date('g:ia', strtotime($task -> task_time)) }}</span>
                                    @endif
                                @else
                                    Pending...
                                @endif
                            </div>

                            <div class="mr-4">
                                {{ $task -> task_title }}
                            </div>

                        </div>

                        <div class="d-flex flex-wrap justify-content-end align-items-center">

                            <div class="text-right mr-4">
                                @foreach($task -> members as $member)
                                    {{ $member -> member_details -> first_name.' '.$member -> member_details -> last_name }}
                                    @if(!$loop -> last) <br> @endif
                                @endforeach
                            </div>

                            <div>
                                @if($task -> status == 'active')
                                    <button class="btn btn-primary mark-completed-button" data-task-id="{{ $task -> id }}" data-status="completed"><i class="fal fa-check mr-2"></i> Mark as Done</button>
                                @else
                                    <div class="d-flex justify-content-end align-items-center">
                                        <div class="p-2 bg-success text-white rounded"><i class="fal fa-check mr-2"></i> Done</div>
                                        <div class="ml-3">
                                            <a href="javascript: void(0)" class="mark-completed-button" data-task-id="{{ $task -> id }}" data-status="active"><i class="fal fa-undo mr-1"></i> Undo</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>
