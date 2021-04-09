@extends('layouts.main')
@section('title', 'User Profile')

@section('content')

<div class="container-1200 page-container page-user-profile mt-5">

    <div class="h2 text-orange">Profile</div>

    <div class="row">

        <div class="col-6 mx-auto">

            <div class="container mt-5 border rounded p-5">

                <div class="row">

                    <div class="col-12">

                        <div class="text-orange font-11 mb-3">Your Signature</div>

                        <form id="edit_user_form">

                            <div class="row">

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="first_name" name="first_name" data-label="First Name" value="{{ $user -> first_name }}">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="last_name" name="last_name" data-label="Last Name" value="{{ $user -> last_name }}">
                                </div>

                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input required" id="email" name="email" data-label="Email" value="{{ $user -> email }}">
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-around">
                                        <a class="btn btn-primary" id="save_profile_button"><i class="fad fa-save mr-2"></i> Save</a>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

                <div class="row">

                    <div class="col-12">

                        <div class="edit-div">

                            <div>

                                <div class="employee-image-div">

                                    <div class="text-orange font-11 mb-3">Profile Picture</div>

                                    <div class="row">

                                        <div class="col-12 col-sm-6">

                                            <div class="d-flex justify-content-center h-100 mt-4">

                                                <div class="image-div relative has-photo @if($user -> photo_location == '') hidden @endif">
                                                    <div class="delete-image-div">
                                                        <a href="javascript: void(0)" class="delete-image-button"><i class="fal fa-times fa-lg text-danger"></i></a>
                                                    </div>
                                                    <img class="shadow rounded" id="photo_location" src="{{ $user -> photo_location }}">
                                                </div>

                                                <div class="no-photo my-auto @if($user -> photo_location != '') hidden @endif">
                                                    <i class="fad fa-user fa-5x text-primary"></i>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="text-gray mb-3">Add/Replace Photo</div>
                                            <input type="file" id="agent_photo_file" name="agent_photo_file">
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="crop_modal" tabindex="-1" role="dialog" aria-labelledby="crop_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="crop_modal_title">Crop Image</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="d-flex justify-content-around align-items-center modal-body">
                <div class="crop-container"></div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_crop_button" data-dismiss"modal"><i class="fad fa-save mr-2"></i> Save</a>
            </div>
        </div>
    </div>
</div>

@endsection
