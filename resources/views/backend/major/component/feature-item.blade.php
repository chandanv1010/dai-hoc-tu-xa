<div class="feature-item mb15" data-index="{{ $index }}">
    <div class="row">
        <div class="col-lg-5">
            <div class="form-row">
                <label class="control-label text-left">Tên tính năng</label>
                <input 
                    type="text"
                    name="feature[{{ $index }}][name]"
                    value="{{ old("feature.{$index}.name", is_array($feature) ? ($feature['name'] ?? '') : '') }}"
                    class="form-control"
                    placeholder="Nhập tên tính năng"
                    required
                >
            </div>
        </div>
        <div class="col-lg-5">
            <div class="form-row">
                <label class="control-label text-left">Hình ảnh</label>
                <input 
                    type="text"
                    name="feature[{{ $index }}][image]"
                    value="{{ old("feature.{$index}.image", is_array($feature) ? ($feature['image'] ?? '') : '') }}"
                    class="form-control upload-image"
                    placeholder="URL hình ảnh"
                >
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-row">
                <label class="control-label text-left">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm removeFeatureBtn" style="width: 100%;">
                    <i class="fa fa-trash"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>
