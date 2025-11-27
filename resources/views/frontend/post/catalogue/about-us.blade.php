@extends('frontend.homepage.layout')
@section('content')
    <div class="post-catalogue page-wrapper intro-wrapper">
        @include('frontend.component.breadcrumb', ['model' => $postCatalogue, 'breadcrumb' => $breadcrumb])
        <div class="product-catalogue-wrapper">
            <div class="uk-container uk-container-center">
                <h1 class="page-heading">{{ $postCatalogue->languages->first()->pivot->name }}</h1>
            </div>
        </div>
        <div class="panel-body">
            <div class="uk-container uk-container-center" style="padding-top:30px;padding-bottom:30px;">
                <div class="post-detail-container about-us">
                    <div class="post-content">
                        <div class="description">
                            {!! $postCatalogue->languages->first()->pivot->description !!}
                        </div>
                        <div class="content">
                            {!! $postCatalogue->languages->first()->pivot->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
