@include('backend.dashboard.component.breadcrumb', ['title' => 'Xóa Trường học'])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Xóa Trường học</h5>
            </div>
            <div class="ibox-content">
                <form action="{{ route('school.destroy', $school->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="alert alert-danger">
                        <strong>Bạn có chắc chắn muốn xóa trường học này?</strong>
                        <p>Tên trường: <strong>{{ $school->languages->first()->pivot->name ?? 'N/A' }}</strong></p>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                        <a href="{{ route('school.index') }}" class="btn btn-default">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

