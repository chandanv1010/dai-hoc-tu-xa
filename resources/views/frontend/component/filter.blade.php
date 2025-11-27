<div class="browse-filters">
    <div class="filters-title">
        <h3 class="heading-2"><span>Lọc khóa học</span></h3>
    </div>
    <div class="filters-category">
        <div class="bucket">
            <div class="filter-item">
                @if(!is_null($descendantTrees))
                    <div class="filter-item__title">Loại khóa học <span class="count"></span></div>
                    <ul class="filter-list lv1">
                        @foreach($descendantTrees as $key => $descendantTree)
                        @php
                            $url = write_url($descendantTree['item']->languages->first()->pivot->canonical);
                        @endphp
                            <li class="filter-list__item filter-group">
                                <div class="uk-flex uk-flex-middle uk-flex-space-between mb10">
                                    <div class="lft">
                                        <input 
                                            data-canonical="{{ $url }}" 
                                            id="product-catalogue-{{ $descendantTree['item']->id }}" 
                                            type="checkbox" class="input-value p-filter" name="product_catalogue_id[]" 
                                            value="{{ $descendantTree['item']->id }}
                                            @checked(isset($productCatalogue) && $descendantTree['item']->id === $productCatalogue->id)
                                            
                                        ">
                                        <label for="product-catalogue-{{ $descendantTree['item']->id }}" style="color:#555555;">
                                            <i class="fa"></i>
                                            {{ $descendantTree['item']->languages->first()->pivot->name  }}
                                        </label>
                                    </div>
                                    <div class="rgt">
                                        <span class="count">({{ $descendantTree['item']['total_product_count'] }})</span>
                                        @if(!empty($descendantTree['children']))
                                            <button class="toggle" aria-label="Chuyển đổi"><i class="fa fa-angle-down"></i></button>
                                        @endif
                                    </div>
                                </div>
                                <ul class="filter-list lv2 children {{ $key == 0 ? 'active' : '' }}">
                                    @if(!empty($descendantTree['children']))
                                        @foreach($descendantTree['children'] as $catP)
                                            @php
                                                $cat_id = $catP['item']->id;
                                                $cat_name = $catP['item']->languages->first()->pivot->name;
                                                $cat_canonical = write_url($catP['item']->languages->first()->pivot->canonical);
                                            @endphp
                                            <li class="filter-list__item">
                                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                    <div class="lft">
                                                        <input 
                                                            id="product-catalogue-{{ $cat_id }}" 
                                                            type="checkbox" 
                                                            data-canonical="{{ $cat_canonical }}" 
                                                            class="input-value p-filter" 
                                                            name="product_catalogue_id[]" 
                                                            value="{{ $cat_id }}"
                                                            @checked(isset($productCatalogue) &&  $cat_id === $productCatalogue->id)
                                                        >
                                                        <label for="product-catalogue-{{ $cat_id }}">
                                                            <i class="fa"></i>
                                                            {{ $cat_name }}
                                                        </label>
                                                    </div>
                                                    <div class="rgt">
                                                        <span class="count">({{ $catP['item']['total_product_count']}})</span>
                                                        @if(!empty($catP['children']))
                                                            <button class="toggle" aria-label="Chuyển đổi"><i class="fa fa-angle-down"></i></button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!empty($catP['children']))
                                                    <ul class="children">
                                                        @foreach($catP['children'] as $k => $v)
                                                            @php
                                                                $id = $v['item']->id;
                                                                $name = $v['item']->languages->first()->pivot->name;
                                                                $canonical = write_url($v['item']->languages->first()->pivot->name);
                                                            @endphp
                                                            <li class="cat-item">
                                                                <div class="uk-flex uk-flex-middle">
                                                                    <input 
                                                                        id="product-catalogue-{{ $id }}" 
                                                                        type="checkbox" 
                                                                        data-canonical="{{ $canonical }}" 
                                                                        class="input-value p-filter" 
                                                                        name="product_catalogue_id[]" 
                                                                        value="{{ $id }}"
                                                                        @checked(isset($productCatalogue) &&  $id === $productCatalogue->id)
                                                                    >
                                                                    <label for="product-catalogue-{{ $id }}">
                                                                        <i class="fa"></i>
                                                                        {{ $name }}
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="bucket mb20 {{ (isset($hiddenLecture) && $hiddenLecture === true) ? 'uk-hidden' : '' }}">
            <div class="filter-item">
                <div class="filter-item__title">Giảng viên</div>
                <div class="filter-item__content filter-group">
                    <ul class="filter-list">
                        @if(!is_null($lecturers))
                            @foreach($lecturers as $item)
                                @php
                                    $id = $item->id;
                                    $name = $item->name;
                                @endphp
                                    <li class="filter-list__item">
                                    <div class="uk-flex uk-flex-middle">
                                        <input id="lecture-{{ $item->id }}" {{ (isset($lecturer) && $lecturer->id === $item->id) ? 'checked' : '' }} type="checkbox" class="input-value p-filter" name="lecture_id[]" value="{{ $item->id }}">
                                        <label for="lecture-{{ $item->id }}">
                                            <i class="fa"></i>
                                            {{ $name }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="filter-item filter-price slider-box">
            <div class="filter-heading" for="priceRange">Giá khóa học:</div>
            <div class="filter-price-content">
                <input type="text" id="priceRange" readonly="" class="uk-hidden">
                <div id="price-range" class="slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
                    <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div>
                        <span class="ui-slider-handle ui-state-default ui-corner-all start" tabindex="0" style="left: 0%;"></span>
                        <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 100%;"></span>
                    </div>
            </div>
            <div class="filter-input-value mt5">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <input type="text" class="min-value input-value" value="0đ">
                    <input type="text" class="max-value input-value" value="10.000.000đ">
                </div>
            </div>
        </div>
    </div>
</div>