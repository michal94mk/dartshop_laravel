@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ __('Your Profile') }}</h2>
                    <p>{{ __('Update your account settings and information.') }}</p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3>{{ __('Update Profile Information') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @include('profile.partials.update-profile-information-form')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3>{{ __('Update Password') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @include('profile.partials.update-password-form')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3>{{ __('Delete Account') }}</h3>
                                </div>
                                <div class="panel-body">
                                    @include('profile.partials.delete-user-form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @if (session('status') === 'password-updated')
    <div class="modal fade" id="passwordUpdatedModal" tabindex="-1" role="dialog" aria-labelledby="passwordUpdatedModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>{{ __('Password updated.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#passwordUpdatedModal').modal('show');
            setTimeout(function () {
                $('#passwordUpdatedModal').modal('hide');
            }, 2000);
        });
    </script>
@endif --}}

@endsection
