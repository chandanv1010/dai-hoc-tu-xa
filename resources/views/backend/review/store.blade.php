@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('review.store') : route('review.update', $review->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Người đánh giá<span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="fullname"
                                        value="{{ old('fullname', ($review->fullname) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Số điện thoại</label>
                                    <input 
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone', ($review->phone) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Email</label>
                                    <input 
                                        type="text"
                                        name="email"
                                        value="{{ old('email', ($review->email) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <label for="" class="control-label text-left">Đánh giá sản phẩm</label>
                                        <a href="" class="multipleUploadImageCkeditor" data-target="ckContent">{{ __('messages.upload') }}</a>
                                    </div>
                                    <textarea
                                        name="description"
                                        class="form-control ck-editor"
                                        placeholder=""
                                        autocomplete="off"
                                        id="ckContent"
                                        data-height="300"
                                    >{{ old('description', ($model->description) ?? '' ) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox-w mb15">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left mb10">Chọn sản phẩm <span class="text-danger">(*)</span></label>
                                    <select name="reviewable_id" id="" class="form-control setupSelect2">
                                        <option value="0"> Chọn sản phẩm</option>
                                        @if(!empty($products))
                                            @foreach($products as $item)
                                                <option value="{{ $item->id }}">{{ $item->languages->first()->pivot->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="mb10">Đánh giá <span class="text-danger">(*)</span></label>
                                    <select name="score" id="" class="form-control setupSelect2">
                                        <option value="0">Chọn đánh giá</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="mb10">Giới tính <span class="text-danger">(*)</span></label>
                                    <select name="gender" id="" class="form-control setupSelect2">
                                        <option value="0">Chọn giới tính</option>
                                        <option value="Nam">Nam</option>
                                        <option value="Nữ">Nữ</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox w">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left mb10">Chọn ảnh</label>
                                    <span 
                                        class="image img-cover image-target" 
                                        style="
                                            height:250px;
                                            padding: 20px;
                                            text-align: center;
                                            border: 1px dashed #b8b2b2;
                                            display:flex;
                                            align-items: center;
                                            justify-content:center;
                                        "
                                    >
                                        <img src="{{ (old('image', ($review->image) ?? '' ) ? old('image', ($review->image) ?? '')   :  'backend/img/image.svg') }}" alt="" style="width:110px;height:110px;">
                                    </span>
                                    <input type="hidden" name="image" value="{{ old('image', ($review->image) ?? '' ) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="reviewable_type" value="App\Models\Product">
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>