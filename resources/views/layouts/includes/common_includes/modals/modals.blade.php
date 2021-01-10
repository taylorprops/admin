<!-- Success Modal -->
<div class="modal fade" id="modal_success" tabindex="-1" role="dialog" aria-labelledby="modal_success_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="modal_success_title">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times text-white"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div class="modal fade" id="modal_info" tabindex="-1" role="dialog" aria-labelledby="modal_info_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_info_title">Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times text-white"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Fail Modal -->
<div class="modal fade" id="modal_danger" tabindex="-1" role="dialog" aria-labelledby="modal_danger_title" aria-hidden="true">
    <div class="modal-notify modal-danger modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modal_danger_title">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times text-white"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="modal fade draggable" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="confirm_title"></h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-success modal-confirm-button" data-dismiss="modal" id="confirm_button"><i class="fad fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>
