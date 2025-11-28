@include('backend.dashboard.component.breadcrumb', ['title' => ($config['method'] == 'create') ? 'Thêm mới Danh mục Ngành học' : 'Cập nhật Danh mục Ngành học'])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('major.catalogue.store') : route('major.catalogue.update', $majorCatalogue->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.content', ['model' => ($majorCatalogue) ?? null])
                    </div>
                </div>
               @include('backend.dashboard.component.album', ['model' => ($majorCatalogue) ?? null])
               @include('backend.dashboard.component.seo', ['model' => ($majorCatalogue) ?? null])
            </div>
            <div class="col-lg-3">
                <div class="text-right mb15 fixed-bottom">
                    @if($config['method'] == 'create')
                        @include('components.btn-create')
                    @else
                        @include('components.btn-update',['model' => $majorCatalogue ?? null])
                    @endif   
                </div>
                @include('backend.major.catalogue.component.aside')
            </div>
        </div>
    </div>
</form>

