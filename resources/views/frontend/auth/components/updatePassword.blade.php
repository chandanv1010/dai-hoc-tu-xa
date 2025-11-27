
@extends('frontend.homepage.layout')
@section('content')
    <div class="forgotpassword-container">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-center">
                <div class="forgot-password-form">
                    <form class="update-password" action="{{ route($route, $email) }}" method="post">
                        @csrf
                            <div class="form-heading">Cập nhật mật khẩu</div>
                            <div class="description mb20">
                                Nhập đầy đủ mật khẩu mới, và xác nhận lại mật khẩu mới của bạn
                            </div>
                            <div class="form-row mb10">
                                <input 
                                    type="password" 
                                    class="input-text" 
                                    name="password"
                                    placeholder="Mật khẩu"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <svg class="eye-icon" id="eye-password" fill="currentColor" viewBox="0 0 20 20" style="display: block;">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg class="eye-icon" id="eye-slash-password" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-row">
                                <input 
                                    type="password" 
                                    class="input-text" 
                                    name="re_password"
                                    placeholder="Nhập lại mật khẩu"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <svg class="eye-icon" id="eye-password" fill="currentColor" viewBox="0 0 20 20" style="display: block;">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg class="eye-icon" id="eye-slash-password" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                                    </svg>
                                </button>
                            </div>
                            <button type="submit" class="btn btn-primary block full-width m-b">Đổi mật khẩu</button>  
                    </form>
                    <p class="m-t mt5">
                        <small>{{ $system['homepage_brand'] }} System 2023</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
