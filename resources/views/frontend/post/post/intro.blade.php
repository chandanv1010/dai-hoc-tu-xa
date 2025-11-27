@extends('frontend.homepage.layout')
@section('content')
    @php
        $album = json_decode($post->album);
    @endphp
    <div class="intro-banner">
        <img src="{{ $album[0] }}" alt="">
    </div>

    @if(isset($widgets['intro']->object) && count($widgets['intro']->object))
       @foreach($widgets['intro']->object as $key => $val)
       @php
           $name = $val->languages->first()->pivot->name;
           $canonical = write_url($val->languages->first()->pivot->canonical);
           $description = $val->languages->first()->pivot->description;
           $content = $val->languages->first()->pivot->content;
           $image = $val->image;
       @endphp
       <div class="panel-intro page page-intro-container">
           <div class="uk-container uk-container-center">
               <div class="uk-grid uk-grid-large">
                   <div class="uk-width-large-3-5">
                       <div class="panel-body">
                           <div class="uk-flex uk-flex-middle uk-flex-space-between mb30">
                               <div>
                                   <h3 class="title">{{ $name }}</h3>
                                   <div class="small-title">ABOUT COMPANY</div>
                               </div>
                           </div>
                           <div class="description">
                               {!! $description !!}
                               {!! $content !!}
                           </div>
                       </div>
                   </div>
                   <div class="uk-width-large-2-5">
                       <div class="panel-head">
                           <span class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></span>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       @endforeach
   @endif

   @if(isset($widgets['product-advantage']->object) && count($widgets['product-advantage']->object))
   @foreach($widgets['product-advantage']->object as $key => $val)
   @php
       $name = $val->languages->first()->pivot->name;
       $canonical = write_url($val->languages->first()->pivot->canonical);
       $description = $val->languages->first()->pivot->description;
       $content = $val->languages->first()->pivot->content;
       $image = $val->image;
   @endphp
   <div class="panel-intro product-advantage page page-intro-container">
       <div class="uk-container uk-container-center">
           <div class="uk-grid uk-grid-large">
               <div class="uk-width-large-1-2">
                    <div class="panel-head">
                        <span class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></span>
                    </div>
                   
               </div>
               <div class="uk-width-large-1-2">
                    <div class="panel-body">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between mb30">
                            <div>
                                <h3 class="title">{{ $name }}</h3>
                                <div class="small-title">Ưu điểm của sản phẩm</div>
                            </div>
                        </div>
                        <div class="description">
                            {!! $description !!}
                            {!! $content !!}
                        </div>
                    </div>
               </div>
           </div>
       </div>
    </div>
    @endforeach
    @endif

    @if(isset($widgets['target']->object) && count($widgets['target']->object))
       @foreach($widgets['target']->object as $key => $val)
       @php
           $name = $val->languages->first()->pivot->name;
           $canonical = write_url($val->languages->first()->pivot->canonical);
           $description = $val->languages->first()->pivot->description;
           $content = $val->languages->first()->pivot->content;
           $image = $val->image;
       @endphp
       <div class="panel-intro page page-intro-container">
           <div class="uk-container uk-container-center">
               <div class="uk-grid uk-grid-large">
                   <div class="uk-width-large-3-5">
                       <div class="panel-body">
                           <div class="uk-flex uk-flex-middle uk-flex-space-between mb30">
                               <div>
                                   <h3 class="title">{{ $name }}</h3>
                                   <div class="small-title">ABOUT COMPANY</div>
                               </div>
                           </div>
                           <div class="description">
                               {!! $description !!}
                               {!! $content !!}
                           </div>
                       </div>
                   </div>
                   <div class="uk-width-large-2-5">
                       <div class="panel-head">
                           <span class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></span>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       @endforeach
   @endif
@endsection