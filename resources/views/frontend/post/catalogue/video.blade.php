@extends('frontend.homepage.layout')
@section('content')
    @php
        $slideKeyword4 = 'background-banner';
    @endphp

    <div class="post-catalogue video">
        @if (!empty($postCatalogue->image))
            <div class="project-banner">
                <img src="{{ image($postCatalogue->image) }}" alt="">
                @include('frontend.component.breadcrumb', [
                    'model' => $postCatalogue,
                    'breadcrumb' => $breadcrumb,
                ])
            </div>
        @endif

        <div class="uk-container uk-container-center">
            <div class="project-container">
                <h1 class="heading-6"><span>{{ $postCatalogue->name }}</span></h1>
                <div class="description">
                    {!! $postCatalogue->description !!}
                </div>
                <div class="project-list">
                    @if (!is_null($posts))
                        <div class="uk-grid uk-grid-medium">
                            @foreach ($posts as $key => $post)
                                @php
                                    $name = $post->languages->first()->pivot->name;
                                    $description = $post->languages->first()->pivot->description;
                                    $image = image($post->image);
                                    $canonical = write_url($post->languages->first()->pivot->canonical);
                                @endphp
                                <div class="uk-width-small-1-2 uk-width-1-2 uk-width-medium-1-2 uk-width-large-1-3 project-item">
                                    <div class="project-item-1">
                                        <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}"
                                                alt="{{ $name }}"></a>
                                        <div class="video-info">
                                            <h3 class="video-title"><a href="{{ $canonical }}"
                                                    title="{{ $name }}">{{ $name }}</a></h3>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @include('frontend.component.pagination', ['model' => $posts])
                </div>
            </div>
        </div>

        <div class="certificate-section">
            @if (isset($slides[$slideKeyword4]))
                <img src="{{ $slides[$slideKeyword4]['item'][0]['image'] }}" alt="">
            @endif
            <div class="form-content uk-container uk-container-center uk-visible-large">

                <div class="uk-container uk-container-center">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-medium-2-3">
                            <div class="form-content-left">
                                <h3>Phân phối toàn quốc</h3>
                                <div class="form-description">
                                    <p>
                                        Văn phòng Hà Nội: Tầng 28, Tòa nhà Sông Đà, Số 110 Trần Phú, Hà Đông
                                    <p class="hotline">Hotline: 84-24-3823-5588</p>
                                    </p>
                                    <p>
                                        Văn phòng Hà Nội: Tầng 28, Tòa nhà Sông Đà, Số 110 Trần Phú, Hà Đông
                                    <p class="hotline">Hotline: 84-24-3823-5588</p>
                                    </p>
                                    <p>
                                        Văn phòng Hà Nội: Tầng 28, Tòa nhà Sông Đà, Số 110 Trần Phú, Hà Đông
                                    <p class="hotline">Hotline: 84-24-3823-5588</p>
                                    </p>
                                </div>
                            </div>
                           
                        </div>
                        <div class="uk-width-medium-1-3">
                             <div class="form-content-right">
                                <form class="uk-flex uk-flex-midde uk-flex-column">
                                    <div class="form-input">
                                        <input type="text" required name="name" placeholder="Họ và tên....">
                                    </div>
                                    <div class="form-input">
                                        <input type="email" required name="email" placeholder="Email....">
                                    </div>
                                    <div class="form-input">
                                        <input type="text" required name="phone" placeholder="Số điện thoại....">
                                    </div>
                                    <div class="form-input">
                                        <textarea name="" id="" cols="30" rows="10" placeholder="Ghi chú.."></textarea>
                                    </div>
                                    <div class="btn">
                                        <button class="submit">Gửi Thông Tin</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
