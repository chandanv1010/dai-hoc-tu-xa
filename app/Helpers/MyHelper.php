<?php

use App\Enums\PromotionEnum;

if(!function_exists('convertRevenueChartData')){
    function convertRevenueChartData($chartData, $data = 'monthly_revenue', $label = 'month' , $text = 'Tháng'){
        $newArray = [];
        if(!is_null($chartData) && count($chartData)){
            foreach($chartData as $key => $val){
                $newArray['data'][] = $val->{$data};
                $newArray['label'][] = $text.' '.$val->{$label};
            }
        }
        return $newArray;
    }
}


if(!function_exists('growHtml')){
    function growHtml($grow){
        if($grow > 0){
            return '<div class="stat-percent font-bold text-success">'.$grow.'% <i class="fa fa-level-up"></i></div>';
        }else{
            return '<div class="stat-percent font-bold text-danger">'.$grow.'% <i class="fa fa-level-down"></i></div>';
        }
    }
}

if(!function_exists('growth')){
    function growth($currentValue, $previousValue){
        $divison = ($previousValue == 0) ? 1 : $previousValue;
        $grow =  ($currentValue - $previousValue) / $divison * 100;
        return number_format($grow, 1);
    }
}

if(!function_exists('pre')){
    function pre($data = ''){
        echo '<pre>';
        print_r($data);
        echo '<pre>';
        die();
    }
}

if(!function_exists('image')){
    function image($image){
        
        if(is_null($image)) return 'backend/img/not-found.jpg';

        $image = str_replace('/public/', '/', $image);

        return $image;
    }
}

if(!function_exists('getGiaoHangNhanhToken')){
    function getGiaoHangNhanhToken(){
       return '21e62550-a35f-11ef-a89d-dab02cbaab48';
    }
}


if(!function_exists('convert_price')){
    function convert_price(mixed $price = '', $flag = false){
        if($price === null) return 0;
        return ($flag === false) ? str_replace('.','', $price) : number_format($price, 0, ',', '.');
    }
}

if(!function_exists('getPercent')){
    function getPercent($product = null, $discountValue = 0){
        return ($product->price > 0) ? round($discountValue/$product->price*100) : 0;
    
    }
}

if(!function_exists('getPromotionPrice')){
    function getPromotionPrice($priceMain = 0, $discountValue = 0){
       

        return $priceMain - $discountValue;
    
    }
}


if(!function_exists('getPrice')){
    function getPrice($product = null){
        $result = [
            'price' => $product->price, 
            'priceSale' => 0,
            'percent' => 0, 
            'html' => ''
        ];

        if($product->price == 0){

            $result['html'] .= '<div class="price mt10">';
                $result['html'] .= '<div class="price-sale">Liên Hệ</div>';
            $result['html'] .= '</div>';
            return $result;
        }

        // Kiểm tra promotion - có thể là object hoặc null
        if(!empty($product->promotions) && is_object($product->promotions)){
            $promotion = $product->promotions;
            
            // Lấy giá trị discount đã tính toán sẵn hoặc tính toán từ discountValue và discountType
            $discount = 0;
            
            // Ưu tiên sử dụng discount đã tính toán sẵn
            if(isset($promotion->discount) && $promotion->discount > 0){
                $discount = (float)$promotion->discount;
            } 
            // Nếu không có discount, tính từ discountValue và discountType
            elseif(isset($promotion->discountValue) && $promotion->discountValue > 0 && isset($promotion->discountType)){
                if($promotion->discountType == 'cash'){
                    $discount = (float)$promotion->discountValue;
                } elseif($promotion->discountType == 'percent'){
                    $discount = ($product->price * (float)$promotion->discountValue) / 100;
                    // Áp dụng maxDiscountValue nếu có
                    if(isset($promotion->maxDiscountValue) && $promotion->maxDiscountValue > 0){
                        $discount = min($discount, (float)$promotion->maxDiscountValue);
                    }
                }
            }
            
            if($discount > 0){
                $result['percent'] = getPercent($product, $discount);
                $result['priceSale'] = getPromotionPrice($product->price, $discount);
            }
        }
        $result['html'] .= '<div class="price uk-flex uk-flex-middle mt10">';
            $result['html'] .= '<div class="price-sale">'.(($result['priceSale'] > 0) ? convert_price($result['priceSale'], true) : convert_price($result['price'], true) ).'<span class="currency">₫</span></div>';
            if($result['priceSale'] > 0){
                $result['html'] .= '<div class="price-old uk-flex uk-flex-middle">'.convert_price($result['price'], true).'đ <div class="percent"><div class="percent-value">-'.$result['percent'].'%</div></div></div>';
                
            }
        $result['html'] .= '</div>';
        return $result;
    }
}

if(!function_exists('getVariantPrice')){
    function getVariantPrice($variant, $variantPromotion){
        $result = [
            'price' => $variant->price, 
            'priceSale' => 0,
            'percent' => 0, 
            'html' => ''
        ];

        if($variant->price == 0){

            $result['html'] .= '<div class="price mt10">';
                $result['html'] .= '<div class="price-sale">Liên Hệ</div>';
            $result['html'] .= '</div>';
            return $result;
        }

        if(!is_null($variantPromotion)){
            $result['percent'] = getPercent($variant, $variantPromotion->discount);
            $result['priceSale'] = getPromotionPrice($variant->price, $variantPromotion->discount);
        }


        $result['html'] .= '<div class="price-sale">'.(($result['priceSale'] > 0) ? convert_price($result['priceSale'], true) : convert_price($result['price'], true) ).'đ</div>';
        if($result['priceSale'] !== $result['price']){
            $result['html'] .= '<div class="price-old">'.convert_price($result['price'], true).'đ <div class="percent"><div class="percent-value">-'.$result['percent'].'%</div></div></div>';
        }
        return $result;
    }
}


