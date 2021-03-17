<!-- Success Modal -->
<div class="modal fade" id="modal_success" tabindex="-1" role="dialog" aria-labelledby="modal_success_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="modal_success_title"><i class="fal fa-check-circle mr-3"></i> Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times text-danger"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div class="modal fade" id="modal_info" tabindex="-1" role="dialog" aria-labelledby="modal_info_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="modal_info_title"><i class="fad fa-info-circle mr-3"></i> Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times text-danger"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Fail Modal -->
<div class="modal fade" id="modal_danger" tabindex="-1" role="dialog" aria-labelledby="modal_danger_title" aria-hidden="true">
    <div class="modal-notify modal-danger modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="modal_danger_title"><i class="fad fa-exclamation-circle mr-3"></i> Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times text-danger"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="modal fade draggable" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="confirm_title"></h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" data-dismiss="modal" id="confirm_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>
