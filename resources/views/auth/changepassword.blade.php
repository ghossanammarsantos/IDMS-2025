@extends('admin.layouts.app', [
    'activePage' => 'Change Password',
])

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-gradient-directional-info pl-2 pt-1 pb-1">
                                    <h4 class="card-title text-white" id="basic-layout-form">Change Password</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body p-1">
                                        <!-- Form untuk mengubah password -->
                                        <form method="POST" action="{{ route('auth.changepassword') }}">
                                            @csrf

                                            <div class="form-group row">
                                                <label for="current_password" class="col-md-4 col-form-label text-md-right">{{ __('Current Password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">

                                                    @error('current_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="new_password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required autocomplete="new-password">

                                                    @error('new_password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="new_password_confirmation" class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>

                                                <div class="col-md-6">
                                                    <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required autocomplete="new-password">
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('Change Password') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
