<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1,user-scalable=0">
<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
<meta name="robots" content="index,follow">
<meta name="author" content="{{ $system['homepage_company'] }}">
<meta name="copyright" content="{{ $system['homepage_company'] }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ $system['homepage_favicon'] }}" type="image/png" sizes="30x30">
<!-- GOOGLE -->
<title>{{ $seo['meta_title'] }}</title>
<meta name="description" content="{!! $seo['meta_description'] !!}">
<meta name="keyword" content="{{ $seo['meta_keyword'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta property="og:locale" content="vi_VN">
<!-- for Facebook -->
<meta property="og:title" content="{{ $seo['meta_title'] }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ asset($seo['meta_image']) }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<meta property="og:description" content="{!! $seo['meta_description'] !!}">
<meta property="og:site_name" content="">
<meta property="fb:admins" content="">
<meta property="fb:app_id" content="">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $seo['meta_title'] }}">
<meta name="twitter:description" content="{!! $seo['meta_description'] !!}">
<meta name="twitter:image" content="{{ asset($seo['meta_image']) }}">

{{-- Resource Hints for faster loading --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://unpkg.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://www.google-analytics.com">

{{-- Preload LCP image for homepage --}}
@if(isset($ishome) && $ishome && isset($slides[App\Enums\SlideEnum::MAIN]['item'][0]['image']))
    <link rel="preload" as="image" href="{{ $slides[App\Enums\SlideEnum::MAIN]['item'][0]['image'] }}" fetchpriority="high">
@endif

{{-- Google Fonts with display=swap for better performance --}}
<link href="https://fonts.googleapis.com/css2?family=Asap:ital,wght@0,100..900;1,100..900&family=Questrial&family=Quicksand:wght@300..700&family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://fonts.googleapis.com/css2?family=Asap:ital,wght@0,100..900;1,100..900&family=Questrial&family=Quicksand:wght@300..700&family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet"></noscript>

{{-- <meta name="p:domain_verify" content="bbf6b87e5e83b6aa8d4bc6dab42cba0a"/> --}}

{{-- All CSS files are now imported via Vite in app.scss --}}
{{-- jQuery with defer to prevent render blocking - moved to bottom or use defer --}}
<script src="{{ asset('frontend/resources/library/js/jquery.js') }}"></script>
@vite(['resources/css/app.scss', 'resources/js/app.js'])

{{-- FAQ Schema JSON-LD (for Post, Major, and School detail pages) --}}
@if(isset($faqSchema) && !empty($faqSchema))
    <script type="application/ld+json">
    {!! $faqSchema !!}
    </script>
@endif