if(!function_exists('getReview')){
    function getReview($product = ''){

        $totalReviews = $product->reviews()->count();
        $totalRate = number_format($product->reviews()->avg('score'), 1);
        $starPercent = ($totalReviews == 0) ? '0' : $totalRate/5*100;

        return [
            'star' => $starPercent,
            'count' => $totalReviews,
            'totalRate' => $totalRate
        ];
        
    }
}


if(!function_exists('convert_array')){
    function convert_array($system = null, $keyword = '', $value = ''){
        $temp = [];
        if(is_array($system)){
            foreach($system as $key => $val){
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if(is_object($system)){
            foreach($system as $key => $val){
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }

        return $temp;
    }
}

if(!function_exists('convertDateTime')){
    function convertDateTime(string $date = '', string $format = 'd/m/Y H:i', string $inputDateFormat = 'Y-m-d H:i:s'){
       $carbonDate = \Carbon\Carbon::createFromFormat($inputDateFormat, $date);

       return $carbonDate->format($format);
    }
}

if(!function_exists('renderDiscountInformation')){
    function renderDiscountInformation($promotion = []){
        if($promotion->method === 'product_and_quantity'){
            $discountValue = $promotion->discountInformation['info']['discountValue'];
            $discountType = ($promotion->discountInformation['info']['discountType'] == 'percent') ? '%' : 'đ';
            return '<span class="label label-success">'.$discountValue.$discountType.' </span>';
        }
        return  '<div><a href="'.route('promotion.edit', $promotion->id).'">Xem chi tiết</a></div>';
    }
}

if(!function_exists('renderDiscountVoucher')){
    function renderDiscountVoucher($voucher = []){
        $discount_value = $voucher->discount_value;
        $discount_type = ($voucher->discount_type == 'PERCENTAGE') ? '%' : 'đ';
        return '<span class="label label-success">'.$discount_value.$discount_type.' </span>';
    }
}

if(!function_exists('renderSystemInput')){
    function renderSystemInput(string $name = '', $systems = null){
        return '<input 
            type="text"
            name="config['.$name.']"
            value="'.old($name, ($systems[$name]) ?? '').'"
            class="form-control"
            placeholder=""
            autocomplete="off"
        >';
    }
}


if(!function_exists('renderSystemImages')){
    function renderSystemImages(string $name = '', $systems = null){
        return '<input 
            type="text"
            name="config['.$name.']"
            value="'.old($name, ($systems[$name]) ?? '').'"
            class="form-control upload-image"
            placeholder=""
            autocomplete="off"
        >';
    }
}


if(!function_exists('renderSystemTextarea')){
    function renderSystemTextarea(string $name = '', $systems = null){
        return '<textarea name="config['.$name.']" class="form-control system-textarea">'.old($name, ($systems[$name]) ?? '').'</textarea>';
    }
}

if(!function_exists('renderSystemEditor')){
    function renderSystemEditor(string $name = '', $systems = null){
        return '<textarea name="config['.$name.']" id="'.$name.'" class="form-control system-textarea ck-editor">'.old($name, ($systems[$name]) ?? '').'</textarea>';
    }
}

if(!function_exists('renderSystemLink')){
    function renderSystemLink(array $item = [], $systems = null){
        return (isset($item['link'])) ? '<a class="system-link" target="'.$item['link']['target'].'" href="'.$item['link']['href'].'">'.$item['link']['text'].'</a>' : '';
    }
}

if(!function_exists('renderSystemTitle')){
    function renderSystemTitle(array $item = [], $systems = null){
        return (isset($item['title'])) ? '<span class="system-link text-danger">'.$item['title'].'</span>' : '';
    }
}

if(!function_exists('renderSystemSelect')){
    function renderSystemSelect(array $item, string $name = '', $systems = null){
       $html = '<select name="config['.$name.']" class="form-control">';
            foreach($item['option'] as $key => $val){
                $html .= '<option '.((isset($systems[$name]) && $key == $systems[$name]) ? 'selected' : '').' value="'.$key.'">'.$val.'</option>';
            }
       $html .= '</select>';

       return $html;
    }
}

if(!function_exists('write_url')){
    function write_url($canonical = null, bool $fullDomain = true, $suffix = true){
        $canonical = ($canonical) ?? '';
        if(strpos($canonical, 'http') !== false){
            // Nếu là external URL, thêm UTM nếu cần
            return addUtmToUrl($canonical);
        }
        $fullUrl = (($fullDomain === true) ? config('app.url') : '').$canonical.( ($suffix === true) ? config('apps.general.suffix') : '' );
        // Tự động thêm UTM parameters nếu có trong session
        return addUtmToUrl($fullUrl);
    }
}

if(!function_exists('seo')){
    function seo($model = null, $page = 1){
        // Kiểm tra xem model có relationship languages và pivot không (Major, School, etc.)
        $pivot = null;
        $canonicalValue = null;
        $metaTitle = null;
        $metaKeyword = null;
        $metaDescription = null;
        $name = null;
        $description = null;
        
        if ($model && method_exists($model, 'languages') && $model->languages && $model->languages->count() > 0) {
            $pivot = $model->languages->first()->pivot ?? null;
            if ($pivot) {
                $canonicalValue = $pivot->canonical ?? null;
                $metaTitle = $pivot->meta_title ?? null;
                $metaKeyword = $pivot->meta_keyword ?? null;
                $metaDescription = $pivot->meta_description ?? null;
                $name = $pivot->name ?? null;
                $description = $pivot->description ?? null;
            }
        }
        
        // Nếu không có từ pivot, lấy từ model trực tiếp
        $canonicalValue = $canonicalValue ?? ($model->canonical ?? null);
        $metaTitle = $metaTitle ?? ($model->meta_title ?? null);
        $metaKeyword = $metaKeyword ?? ($model->meta_keyword ?? null);
        $metaDescription = $metaDescription ?? ($model->meta_description ?? null);
        $name = $name ?? ($model->name ?? null);
        $description = $description ?? ($model->description ?? null);
        
        // Fallback: nếu không có meta_title thì dùng name
        if (empty($metaTitle)) {
            $metaTitle = $name ?? '';
        }
        
        // Fallback: nếu không có meta_description thì dùng description
        if (empty($metaDescription)) {
            $metaDescription = !empty($description) ? cut_string_and_decode($description, 168) : '';
        }
        
        // Xử lý canonical
        $canonical = ($page > 1) 
            ? write_url($canonicalValue, true, false).'/trang-'.$page.config('apps.general.suffix')
            : write_url($canonicalValue, true, true);
        
        // Lấy follow từ model (nếu có)
        $follow = null;
        if ($model && isset($model->follow)) {
            $follow = $model->follow;
        }
        
        return [
            'meta_title' => $metaTitle,
            'meta_keyword' => $metaKeyword ?? '',
            'meta_description' => $metaDescription,
            'meta_image' => $model->image ?? null,
            'canonical' => $canonical,
            'follow' => $follow, // 1 = Follow, 2 = Nofollow, null = default Follow
        ];
    }
}

if(!function_exists('recursive')){
    function recursive($data, $parentId = 0){
        $temp = [];
        if(!is_null($data) && count($data)){
            foreach($data as $key => $val){
                if($val->parent_id == $parentId){
                    $temp[] = [
                        'item' => $val,
                        'children' => recursive($data, $val->id)
                    ];
                }
            }
        }
        return $temp;
    }
}

if(!function_exists('frontend_recursive_menu')){
    function frontend_recursive_menu(array $data = [], int $parentId = 0, int $count = 1, $type = 'html'){
        $html = '';
        if(isset($data) && !is_null($data) && count($data)){
            if($type == 'html'){
                foreach($data as $key => $val){
                    $name = $val['item']->languages->first()->pivot->name;
                    $canonical = write_url($val['item']->languages->first()->pivot->canonical, true, true);
                    $ulClass = ($count >= 1) ? 'menu-level__'.($count + 1) : '';
                    $html .= '<li class="'.(($count != 0 && count($val['children'])) ? 'children' : '').'">';
                        $html .= '<a href="'.(($name == 'Trang chủ') ? '.' : $canonical).'" title="'.$name.'" data-menu-id="'.$val['item']->id.'">'.
                        (($name == 'Trang chủ') ? '' : '').$name.'</a>';
                        if(count($val['children'])){
                            $html .= '<div class="header-dropdown-menu">';
                                $html .= '<ul class="uk-list uk-clearfix menu-style '.$ulClass.'">';
                                    $html .= frontend_recursive_menu($val['children'], $val['item']->parent_id,  $count + 1, $type);
                                $html .= '</ul>';
                            $html .='</div>';
                        }
                    $html .= '</li>';
                }
                return $html;
            } 
        }
        return $data;
       
    }
}


if(!function_exists('recursive_menu')){
    function recursive_menu($data){
        $html = '';
        if(count($data)){
            foreach($data as $key => $val){
                $itemId = $val['item']->id;
                $itemName = $val['item']->languages->first()->pivot->name;
                $itemUrl = route('menu.children', ['id' => $itemId]);


                $html .= "<li class='dd-item' data-id='$itemId'>" ;
                    $html .= "<div class='dd-handle'>";
                        $html .= "<span class='label label-info'><i class='fa fa-arrows'></i></span> $itemName";
                    $html .= "</div>";
                    $html .= "<a class='create-children-menu' href='$itemUrl'> Quản lý menu con </a>";

                    if(count($val['children'])){
                        $html .= "<ol class='dd-list'>";
                            $html .= recursive_menu($val['children']);
                        $html .= '</ol>';
                    }
                $html .= "</li>";
            }
        }
        return $html;
    }
}


if(!function_exists('buildMenu')){
    function buildMenu($menus = null, $parent_id = 0, $prefix = ''){
        $output = [];
        $count = 1;

        if(count($menus)){
            foreach($menus as $key => $val){
                if($val->parent_id == $parent_id){
                    $val->position = $prefix.$count;
                    $output[] = $val;
                    $output = array_merge($output, buildMenu($menus, $val->id, $val->position . '.'));
                    $count++;
                }
            }
        }
        return $output;
    }
}

if(!function_exists('loadClass')){
    function loadClass(string $model = '', $folder = 'Repositories', $interface = 'Repository'){
        // Nếu model có namespace (App\Models\PostCatalogue), chỉ lấy tên class cuối cùng
        $className = class_basename($model);
        $serviceInterfaceNamespace = '\App\\'.$folder.'\\' . ucfirst($className) . $interface;
        $serviceInstance = null;
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        return $serviceInstance;
    }
}

if(!function_exists('convertArrayByKey')){
    function convertArrayByKey($object = null, $fields = []){
        $temp = [];
        foreach($object as $key => $val){
            foreach($fields as $field){
                if(is_array($object)){
                    $temp[$field][] = $val[$field];
                }else{
                    $extract = explode('.', $field);
                    if(count($extract) == 2){
                        if($extract[1] == 'languages'){
                            $temp[$extract[0]][] = $val->{$extract[1]}->first()->pivot->{$extract[0]};
                        }else{
                            $temp[$extract[0]][] = $val->pivot->{$extract[0]};
                        }
                        
                    }else{
                        $temp[$field][] = $val->{$field}; 
                    }
                    
                }
            }
        }
        return $temp;
    }
}

if(!function_exists('renderQuickBuy')){
    function renderQuickBuy($product, string $canonical = '', string $name = ''){

        $class = 'btn-addCart';
        $openModal = '';
        if(isset($product->product_variants) && count($product->product_variants)){
            $class = '';
            $canonical = '#popup';
            $openModal = 'data-uk-modal';
        }

        $html = '<a href="'.$canonical.'" '.$openModal.' title="'.$name.'" class="'.$class.'">
                <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g>
                    <path d="M24.4941 3.36652H4.73614L4.69414 3.01552C4.60819 2.28593 4.25753 1.61325 3.70863 1.12499C3.15974 0.636739 2.45077 0.366858 1.71614 0.366516L0.494141 0.366516V2.36652H1.71614C1.96107 2.36655 2.19748 2.45647 2.38051 2.61923C2.56355 2.78199 2.68048 3.00626 2.70914 3.24952L4.29414 16.7175C4.38009 17.4471 4.73076 18.1198 5.27965 18.608C5.82855 19.0963 6.53751 19.3662 7.27214 19.3665H20.4941V17.3665H7.27214C7.02705 17.3665 6.79052 17.2764 6.60747 17.1134C6.42441 16.9505 6.30757 16.7259 6.27914 16.4825L6.14814 15.3665H22.3301L24.4941 3.36652ZM20.6581 13.3665H5.91314L4.97214 5.36652H22.1011L20.6581 13.3665Z" fill="#253D4E"></path>
                    <path d="M7.49414 24.3665C8.59871 24.3665 9.49414 23.4711 9.49414 22.3665C9.49414 21.2619 8.59871 20.3665 7.49414 20.3665C6.38957 20.3665 5.49414 21.2619 5.49414 22.3665C5.49414 23.4711 6.38957 24.3665 7.49414 24.3665Z" fill="#253D4E"></path>
                    <path d="M17.4941 24.3665C18.5987 24.3665 19.4941 23.4711 19.4941 22.3665C19.4941 21.2619 18.5987 20.3665 17.4941 20.3665C16.3896 20.3665 15.4941 21.2619 15.4941 22.3665C15.4941 23.4711 16.3896 24.3665 17.4941 24.3665Z" fill="#253D4E"></path>
                    </g>
                    <defs>
                    <clipPath>
                    <rect width="24" height="24" fill="white" transform="translate(0.494141 0.366516)"></rect>
                    </clipPath>
                    </defs>
                </svg>
        </a>';
    return $html;
    }
}

if(!function_exists('cutnchar')){
	function cutnchar($str = NULL, $n = 320){
		if(strlen($str) < $n) return $str;
		$html = substr($str, 0, $n);
		$html = substr($html, 0, strrpos($html,' '));
		return $html.'...';
	}
}

if(!function_exists('cut_string_and_decode')){
	function cut_string_and_decode($str = NULL, $n = 200){
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = cutnchar($str, $n);
        return $str;
	}
}

if(!function_exists('categorySelectRaw')){
    function categorySelectRaw($table = 'products'){
        $rawQuery = "
            (
                SELECT COUNT(id) 
                FROM {$table}s
                JOIN {$table}_catalogue_{$table} as tb3 ON tb3.{$table}_id = {$table}s.id
                WHERE tb3.{$table}_catalogue_id IN (
                    SELECT id
                    FROM {$table}_catalogues as parent_category
                    WHERE lft >= (SELECT lft FROM {$table}_catalogues as pc WHERE pc.id = {$table}_catalogues.id)
                    AND rgt <= (SELECT rgt FROM {$table}_catalogues as pc WHERE pc.id = {$table}_catalogues.id)
                )
            ) as {$table}s_count 
        "; 
        return $rawQuery;
    }
}


if(!function_exists('sortString')){
    function sortString($string = ''){
        $extract = explode(',', $string);
        $extract = array_map('trim', $extract);
        sort($extract, SORT_NUMERIC);
        $newArray = implode(',', $extract);
        return $newArray;
    }
}


if(!function_exists('sortAttributeId')){
    function sortAttributeId(array $attributeId = []){
        sort($attributeId, SORT_NUMERIC);
        $attributeId = implode(',', $attributeId);
        return $attributeId;
    }
}


if(!function_exists('vnpayConfig')){
    function vnpayConfig(){
        return [
            'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'vnp_Returnurl' => write_url('return/vnpay'),
            'vnp_TmnCode' => 'RLE42FCR',
            'vnp_HashSecret' => 'OQPUUZRVSSJASOQVUQHHURHBXGDIMBTU',
            'vnp_apiUrl' => 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html',
            'apiUrl' => 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'
        ];
    }
}


if(!function_exists('momoConfig')){
    function momoConfig(){
        return [
            'partnerCode' => 'MOMOBKUN20180529',
            'accessKey' => 'klm05TvNBzhg7h7j',
            'secretKey' => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',
        ];
    }
}

if(!function_exists('zaloConfig')){
    function zaloConfig(){
        return [
            'appid' => '553',
            'key1' => '9phuAOYhan4urywHTh0ndEXiV3pKHr5Q',
            'key2' => 'Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3',
        ];
    }
}

if(!function_exists('execPostRequest')){
    function execPostRequest($url, $data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
}


if(!function_exists('getReviewName')){
    function getReviewName($string){
        // $string = Nguyễn Công Tuấn
        $words = explode(' ', $string);
        $initialize = '';
        foreach($words as $key => $val){
            $initialize .= strtoupper(substr($val, 0, 1));
        }
        return $initialize;
    }
}


if(!function_exists('generateStar')){
    function generateStar($rating){
        $rating = max(1, min(5, $rating));
        $output = '<div class="review-star">';
            for($i = 1; $i <= $rating; $i++){
                $output .= '<i class="fa fa-star"></i>';
            }
            for($i = $rating + 1; $i <= 5; $i++){
                $output .= '<i class="fa fa-star-o"></i>';
            }
        $output .= '</div>';
        return $output;
    }
}


if(!function_exists('convertCombineArray')){
    function convertCombineArray(mixed $data, $mix_1 = ''){
        $array = [];
        foreach($data as $key => $val){
            $array[$val->id] = (($mix_1 != '') ? $val->{$mix_1} : $val->code).' / '.$val->phone;
        }
        return $array;
    }
}


if(!function_exists('convertArray')){
    function convertArray($datas){
        $id = [];
        foreach ($datas as $data) {
            $id[]= $data->id;
        }
        return $id;
    }
}

if(!function_exists('convertToIdNameArray')){
    function convertToIdNameArray($customers)
   {
    $idNameArray = [];

    foreach ($customers as $customer) {
        $idNameArray[$customer['id']] = $customer['name'];
    }

    return $idNameArray;
    }

}

if(!function_exists('convertToK')){
    function convertToK($discount)
   {
        if ($discount >= 1000) {
            return number_format($discount / 1000, 0, '.', '') . 'k';
        }
        return $discount;
    }
}
  

if(!function_exists('convertData')){
    function convertData($data, $type)
    {
        $promotion_id = $data->id;
        $payload_pivot = ($type == 'products') ? $data->promotion_rules : $data->promotion_gifts;
        $products = ($type == 'products') ? 
        DB::table('promotion_rules')->where('promotion_id', $promotion_id)->get() : DB::table('promotion_gifts')->where('promotion_id', $promotion_id)->get();
        $temp = [];
        if(!is_null($products)){
            foreach($products as $k => $v){
                $temp['id'][$k] = $v->product_id;
                $temp['quantity'][$k] = $v->quantity;
                $temp['image'][$k] = $payload_pivot[$k]['image'];
                $temp['name'][$k] = $payload_pivot[$k]->languages->first()->pivot->name;
            }
        }
        return $temp;
    }
}



if(!function_exists('thumb')){
    function thumb($path, $width = null, $height = null)
    {
        $width = 600;
        $height = 400;

        if (empty($path)) {
            return asset('images/no-image.jpg');
        }
        
        $params = ['src' => $path];
        
        if ($width) {
            $params['w'] = $width;
        }
        
        if ($height) {
            $params['h'] = $height;
        }
        
        // return route('thumb', $params);

        return $path;
    }
}

if (!function_exists('convertImgToAnchor')) {
    function convertImgToAnchor($html) {
        if (!$html || !is_string($html)) {
            return $html;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        if ($images->length === 0) {
            return $html;
        }

        foreach ($images as $image) {
            $src = $image->getAttribute('src') ?: '';
            $alt = $image->getAttribute('alt') ?: '';
            $class = $image->getAttribute('class') ?: 'img-cover img-zoomin';

            // Tạo thẻ <a>
            $anchor = $dom->createElement('a');
            $anchor->setAttribute('href', $src);
            $anchor->setAttribute('title', $alt);
            $anchor->setAttribute('class', $class);

            // Thêm <div class="skeleton-loading"> vào trong <a>
            $skeleton = $dom->createElement('span');
            $skeleton->setAttribute('class', 'skeleton-loading');
            $anchor->appendChild($skeleton);

            // Thêm <img class="lazy-image"> vào trong <a>
            $newImg = $dom->createElement('img');
            $newImg->setAttribute('class', 'lazy-image');
            $newImg->setAttribute('data-src', $src);
            $newImg->setAttribute('alt', $alt);
            $anchor->appendChild($newImg);

            // Thay thế <img> bằng <a> hoàn chỉnh
            $image->parentNode->replaceChild($anchor, $image);
        }

        $html = $dom->saveHTML();
        // Loại bỏ các thẻ HTML bổ sung do DOMDocument thêm vào
        $html = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html><body>', '</body></html>'], '', $html));

        return $html;
    }
}

if (!function_exists('calculateCourses')) {
    function calculateCourses($product) {
        $totalMinutes = 0;
        $totalSession = 0;
        $temp = $product->chapter;
        if (!is_array($temp)) {
            $temp = json_decode($temp, true); 
        }
        foreach ($temp as $chapter) {
            if (isset($chapter['content']) && is_array($chapter['content'])) {
                foreach ($chapter['content'] as $lesson) {
                    $totalSession++;
                    if (isset($lesson['time'])) {
                        $totalMinutes += (int)$lesson['time'];
                    }
                }
            }
        }
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        $durationText = '';
        if ($hours > 0 && $minutes > 0) {
            $durationText = $hours . ' giờ ' . $minutes . ' phút';
        } elseif ($hours > 0) {
            $durationText = $hours . ' giờ';
        } else {
            $durationText = $minutes . ' phút';
        }
        $chapters = [
            'durationText' => $durationText,
            'totalSession' => $totalSession
        ];
        return $chapters;
    }
}

if (!function_exists('addUtmToUrl')) {
    /**
     * Thêm UTM parameters vào URL nếu có trong session
     * Chỉ áp dụng cho frontend URLs
     */
    function addUtmToUrl($url) {
        // Chỉ thêm UTM cho frontend URLs, không thêm cho backend
        if (str_contains($url, '/admin') || 
            str_contains($url, '/dashboard') || 
            str_contains($url, '/backend') ||
            str_contains($url, '/api/')) {
            return $url;
        }
        
        // Lấy UTM parameters từ session
        $utmParams = session('utm_parameters', []);
        
        if (empty($utmParams)) {
            return $url;
        }
        
        // Parse URL
        $parsedUrl = parse_url($url);
        $queryParams = [];
        
        // Lấy query string hiện tại nếu có
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }
        
        // Merge UTM parameters (không ghi đè nếu đã có trong URL)
        foreach ($utmParams as $key => $value) {
            if (!isset($queryParams[$key])) {
                $queryParams[$key] = $value;
            }
        }
        
        // Rebuild URL
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        
        return $scheme . $host . $port . $path . $query . $fragment;
    }
}

if (!function_exists('formatPaginationUrl')) {
    /**
     * Format pagination URL với UTM parameters
     * Chuyển ?page=2 hoặc &page=2 thành /trang-2.html?utm_source=...
     */
    function formatPaginationUrl($url, $page) {
        if (!$url) {
            return null;
        }
        
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $query = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }
        unset($query['page']);
        
        if ($page == 1) {
            $formattedUrl = $path . config('apps.general.suffix');
        } else {
            $formattedUrl = $path . '/trang-' . $page . config('apps.general.suffix');
        }
        
        if (!empty($query)) {
            $formattedUrl .= '?' . http_build_query($query);
        }
        
        return $formattedUrl;
    }
}

if(!function_exists('extractFaqFromContent')){
    /**
     * Extract FAQ questions and answers from HTML content
     * Supports multiple formats:
     * 1. UIkit Accordion: <ul class="uk-accordion"> with <li><a>question</a><div>answer</div></li>
     * 2. H2/H3 headings with following content as answers
     * 3. Custom FAQ structure
     * 
     * @param string $htmlContent
     * @return array Array of ['question' => string, 'answer' => string]
     */
    function extractFaqFromContent($htmlContent){
        $faqs = [];
        
        if(empty($htmlContent)){
            return $faqs;
        }
        
        // Load HTML into DOMDocument
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
        
        // Method 1: UIkit Accordion
        $accordionItems = $xpath->query("//ul[contains(@class, 'uk-accordion')]//li");
        if($accordionItems->length > 0){
            foreach($accordionItems as $item){
                $questionNode = $xpath->query(".//a[contains(@class, 'uk-accordion-title')] | .//a[not(contains(@class, 'uk-accordion-content'))]", $item)->item(0);
                $answerNode = $xpath->query(".//div[contains(@class, 'uk-accordion-content')] | .//div[following-sibling::a[contains(@class, 'uk-accordion-title')]]", $item)->item(0);
                
                if($questionNode && $answerNode){
                    $question = trim(strip_tags($questionNode->textContent));
                    $answer = trim(strip_tags($answerNode->textContent));
                    
                    if(!empty($question) && !empty($answer)){
                        $faqs[] = [
                            'question' => html_entity_decode($question, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'answer' => html_entity_decode($answer, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                        ];
                    }
                }
            }
        }
        
        // Method 2: H2/H3 headings pattern (if no accordion found)
        if(empty($faqs)){
            // Look for H2 or H3 followed by content
            $headings = $xpath->query("//h2 | //h3");
            foreach($headings as $heading){
                $question = trim(strip_tags($heading->textContent));
                
                // Get next sibling content as answer
                $answer = '';
                $nextSibling = $heading->nextSibling;
                while($nextSibling && empty($answer)){
                    if($nextSibling->nodeType === XML_ELEMENT_NODE){
                        $tagName = strtolower($nextSibling->tagName);
                        // Stop if we hit another heading
                        if(in_array($tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])){
                            break;
                        }
                        $answer = trim(strip_tags($nextSibling->textContent));
                        if(!empty($answer)){
                            break;
                        }
                    }
                    $nextSibling = $nextSibling->nextSibling;
                }
                
                // Also check for p or div immediately after
                if(empty($answer)){
                    $nextElement = $xpath->query("following-sibling::p[1] | following-sibling::div[1]", $heading)->item(0);
                    if($nextElement){
                        $answer = trim(strip_tags($nextElement->textContent));
                    }
                }
                
                if(!empty($question) && !empty($answer) && strlen($answer) > 20){
                    $faqs[] = [
                        'question' => html_entity_decode($question, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        'answer' => html_entity_decode($answer, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
        }
        
        // Method 3: Pattern with FAQ class or data attributes
        if(empty($faqs)){
            $faqContainers = $xpath->query("//*[contains(@class, 'faq') or contains(@class, 'question')]");
            foreach($faqContainers as $container){
                $questionNode = $xpath->query(".//*[contains(@class, 'question') or contains(@class, 'faq-question')] | .//h3 | .//h4", $container)->item(0);
                $answerNode = $xpath->query(".//*[contains(@class, 'answer') or contains(@class, 'faq-answer')] | .//p[1] | .//div[contains(@class, 'content')]", $container)->item(0);
                
                if($questionNode){
                    $question = trim(strip_tags($questionNode->textContent));
                    $answer = $answerNode ? trim(strip_tags($answerNode->textContent)) : '';
                    
                    if(!empty($question) && !empty($answer)){
                        $faqs[] = [
                            'question' => html_entity_decode($question, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'answer' => html_entity_decode($answer, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                        ];
                    }
                }
            }
        }
        
        return $faqs;
    }
}

if(!function_exists('cleanFaqText')){
    /**
     * Clean FAQ text: decode HTML entities, remove newlines/tabs, normalize spaces
     * 
     * @param string $text
     * @return string Cleaned text
     */
    function cleanFaqText($text){
        if(empty($text)){
            return '';
        }
        
        // Decode HTML entities first
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove HTML tags if any
        $text = strip_tags($text);
        
        // Remove escape sequences: \r\n, \n, \r, \t (both literal and actual)
        $text = str_replace(["\r\n", "\r", "\n", "\t", "\\r\\n", "\\n", "\\r", "\\t"], ' ', $text);
        
        // Normalize multiple spaces to single space
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim
        $text = trim($text);
        
        return $text;
    }
}

if(!function_exists('generateFaqSchema')){
    /**
     * Generate FAQPage JSON-LD schema from FAQ array
     * 
     * @param array $faqs Array of ['question' => string, 'answer' => string]
     * @return string JSON-LD schema as string
     */
    function generateFaqSchema($faqs){
        if(empty($faqs) || !is_array($faqs)){
            return '';
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => []
        ];
        
        foreach($faqs as $faq){
            if(isset($faq['question']) && isset($faq['answer']) && !empty(trim($faq['question'])) && !empty(trim($faq['answer']))){
                $question = cleanFaqText($faq['question']);
                $answer = cleanFaqText($faq['answer']);
                
                if(!empty($question) && !empty($answer)){
                    $schema['mainEntity'][] = [
                        '@type' => 'Question',
                        'name' => $question,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $answer
                        ]
                    ];
                }
            }
        }
        
        if(empty($schema['mainEntity'])){
            return '';
        }
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}

if(!function_exists('extractFaqFromMajor')){
    /**
     * Extract FAQ from Major object
     * Builds FAQ from various sections: who, priority, learn, chance, content, etc.
     *
     * @param object $major Major model object
     * @param object|null $pivot Major language pivot object
     * @return array Array of ['question' => string, 'answer' => string]
     */
    function extractFaqFromMajor($major, $pivot = null){
        $faqs = [];
        
        if(!$pivot && $major->languages && $major->languages->count() > 0){
            $pivot = $major->languages->first()->pivot;
        }
        
        if(!$pivot){
            return $faqs;
        }
        
        // 1. Extract from content if it has FAQ structure
        $content = $pivot->content ?? '';
        if(!empty($content)){
            $contentFaqs = extractFaqFromContent($content);
            if(!empty($contentFaqs)){
                $faqs = array_merge($faqs, $contentFaqs);
            }
        }
        
        // 2. Build FAQ from "who" section (Đối tượng tuyển sinh)
        $who = ($pivot && isset($pivot->who)) ? (is_array($pivot->who) ? $pivot->who : json_decode($pivot->who, true)) : [];
        if(!empty($who) && is_array($who)){
            $whoItems = isset($who['items']) ? $who['items'] : (is_array($who) && isset($who[0]) ? $who : []);
            if(!empty($whoItems) && count($whoItems) > 0){
                $whoTitle = isset($who['title']) && !empty($who['title']) ? $who['title'] : 'Đối tượng tuyển sinh';
                $whoAnswer = [];
                foreach($whoItems as $item){
                    $itemText = is_array($item) ? ($item['text'] ?? $item['name'] ?? '') : $item;
                    if(!empty($itemText)){
                        $whoAnswer[] = html_entity_decode(strip_tags($itemText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                }
                if(!empty($whoAnswer)){
                    $faqs[] = [
                        'question' => html_entity_decode($whoTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' là gì?',
                        'answer' => html_entity_decode(implode('. ', $whoAnswer), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
        }
        
        // 3. Build FAQ from "priority" section (Ưu điểm)
        $priority = ($pivot && isset($pivot->priority)) ? (is_array($pivot->priority) ? $pivot->priority : json_decode($pivot->priority, true)) : [];
        if(!empty($priority) && is_array($priority)){
            $priorityItems = isset($priority['items']) ? $priority['items'] : [];
            if(empty($priorityItems) && isset($priority[0])){
                $priorityItems = $priority;
            }
            if(!empty($priorityItems) && count($priorityItems) > 0){
                $priorityTitle = isset($priority['title']) && !empty($priority['title']) ? $priority['title'] : 'Ưu điểm khi học ngành này';
                $priorityAnswers = [];
                foreach(array_slice($priorityItems, 0, 5) as $item){
                    $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                    $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                    if(!empty($itemName)){
                        $answerText = html_entity_decode($itemName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if(!empty($itemDescription)){
                            $answerText .= ': ' . html_entity_decode(strip_tags($itemDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        }
                        $priorityAnswers[] = $answerText;
                    }
                }
                if(!empty($priorityAnswers)){
                    $faqs[] = [
                        'question' => html_entity_decode($priorityTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '?',
                        'answer' => html_entity_decode(implode('. ', $priorityAnswers), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
        }
        
        // 4. Build FAQ from training info
        $trainingSystem = $pivot->training_system ?? '';
        $studyMethod = $pivot->study_method ?? '';
        $admissionMethod = $pivot->admission_method ?? '';
        $trainingDuration = $pivot->training_duration ?? '';
        $degreeType = $pivot->degree_type ?? '';
        
        if(!empty($studyMethod)){
            $faqs[] = [
                'question' => 'Hình thức học của ngành này như thế nào?',
                'answer' => html_entity_decode(strip_tags($studyMethod), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            ];
        }
        
        if(!empty($trainingDuration)){
            $faqs[] = [
                'question' => 'Thời gian đào tạo của ngành này là bao lâu?',
                'answer' => html_entity_decode(strip_tags($trainingDuration), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            ];
        }
        
        if(!empty($admissionMethod)){
            $faqs[] = [
                'question' => 'Hình thức xét tuyển của ngành này là gì?',
                'answer' => html_entity_decode(strip_tags($admissionMethod), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            ];
        }
        
        if(!empty($degreeType)){
            $faqs[] = [
                'question' => 'Bằng cấp sau khi tốt nghiệp là gì?',
                'answer' => html_entity_decode(strip_tags($degreeType), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            ];
        }
        
        return $faqs;
    }
}

if(!function_exists('extractFaqFromSchool')){
    /**
     * Extract FAQ from School object
     * Builds FAQ from accordion sections: announceTarget, announceType, announceRequest, announceAddress, announceValue
     *
     * @param object $school School model object
     * @param object|null $pivot School language pivot object
     * @return array Array of ['question' => string, 'answer' => string]
     */
    function extractFaqFromSchool($school, $pivot = null){
        $faqs = [];
        
        if(!$pivot && $school->languages && $school->languages->count() > 0){
            $pivot = $school->languages->first()->pivot;
        }
        
        if(!$pivot){
            return $faqs;
        }
        
        // 1. Extract from content if it has FAQ structure
        $content = $pivot->content ?? '';
        if(!empty($content)){
            $contentFaqs = extractFaqFromContent($content);
            if(!empty($contentFaqs)){
                $faqs = array_merge($faqs, $contentFaqs);
            }
        }
        
        // 2. Extract from announce accordion sections
        $announce = ($pivot && isset($pivot->announce)) ? (is_array($pivot->announce) ? $pivot->announce : json_decode($pivot->announce, true)) : [];
        
        if(!empty($announce) && is_array($announce)){
            // Đối tượng tuyển sinh
            if(!empty($announce['target'])){
                $targetText = is_string($announce['target']) ? $announce['target'] : '';
                if(!empty($targetText)){
                    $faqs[] = [
                        'question' => 'Đối tượng tuyển sinh là gì?',
                        'answer' => html_entity_decode(strip_tags($targetText), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
            
            // Hình thức tuyển sinh
            if(!empty($announce['type'])){
                $typeText = is_string($announce['type']) ? $announce['type'] : '';
                if(!empty($typeText)){
                    $faqs[] = [
                        'question' => 'Hình thức tuyển sinh như thế nào?',
                        'answer' => html_entity_decode(strip_tags($typeText), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
            
            // Yêu cầu tuyển sinh
            if(!empty($announce['request'])){
                $requestText = is_string($announce['request']) ? $announce['request'] : '';
                if(!empty($requestText)){
                    $faqs[] = [
                        'question' => 'Yêu cầu tuyển sinh là gì?',
                        'answer' => html_entity_decode(strip_tags($requestText), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
            
            // Nơi tiếp nhận hồ sơ
            if(!empty($announce['address'])){
                $addressText = is_string($announce['address']) ? $announce['address'] : '';
                if(!empty($addressText)){
                    $faqs[] = [
                        'question' => 'Nơi tiếp nhận hồ sơ ở đâu?',
                        'answer' => html_entity_decode(strip_tags($addressText), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
            
            // Giá trị văn bằng
            if(!empty($announce['value'])){
                $valueText = is_string($announce['value']) ? $announce['value'] : '';
                if(!empty($valueText)){
                    $faqs[] = [
                        'question' => 'Giá trị văn bằng như thế nào?',
                        'answer' => html_entity_decode(strip_tags($valueText), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
        }
        
        // 2b. Extract from intro section if available
        $intro = ($pivot && isset($pivot->intro)) ? (is_array($pivot->intro) ? $pivot->intro : json_decode($pivot->intro, true)) : [];
        if(!empty($intro) && is_array($intro)){
            if(!empty($intro['created'])){
                $faqs[] = [
                    'question' => 'Trường được thành lập năm nào?',
                    'answer' => 'Trường được thành lập năm ' . html_entity_decode(strip_tags($intro['created']), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                ];
            }
        }
        
        // 2c. Extract from suitable section
        $suitable = ($pivot && isset($pivot->suitable)) ? (is_array($pivot->suitable) ? $pivot->suitable : json_decode($pivot->suitable, true)) : [];
        if(!empty($suitable) && is_array($suitable)){
            $suitableName = $suitable['name'] ?? 'Chương trình đào tạo từ xa phù hợp với ai?';
            $suitableDescription = $suitable['description'] ?? '';
            $suitableItems = $suitable['items'] ?? [];
            
            if(!empty($suitableItems) && count($suitableItems) > 0){
                $suitableAnswers = [];
                foreach(array_slice($suitableItems, 0, 3) as $item){
                    $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                    $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                    if(!empty($itemName)){
                        $answerText = html_entity_decode($itemName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if(!empty($itemDescription)){
                            $answerText .= ': ' . html_entity_decode(strip_tags($itemDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        }
                        $suitableAnswers[] = $answerText;
                    }
                }
                if(!empty($suitableAnswers)){
                    $faqs[] = [
                        'question' => html_entity_decode($suitableName, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        'answer' => html_entity_decode(implode('. ', $suitableAnswers), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
        }
        
        // 2d. Extract from advantage section
        $advantage = ($pivot && isset($pivot->advantage)) ? (is_array($pivot->advantage) ? $pivot->advantage : json_decode($pivot->advantage, true)) : [];
        if(!empty($advantage) && is_array($advantage)){
            $advantageTitle = $advantage['title'] ?? 'Ưu điểm của hệ đào tạo từ xa';
            $advantageItems = $advantage['items'] ?? [];
            
            if(!empty($advantageItems) && count($advantageItems) > 0){
                $advantageAnswers = [];
                foreach(array_slice($advantageItems, 0, 3) as $item){
                    $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                    $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                    if(!empty($itemName)){
                        $answerText = html_entity_decode($itemName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if(!empty($itemDescription)){
                            $answerText .= ': ' . html_entity_decode(strip_tags($itemDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        }
                        $advantageAnswers[] = $answerText;
                    }
                }
                if(!empty($advantageAnswers)){
                    $faqs[] = [
                        'question' => html_entity_decode($advantageTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '?',
                        'answer' => html_entity_decode(implode('. ', $advantageAnswers), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ];
                }
            }
        }
        
        // 2e. Extract from study_method section
        $studyMethod = ($pivot && isset($pivot->study_method)) ? (is_array($pivot->study_method) ? $pivot->study_method : json_decode($pivot->study_method, true)) : [];
        if(!empty($studyMethod) && is_array($studyMethod)){
            $studyMethodName = $studyMethod['name'] ?? 'Hình thức học';
            $studyMethodDescription = $studyMethod['description'] ?? '';
            $studyMethodContent = $studyMethod['content'] ?? '';
            
            if(!empty($studyMethodDescription) || !empty($studyMethodContent)){
                $answerText = !empty($studyMethodDescription) ? strip_tags($studyMethodDescription) : strip_tags($studyMethodContent);
                if(!empty($answerText)){
                    $answerText = html_entity_decode($answerText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $faqs[] = [
                        'question' => html_entity_decode($studyMethodName, ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' như thế nào?',
                        'answer' => mb_substr($answerText, 0, 500) . (mb_strlen($answerText) > 500 ? '...' : '')
                    ];
                }
            }
        }
        
        // 3. Build FAQ from description
        $description = $pivot->description ?? '';
        if(!empty($description)){
            $cleanDescription = html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if(mb_strlen($cleanDescription) > 50){
                $schoolName = $pivot->name ?? 'Trường';
                $faqs[] = [
                    'question' => html_entity_decode($schoolName, ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' là gì?',
                    'answer' => mb_substr($cleanDescription, 0, 500) . (mb_strlen($cleanDescription) > 500 ? '...' : '')
                ];
            }
        }
        
        return $faqs;
    }
}