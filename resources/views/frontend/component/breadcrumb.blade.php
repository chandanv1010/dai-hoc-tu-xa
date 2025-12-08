@php
    $modelName = $model->languages->first()->pivot->name;
@endphp
<div class="page-breadcrumb background">      
    <div class="uk-container uk-container-center">
        <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
            <li>
                <a href="/">{{ __('frontend.home') }}</a>
            </li>
            <li>
                <span class="slash">/</span>
            </li>
            @if(!is_null($breadcrumb))
                @foreach($breadcrumb as $key => $val)
                    @php
                        $name = $val->languages->first()->pivot->name;
                        $canonical = write_url($val->languages->first()->pivot->canonical, true, true);
                    @endphp
                    <li>
                        <span class="slash">/</span>
                        <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
                    </li>
                @endforeach
            @endif
            @if(isset($model) && !is_null($model))
                <li>
                    <span class="slash">/</span>
                    <span>{{ $modelName }}</span>
                </li>
            @endif
        </ul>
    </div>
</div>