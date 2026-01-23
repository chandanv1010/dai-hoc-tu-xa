<!DOCTYPE html>
<html lang="vi">
    <head>
        <base href="{{ config('app.url') }}">
        {!! $system['script_1'] !!}
        @include('frontend.component.head')
    </head>
    <body>
        @include('frontend.component.header')
        @yield('content')
        @include('frontend.component.footer')
        @include('frontend.component.fixed-buttons')
        @include('frontend.component.tai_lo_trinh_hoc_form')
        @include('frontend.component.consultation-modal')
        @include('frontend.component.script')
        {!! $system['script_2'] !!}
    </body>
</html>