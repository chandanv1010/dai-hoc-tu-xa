<div class="reception-place-item mb15" data-index="{{ $index }}">
    <div class="row mb10">
        <div class="col-lg-12">
            <div class="form-row">
                <input 
                    type="text"
                    name="address[{{ $index }}][name]"
                    value="{{ old("address.{$index}.name", (is_array($place) ? ($place['name'] ?? '') : ($place->name ?? ''))) }}"
                    class="form-control"
                    placeholder="Nhập nơi nhận"
                >
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10">
            <div class="form-row">
                <input 
                    type="text"
                    name="address[{{ $index }}][address]"
                    value="{{ old("address.{$index}.address", (is_array($place) ? ($place['address'] ?? '') : ($place->address ?? ''))) }}"
                    class="form-control"
                    placeholder="Nhập địa chỉ"
                >
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-row">
                <button type="button" class="btn btn-danger btn-sm removeReceptionPlaceBtn" style="width: 100%;">
                    <i class="fa fa-trash"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

