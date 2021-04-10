@extends('layouts.main')
@section('title', 'Website Users')

@section('content')

<div class="container-1200 page-container page-users mt-3">

    <div class="row">

        <div class="col-12">

            <div class="h2 text-orange my-4">Website Users</div>

            <div class="no-wrap hidden users-table-wrapper animate__animated animate__slow animate__fadeIn">

                <table id="users_table" class="table table-hover table-bordered table-sm" width="100%">

                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td width="100"><button class="btn btn-primary btn-sm send-password-reset-button" data-email="{{ $user -> email }}">Send Reset Password Email</button></td>
                                <td width="100"><button class="btn btn-primary btn-sm send-register-email-button" data-email="{{ $user -> email }}">Send Registration Email</button></td>
                                <td>{{ $user -> last_name.', '.$user -> first_name }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $user -> group)) }}</td>
                                <td><a href="mailto:{{ $user -> email }}">{{ $user -> email }}</a></td>
                                <td align="center">
                                    @if($user -> photo_location != '')
                                        <img src="{{ $user -> photo_location }}" class="rounded" height="50">
                                    @else
                                        <i class="fa fa-user fa-2x text-primary"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </body>

                </table>

            </div>



        </div>

    </div>

</div>

@endsection
