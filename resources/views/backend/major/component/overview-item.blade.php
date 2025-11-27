<div class="overview-item mb15" data-index="{{ $index }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Ảnh</label>
                    <input 
                        type="text"
                        name="overview[items][{{ $index }}][image]"
                        value="{{ old("overview.items.{$index}.image", (is_array($item) ? ($item['image'] ?? '') : ($item->image ?? ''))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn ảnh"
                        readonly
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Tên</label>
                    <input 
                        type="text"
                        name="overview[items][{{ $index }}][name]"
                        value="{{ old("overview.items.{$index}.name", (is_array($item) ? ($item['name'] ?? '') : ($item->name ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập tên"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-11">
                <div class="form-row">
                    <label class="control-label text-left">Mô tả</label>
                    <textarea 
                        name="overview[items][{{ $index }}][description]"
                        class="form-control"
                        rows="3"
                        placeholder="Nhập mô tả"
                    >{{ old("overview.items.{$index}.description", (is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''))) }}</textarea>
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeOverviewItemBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

