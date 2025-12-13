<div class="what-learn-category mb15" data-category-index="{{ $categoryIndex }}">
    <div class="ibox" style="background: #f0f0f0; padding: 15px; border-radius: 5px; border: 2px solid #ddd;">
        <div class="row mb15">
            <div class="col-lg-11">
                <div class="form-row">
                    <label class="control-label text-left">Tiêu đề mục</label>
                    <input 
                        type="text"
                        name="learn[items][{{ $categoryIndex }}][name]"
                        value="{{ old("learn.items.{$categoryIndex}.name", (is_array($category) ? ($category['name'] ?? '') : ($category->name ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập tên mục"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeWhatLearnCategoryBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <h6>
                        Danh sách bài 
                        <a href="javascript:void(0)" class="addWhatLearnItemBtn" data-category-index="{{ $categoryIndex }}" style="margin-left: 10px;"><i class="fa fa-plus"></i> Thêm bài</a>
                        <a href="javascript:void(0)" class="toggle-learn-items-btn" data-category-index="{{ $categoryIndex }}" style="margin-left: 10px; color: #1ab394;">
                            <i class="fa fa-chevron-down"></i> <span class="toggle-text">Thu gọn</span>
                        </a>
                    </h6>
                </div>
            </div>
        </div>
        <div class="what-learn-items-container sortable-items" data-category-index="{{ $categoryIndex }}" style="overflow: visible; max-height: none;">
            @if(old("learn.items.{$categoryIndex}.items"))
                @foreach(old("learn.items.{$categoryIndex}.items") as $itemIndex => $item)
                    @include('backend.major.component.what-learn-item', ['categoryIndex' => $categoryIndex, 'itemIndex' => $itemIndex, 'item' => $item])
                @endforeach
            @elseif(isset($category) && is_array($category) && isset($category['items']) && is_array($category['items']) && count($category['items']) > 0)
                @foreach($category['items'] as $itemIndex => $item)
                    @include('backend.major.component.what-learn-item', ['categoryIndex' => $categoryIndex, 'itemIndex' => $itemIndex, 'item' => $item])
                @endforeach
            @endif
        </div>
    </div>
</div>

