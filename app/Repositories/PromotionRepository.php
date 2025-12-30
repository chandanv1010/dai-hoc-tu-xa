<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
/**
 * Class UserService
 * @package App\Services
 */
class PromotionRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Promotion $model
    ){
        $this->model = $model;
    }

    public function findByProduct(array $productId = [])
    {
        if (empty($productId)) {
            return collect();
        }
        $now = now()->format('Y-m-d H:i:s');
        return DB::table('products')
            ->select(
                'products.id as product_id', 
                'products.price as product_price',
                DB::raw("MAX(
                    CASE
                        WHEN promotions.maxDiscountValue != 0 AND promotions.maxDiscountValue IS NOT NULL THEN
                            LEAST(
                                CASE
                                    WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                                    WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                                    ELSE 0
                                END,
                                promotions.maxDiscountValue
                            )
                        ELSE
                            CASE
                                WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                                WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                                ELSE 0
                            END
                    END
                ) as discount"),
                DB::raw('MAX(promotions.id) as promotion_id'),
                DB::raw('MAX(promotions.discountValue) as discountValue'),
                DB::raw('MAX(promotions.discountType) as discountType'),
                DB::raw('MAX(promotions.maxDiscountValue) as maxDiscountValue'),
                DB::raw('MAX(promotions.endDate) as endDate')
            )
            ->leftJoin('promotion_product_variant as ppv', 'ppv.product_id', '=', 'products.id')
            ->leftJoin('promotions as promo1', function($join) use ($now) {
                $join->on('promo1.id', '=', 'ppv.promotion_id')
                    ->where('promo1.publish', 2)
                    ->where('promo1.method', 'product_and_quantity')
                    ->where('promo1.startDate', '<=', $now)
                    ->where(function($query) use ($now) {
                        $query->where('promo1.neverEndDate', '=', 'accept')
                              ->orWhere('promo1.endDate', '>', $now);
                    });
            })
            ->leftJoin(DB::raw('(
                SELECT DISTINCT product_id, product_catalogue_id 
                FROM (
                    SELECT product_id, product_catalogue_id FROM product_catalogue_product
                    UNION
                    SELECT id as product_id, product_catalogue_id FROM products WHERE product_catalogue_id IS NOT NULL
                ) as all_catalogues
            ) as pcprod'), 'pcprod.product_id', '=', 'products.id')
            ->leftJoin(DB::raw('(
                SELECT ppc.promotion_id, pc_child.id as product_catalogue_id
                FROM promotion_product_catalogue ppc
                JOIN product_catalogues pc_parent ON pc_parent.id = ppc.product_catalogue_id
                JOIN product_catalogues pc_child ON pc_child.lft >= pc_parent.lft AND pc_child.rgt <= pc_parent.rgt
            ) as pcp'), 'pcp.product_catalogue_id', '=', 'pcprod.product_catalogue_id')
            ->leftJoin('promotions as promo2', function($join) use ($now) {
                $join->on('promo2.id', '=', 'pcp.promotion_id')
                    ->where('promo2.publish', 2)
                    ->where('promo2.method', 'product_and_quantity')
                    ->where('promo2.startDate', '<=', $now)
                    ->where(function($query) use ($now) {
                        $query->where('promo2.neverEndDate', '=', 'accept')
                              ->orWhere('promo2.endDate', '>', $now);
                    });
            })
            ->leftJoin('promotions', function($join) {
                $join->on(function($query) {
                    $query->whereColumn('promotions.id', '=', 'promo1.id')
                        ->orWhereColumn('promotions.id', '=', 'promo2.id');
                });
            })
            ->where('products.publish', 2)
            ->whereIn('products.id', $productId)
            ->groupBy('products.id', 'products.price')
            ->get();
    }

    public function findPromotionByVariantUuid($uuid = ''){
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
        )
        ->selectRaw(
            "
                MAX(
                    IF(promotions.maxDiscountValue != 0,
                        LEAST(
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN pv.price * discountValue / 100
                            ELSE 0
                            END,
                            promotions.maxDiscountValue 
                        ),
                        CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN pv.price * discountValue / 100
                        ELSE 0
                        END
                    )
                ) as discount
            "
        )
        ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
        ->join('product_variants as pv', 'pv.uuid', '=', 'ppv.variant_uuid')
        ->where('promotions.publish', 2)
        ->where('ppv.variant_uuid', $uuid)
        ->whereDate('promotions.endDate', '>', now())
        ->whereDate('promotions.startDate', '<', now())
        ->orderByDesc('discount') 
        ->first();
    }

    public function getPromotionByCartTotal()
    {
        return $this->model
            ->where('promotions.publish', 2)
            ->where('promotions.method', 'order_amount_range')
            ->whereDate('promotions.endDate', '>=', now())
            ->whereDate('promotions.startDate', '<=', now())
            ->get();
    }
    
    public function getPromotionTakeGiftBuyProduct($method, $id = null){
        $promotionIds = $this->model->join('promotion_rules as tb2', 'tb2.promotion_id', '=', 'promotions.id')
        ->where('tb2.product_id', $id)
        ->pluck('promotions.id');
        return $this->model->select(
            'promotions.*',
            'tb4.product_id as pd_id',
            'tb4.name as pd_name',
            'tb4.canonical as pd_canonical',
            'tb2.quantity as pd_quantity',
            'tb7.product_id as pdg_id',
            'tb7.name as pdg_name',
            'tb7.canonical as pdg_canonical',
            'tb5.quantity as pdg_quantity',
        )
        ->leftJoin('promotion_rules as tb2', 'tb2.promotion_id', '=', 'promotions.id')
        ->leftJoin('products as tb3', 'tb3.id', '=', 'tb2.product_id')
        ->leftJoin('product_language as tb4', 'tb4.product_id', '=', 'tb3.id')
        ->leftJoin('promotion_gifts as tb5', 'tb5.promotion_id', '=', 'promotions.id')
        ->leftJoin('products as tb6', 'tb6.id', '=', 'tb5.product_id')
        ->leftJoin('product_language as tb7', 'tb7.product_id', '=', 'tb6.id')
        ->where('promotions.method', $method)
        ->whereIn('promotions.id', $promotionIds)
        ->get();
    }

    
}

