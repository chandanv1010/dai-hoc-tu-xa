<div class="career-tag-item mb15" data-tag-index="{{ $tagIndex }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row mb15">
            <div class="col-lg-4">
                <div class="form-row">
                    <label class="control-label text-left">Icon</label>
                    <input 
                        type="text"
                        name="chance[tags][{{ $tagIndex }}][icon]"
                        value="{{ old("chance.tags.{$tagIndex}.icon", (is_array($tag) ? ($tag['icon'] ?? '') : ($tag->icon ?? ''))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn icon"
                        readonly
                    >
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-row">
                    <label class="control-label text-left">Tên</label>
                    <input 
                        type="text"
                        name="chance[tags][{{ $tagIndex }}][name]"
                        value="{{ old("chance.tags.{$tagIndex}.name", (is_array($tag) ? ($tag['name'] ?? '') : ($tag->name ?? ($tag->text ?? '')))) }}"
                        class="form-control"
                        placeholder="Nhập tên"
                    >
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-row">
                    <label class="control-label text-left">Màu</label>
                    <input 
                        type="color"
                        name="chance[tags][{{ $tagIndex }}][color]"
                        value="{{ old("chance.tags.{$tagIndex}.color", (is_array($tag) ? ($tag['color'] ?? '#000000') : ($tag->color ?? '#000000'))) }}"
                        class="form-control"
                        style="height: 38px;"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeCareerTagBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

