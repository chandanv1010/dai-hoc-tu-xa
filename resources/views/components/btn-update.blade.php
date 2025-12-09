<button class="btn btn-primary mr10" type="submit" name="send" value="send_and_stay">{{ __('messages.save') }}</button>
@php
    // Lấy canonical từ pivot nếu có, nếu không thì lấy từ model
    $canonical = null;
    if (isset($model) && $model->languages && $model->languages->count() > 0) {
        $pivot = $model->languages->first()->pivot;
        $canonical = $pivot->canonical ?? ($model->canonical ?? null);
    } else {
        $canonical = $model->canonical ?? null;
    }
@endphp
@if($canonical)
    <a class="btn btn-danger mr10" href="{{ write_url($canonical) }}" style="color:#fff;" target="_blank">Xem</a>
@endif
<button class="btn btn-success" type="submit" name="send" value="send_and_exit">Đóng</button>