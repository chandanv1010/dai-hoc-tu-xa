<!DOCTYPE html>
<html lang="en">
    <head>
        {!! $system['script_1'] !!}
        @include('frontend.component.head')
    </head>
    @if(isset($schema))
        {!! $schema !!}
    @endif
    <body>
        @include('frontend.component.header')
        @yield('content')
        @include('frontend.component.footer')
        @include('frontend.component.fixed-buttons')
        @include('frontend.component.script')
        {!! $system['script_2'] !!}
    </body>
</html>