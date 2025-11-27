<div class="ibox">
    <div class="ibox-title">
        <h5>Cài đặt thông tin chi tiết khuyến mãi</h5>
    </div>
    <div class="ibox-content">
        <div class="form-row">
            <div class="fix-label" for="">Chọn hình thức khuyến mãi</div>
            <select name="method" class="setupSelect2 promotionMethod" id="" 
                {{ $config['method'] == 'edit' ? 'disabled' : '' }}>
                <option value="none">Chọn hình thức</option>
                @foreach(__('module.promotion') as $key => $val)
                    <option value="{{ $key }}"> {{ $val }} </option>
                @endforeach
            </select>
            @if($config['method'] == 'edit')
                <input type="hidden" name="method" value="{{ $promotion->method }}">
            @endif
        </div>
        <div class="promotion-container">
            
        </div>
    </div>
</div>