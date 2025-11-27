@extends('frontend.homepage.layout')
@section('content')
    <div class="background">
        <img src="{{ $postCatalogue->image }}" alt="{{ $postCatalogue->name }}">
    </div>
    <div class="catalogue-container">
        <div class="uk-container uk-container-center">
         <h1 class="title mb30 uk-text-center"><span>{{ $postCatalogue->name }}</span></h1>
            <div class="panel-body">
                @if(count($posts))
                <div class="catalogue-slide page-setup">
                    <div class="swiper-container">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-wrapper">
                            @foreach($posts as $key => $val )
                            @php
                                $canonical = $val->short_name;
                                $image = $val->image;
                            @endphp
                            <div class="swiper-slide">
                                <div class="slide-item catalogue-item">
                                    <span  class="image img-cover"><img src="{{ $image }}" alt="{{ $image }}"></span>
                                    <a href="{{ $canonical }}" class="view-pdf" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 110 110">
                                        <path fill="currentColor" d="M56.914,69.734 56.914,62.525 49.417,62.525 49.417,69.734 45.464,69.734 53.117,79.239 
                                        60.771,69.734z"></path>
                                        <path fill="currentColor" d="M32.222,39.849c-0.426,0-0.715,0.041-0.866,0.084v2.734c0.179,0.041,0.398,0.055,0.701,0.055
                                        c1.113,0,1.8-0.563,1.8-1.512C33.857,40.357,33.267,39.849,32.222,39.849z"></path>
                                        <path fill="currentColor" d="M43.233,42.818c0.014-1.925-1.113-2.942-2.914-2.942c-0.467,0-0.77,0.042-0.948,0.082v6.063
                                        c0.179,0.041,0.468,0.041,0.729,0.041C41.997,46.076,43.233,45.031,43.233,42.818z"></path>
                                        <path fill="currentColor" d="M61.49,17.65H36.149c-3.739,0-6.781,3.042-6.781,6.781v10.632C26.909,35.377,25,37.456,25,40v6.5
                                        c0,2.544,1.908,4.622,4.368,4.937v18.682c0,3.738,3.042,6.78,6.781,6.78h11.237l-4.832-6h-6.406c-0.423,0-0.781-0.357-0.781-0.78
                                        V51.5h15.715c2.762,0,5-2.239,5-5V40c0-2.761-2.238-5-5-5H35.368V24.431c0-0.423,0.357-0.781,0.781-0.781h22.837l0.834,0.843v6.935
                                        c0,2.079,1.701,3.781,3.781,3.781h6.817l0.788,0.797v34.112c0,0.423-0.357,0.78-0.781,0.78h-6.745l-4.832,6h11.577
                                        c3.739,0,6.781-3.042,6.781-6.78V33.54L61.49,17.65z M46.837,38.337H52.5v1.719h-3.56v2.115h3.328v1.705H48.94v3.726h-2.103V38.337
                                        z M37.268,38.46c0.77-0.123,1.773-0.191,2.832-0.191c1.759,0,2.899,0.316,3.793,0.99c0.962,0.714,1.566,1.855,1.566,3.49
                                        c0,1.773-0.646,2.997-1.539,3.754c-0.976,0.811-2.46,1.195-4.275,1.195c-1.086,0-1.855-0.069-2.378-0.137V38.46z M35.933,41.155
                                        c0,0.906-0.303,1.676-0.852,2.199c-0.715,0.674-1.773,0.975-3.01,0.975c-0.275,0-0.522-0.013-0.715-0.04v3.313H29.28V38.46
                                        c0.646-0.109,1.553-0.191,2.832-0.191c1.292,0,2.213,0.248,2.832,0.742C35.534,39.479,35.933,40.248,35.933,41.155z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
