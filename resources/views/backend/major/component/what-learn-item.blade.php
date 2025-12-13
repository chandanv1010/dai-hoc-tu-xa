<div class="what-learn-item mb10" data-category-index="{{ $categoryIndex }}" data-item-index="{{ $itemIndex }}" style="cursor: move;">
    <div class="ibox" style="background: #f9f9f9; padding: 10px; border-radius: 3px; margin-left: 20px; position: relative;">
        <div class="row mb10" style="cursor: pointer;" onclick="toggleLearnItemContent(this)">
            <div class="col-lg-11">
                <div class="form-row">
                    <label class="control-label text-left">
                        <i class="fa fa-grip-vertical" style="color: #999; margin-right: 5px;"></i>
                        <i class="fa fa-chevron-down toggle-item-icon" style="color: #1ab394; margin-right: 5px;"></i>
                        Tên
                    </label>
                    <input 
                        type="text"
                        name="learn[items][{{ $categoryIndex }}][items][{{ $itemIndex }}][name]"
                        value="{{ old("learn.items.{$categoryIndex}.items.{$itemIndex}.name", (is_array($item) ? ($item['name'] ?? '') : ($item->name ?? ''))) }}"
                        class="form-control learn-item-name"
                        placeholder="Nhập tên"
                        onclick="event.stopPropagation();"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeWhatLearnItemBtn" style="width: 100%;" onclick="event.stopPropagation();">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="learn-item-content">
            <div class="row mb10">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label text-left">Hình ảnh</label>
                        <input 
                            type="text"
                            name="learn[items][{{ $categoryIndex }}][items][{{ $itemIndex }}][image]"
                            value="{{ old("learn.items.{$categoryIndex}.items.{$itemIndex}.image", (is_array($item) ? ($item['image'] ?? '') : ($item->image ?? ''))) }}"
                            class="form-control upload-image"
                            placeholder="Chọn ảnh"
                            readonly
                        >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label text-left">Mô tả</label>
                        <textarea 
                            name="learn[items][{{ $categoryIndex }}][items][{{ $itemIndex }}][description]"
                            class="form-control"
                            rows="3"
                            placeholder="Nhập mô tả"
                        >{{ old("learn.items.{$categoryIndex}.items.{$itemIndex}.description", (is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''))) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

