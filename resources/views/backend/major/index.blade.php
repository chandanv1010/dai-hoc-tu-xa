@include('backend.dashboard.component.breadcrumb', ['title' => 'Quản lý Ngành học'])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Danh sách Ngành học</h5>
                @include('backend.dashboard.component.toolbox', ['model' => 'Major'])
            </div>
            <div class="ibox-content">
                @include('backend.major.component.filter')
                @include('backend.major.component.table')
            </div>
        </div>
    </div>
</div>

