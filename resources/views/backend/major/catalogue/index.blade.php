
@include('backend.dashboard.component.breadcrumb', ['title' => 'Danh mục Ngành học'])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Danh sách Danh mục Ngành học</h5>
                @include('backend.dashboard.component.toolbox', ['model' => 'MajorCatalogue'])
            </div>
            <div class="ibox-content">
                @include('backend.major.catalogue.component.filter')
                @include('backend.major.catalogue.component.table')
            </div>
        </div>
    </div>
</div>

