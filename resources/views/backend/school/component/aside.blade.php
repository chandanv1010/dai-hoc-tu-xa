@include('backend.dashboard.component.publish', ['model' => ($school) ?? null, 'hideImage' => false])

<div class="ibox w">
    <div class="ibox-title">
        <h5>Ký hiệu trường</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Ký hiệu (VD: NEU, HOU, TNU, AOF)</label>
                    <input 
                        type="text" 
                        name="short_name" 
                        class="form-control" 
                        value="{{ old('short_name', ($school->short_name ?? '') ?? '') }}" 
                        placeholder="Nhập ký hiệu trường"
                        maxlength="50"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
    </div>
</div>

