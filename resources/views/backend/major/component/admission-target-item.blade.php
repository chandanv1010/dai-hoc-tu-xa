<div class="admission-target-item mb15" data-index="{{ $index }}">
    <div class="row">
        <div class="col-lg-10">
            <div class="form-row">
                <input 
                    type="text"
                    name="target[]"
                    value="{{ old("target.{$index}", is_string($target) ? $target : ($target ?? '')) }}"
                    class="form-control"
                    placeholder="Nhập đối tượng tuyển sinh"
                >
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-row">
                <button type="button" class="btn btn-danger btn-sm removeAdmissionTargetBtn" style="width: 100%;">
                    <i class="fa fa-trash"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

