@extends('frontend.homepage.layout')
@section('content')
    <!-- Thank You Page -->
    <div class="panel-thank-you">
        <div class="uk-container uk-container-center">
            <div class="thank-you-wrapper">
                <!-- Logo Section -->
                @if($schools && $schools->count() > 0)
                    <div class="thank-you-schools-logo">
                        <div class="schools-logo-grid">
                            @foreach($schools as $school)
                                @php
                                    $schoolLanguage = $school->languages->first();
                                    $schoolImage = $school->image ?? '';
                                    $schoolName = $schoolLanguage && $schoolLanguage->pivot ? ($schoolLanguage->pivot->name ?? '') : '';
                                @endphp
                                @if($schoolImage)
                                    <div class="school-logo-item">
                                        <img src="{{ image($schoolImage) }}" alt="{{ $schoolName }}" class="school-logo-img">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Success Icon -->
                <div class="thank-you-icon">
                    <div class="success-icon-circle">
                        <i class="fa fa-check"></i>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="thank-you-title">🎉 ĐĂNG KÝ THÀNH CÔNG</h1>

                <!-- Content -->
                <div class="thank-you-content">
                    <p class="thank-you-message">
                        Cảm ơn bạn đã để lại thông tin liên hệ. Thông tin của Bạn được bảo mật tuyệt đối.
                    </p>
                    <p class="thank-you-notice">
                        Cán bộ tư vấn nhà trường sẽ liên hệ với mình trong 24h tới. Bạn vui lòng để ý điện thoại và mở chặn zalo người lạ để không bỏ lỡ thông tin quan trọng về hồ sơ, chuyên ngành và học phí từ cán bộ tư vấn nhé!
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="thank-you-actions">
                    <a href="{{ route('home.index') }}" class="btn-back-home">
                        <i class="fa fa-home"></i>
                        Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

