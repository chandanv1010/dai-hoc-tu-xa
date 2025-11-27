@extends('frontend.homepage.layout')
@section('content')
    <div class="register-container">
        <div class="uk-container uk-container-center">
            <div class="register-page">
                <div class="page-breadcrumb">
                    <div class="uk-container uk-container-center">
                        <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
                            <li>
                                <a href="/">Trang chủ</a>
                            </li>
                            <li>
                                <span class="slash">/</span>
                            </li>
                            <li>
                                <a href="" title="Đăng ký" class="rg">Đăng ký</a>
                            </li>
                        </ul>
                    </div>
                </div>    
                @php
                    $slideKeyword = App\Enums\SlideEnum::WHYCHOOSE;
                @endphp  
                @if(count($slides[$slideKeyword]['item']))
                    <div class="panel-why-choose">
                        <div class="uk-container uk-container-center">
                            <div class="background-overlay image img-cover">
                                <img src="{{ $system['background_1'] }}" alt="">
                            </div>
                            <div class="bl">
                                <div class="panel-head">
                                    <h2 class="heading-1">
                                        <span>{{ $slides[$slideKeyword]['name'] }}</span>
                                    </h2>
                                    <div class="description">{{ $slides[$slideKeyword]['short_code'] }}</div>
                                </div>
                                <div class="panel-body">
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($slides[$slideKeyword]['item'] as $key => $val )
                                                <div class="swiper-slide">
                                                    <div class="slide-item">
                                                        <a href="" class="image img-cover">
                                                            <img src="{{ $val['image'] }}" alt="">
                                                        </a>
                                                        <div class="name">
                                                            {{ $val['name'] }}
                                                        </div>
                                                        <div class="description">
                                                            {{ $val['alt'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="panel-register">
                    <div class="uk-grid uk-grid-collapse">
                        <div class="uk-width-medium-1-2">
                            <div class="register-frm">
                                <h2 class="heading-1"><span>Đăng ký</span></h2>
                                <div class="subtitle">Đăng ký ngay để nhận ưu đãi từ chúng tôi</div>
                                <form action="{{ route('customer.reg') }}" method="POST" class="rf">
                                    @csrf
                                    <input type="hidden" name="customer_catalogue_id" value="1">
                                    <div class="form-group">
                                        <label for="name">Họ và tên</label>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Họ và tên">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email của bạn</label>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email của bạn">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Mật khẩu</label>
                                        <input type="password" id="password" name="password" placeholder="Mật khẩu">
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
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="re_password">Xác nhận mật khẩu</label>
                                        <input type="password" id="re_password" name="re_password" placeholder="Xác nhận mật khẩu">
                                        <button type="button" class="password-toggle" onclick="togglePassword('re_password')">
                                            <svg class="eye-icon" id="eye-re_password" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg class="eye-icon" id="eye-slash-re_password" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                                            </svg>
                                        </button>
                                        @error('re_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <p class="condition">
                                        Bằng cách đăng ký, bạn đã đồng ý với 
                                        <a href="#" target="_blank" class="policies">điều khoản và chính sách</a> 
                                        của chúng tôi
                                    </p>
                                    <button type="submit" class="submit-btn">Đăng ký</button>
                                    <div class="login-link">
                                        Bạn đã có tài khoản ? <a href="#modal-login" id="loginLink" data-uk-modal>Đăng nhập</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="uk-width-medium-1-2">
                            <div class="image-content">
                                <div class="bg-overlay">
                                    <img src="{{ $system['background_3'] }}" alt="">
                                </div>
                                <div class="txt-overlay">
                                    <svg width="25" height="20" viewBox="0 0 25 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.6562 9.87292H18.75V7.05208C18.75 5.49622 20.1514 4.23125 21.875 4.23125H22.2656C22.915 4.23125 23.4375 3.75964 23.4375 3.17344V1.05781C23.4375 0.471608 22.915 0 22.2656 0H21.875C17.5586 0 14.0625 3.15581 14.0625 7.05208V17.6302C14.0625 18.7982 15.1123 19.7458 16.4062 19.7458H22.6562C23.9502 19.7458 25 18.7982 25 17.6302V11.9885C25 10.8205 23.9502 9.87292 22.6562 9.87292ZM8.59375 9.87292H4.6875V7.05208C4.6875 5.49622 6.08887 4.23125 7.8125 4.23125H8.20312C8.85254 4.23125 9.375 3.75964 9.375 3.17344V1.05781C9.375 0.471608 8.85254 0 8.20312 0H7.8125C3.49609 0 0 3.15581 0 7.05208V17.6302C0 18.7982 1.0498 19.7458 2.34375 19.7458H8.59375C9.8877 19.7458 10.9375 18.7982 10.9375 17.6302V11.9885C10.9375 10.8205 9.8877 9.87292 8.59375 9.87292Z" fill="white"/>
                                    </svg>
                                    <p>{{  $system['text_1']  }}</p>
                                </div>
                                <a href="" class="image img-cover">
                                    <img src="{{ $system['background_2'] }}" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
