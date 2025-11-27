<div class="degree-value-item mb15" data-item-index="{{ $itemIndex }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row">
            <div class="col-lg-5">
                <div class="form-row">
                    <label class="control-label text-left">Icon</label>
                    <input 
                        type="text"
                        name="value[items][{{ $itemIndex }}][icon]"
                        value="{{ old("value.items.{$itemIndex}.icon", (is_array($item) ? ($item['icon'] ?? '') : ($item->icon ?? ''))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn icon"
                        readonly
                    >
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-row">
                    <label class="control-label text-left">Tên giá trị</label>
                    <input 
                        type="text"
                        name="value[items][{{ $itemIndex }}][name]"
                        value="{{ old("value.items.{$itemIndex}.name", (is_array($item) ? ($item['name'] ?? '') : ($item->name ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập tên giá trị"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeDegreeValueItemBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

