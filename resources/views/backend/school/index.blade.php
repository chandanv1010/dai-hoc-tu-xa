@include('backend.dashboard.component.breadcrumb', ['title' => 'Quản lý Trường học'])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Danh sách Trường học</h5>
                @include('backend.dashboard.component.toolbox', ['model' => 'School'])
            </div>
            <div class="ibox-content">
                @include('backend.school.component.filter')
                @include('backend.school.component.table')
            </div>
        </div>
    </div>
</div>

