@include('backend.dashboard.component.breadcrumb', ['title' => 'Xóa Ngành học'])
@include('backend.dashboard.component.formError')
<form action="{{ route('major.destroy', $major->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin Ngành học</div>
                </div>
                <div class="panel-body">
                    <div class="form-row">
                        <label class="control-label text-left">Tên chuyên ngành</label>
                        <input type="text" value="{{ $major->name }}" class="form-control" disabled>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right">
            <button type="submit" class="btn btn-danger" name="send" value="send">Xóa dữ liệu</button>
        </div>
    </div>
</form>

