<?php

namespace App\Http\Controllers;

use App\settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use App\products;
use App\variation_product;
use App\purchase_order;
use App\shop;
use App\shopping_cart;


class UseInternalController extends Controller
{
    public function _getSetting($key = null)
    {
        try {
            if (!$key) {
                throw new \Exception('key null');
            }

            $data = settings::where('meta', $key);

            if (!$data->exists()) {
                throw new \Exception('meta not found');
            }

            $data = $data->first();

            return $data->value;


        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _getSettings()
    {
        try {

            $data = settings::all();
            return $data->toArray();

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _shoppingCart($usr = null)
    {
        try {
            if (!$usr) {
                throw new \Exception('str null');
            }

            $data = shopping_cart::orderBy('shopping_carts.id', 'DESC')
                ->where('shopping_carts.user_id', $usr)
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('variation_products', 'variation_products.id', '=', 'shopping_carts.product_variation_id')
                ->select('shopping_carts.id', 'products.name', 'variation_products.label',
                    'variation_products.price_normal',
                    'variation_products.price_regular',
                    'shopping_carts.shop_id',
                    'products.id as product_id'
                )
                ->get();

            $data_total = shopping_cart::orderBy('shopping_carts.id', 'DESC')
                ->where('shopping_carts.user_id', $usr)
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('variation_products', 'variation_products.id', '=', 'shopping_carts.product_variation_id')
                ->select(
                    DB::raw('sum(variation_products.price_normal) as price_normal'),
                    DB::raw('sum(variation_products.price_regular) as price_regular'),
                    'shopping_carts.shop_id'
                )
                ->groupBy('products.id')
                ->get();

            return [
                'list' => $data->toArray(),
                'total' => $data_total->toArray()
            ];


        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function actionID($str = null)
    {
        try {
            if (!$str) {
                throw new \Exception('str null');
            }

            $id = substr($str, strpos($str, "_") + 1);
            $action = substr($str, 0, strpos($str, "_"));

            return [
                'id' => $id,
                'action' => $action
            ];

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _isAvailableProduct($id = null)
    {
        try {
            if (!$id) {
                throw new \Exception('id null');
            }

            if (!products::where('id', $id)
                ->where('status','available')
                ->exists()) {
                throw new \Exception('not found');
            }

            $product = products::find($id);
            $shedule = array();
            $exceptions = array();
            $data = shop::where('shops.id', $product->shop_id)
                ->join('hours', 'shops.id', '=', 'hours.shop_id')
                ->select('shops.*', 'hours.shedule_hours as hours_shedule_hours',
                    'hours.exceptions as hours_exceptions')
                ->first();

            if (!$data) {
                return [
                    'isAvailable' => false,
                    'nextOpen' => false,
                    'nextClose' => false,
                ];
            }

            if ($data) {
                $hours_shedule_hours = ($data->hours_shedule_hours && json_decode($data->hours_shedule_hours)) ?
                    json_decode($data->hours_shedule_hours) : null;

                $hours_exceptions = ($data->hours_exceptions && json_decode($data->hours_exceptions)) ?
                    json_decode($data->hours_exceptions) : null;

                if ($hours_shedule_hours && $hours_exceptions) {
                    $openingHours = OpeningHours::create([
                        'monday' => $hours_shedule_hours->monday,
                        'tuesday' => $hours_shedule_hours->tuesday,
                        'wednesday' => $hours_shedule_hours->wednesday,
                        'thursday' => $hours_shedule_hours->thursday,
                        'friday' => $hours_shedule_hours->friday,
                        'saturday' => $hours_shedule_hours->saturday,
                        'sunday' => $hours_shedule_hours->sunday,
                        'exceptions' => $hours_exceptions
                    ]);
                    $next_available = $openingHours->nextOpen(Carbon::now());
                    $next_available = Carbon::parse($next_available)->toArray();
                    $next_close = $openingHours->nextClose(Carbon::now());
                    $next_close = Carbon::parse($next_close)->toArray();
                    $shedule = $openingHours->isOpenAt(Carbon::now());
                }

                return [
                    'isAvailable' => $shedule,
                    'nextOpen' => $next_available,
                    'nextClose' => $next_close,
                ];

            };

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _getVariations($id = null, $sort = 'ASC')
    {
        try {
            $data = [];

            if (!$id) {
                throw new \Exception('id null');
            }

            if (!products::where('id', $id)->exists()) {
                throw new \Exception('not found');
            }

            $data = variation_product::where('variation_products.product_id', $id)
                ->join('attacheds', 'variation_products.attached_id', '=', 'attacheds.id')
                ->select('variation_products.*', 'attacheds.small as attacheds_small',
                    'attacheds.medium as attacheds_medium', 'attacheds.large as attacheds_large')
                ->orderBy('variation_products.price_normal', $sort)
                ->get();

            return [
                'length' => count($data),
                'item' => $data
            ];

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _totalPurchase($uuid = null)
    {
        try {
            $data = [];

            if (!$uuid) {
                throw new \Exception('uuid null');
            }

            if (!purchase_order::where('uuid', $uuid)->exists()) {
                throw new \Exception('not found');
            }

            $data = purchase_order::where('uuid', $uuid)
                ->select(DB::raw('SUM(amount) as total_products'),
                    DB::raw('SUM(amount_shipping) as total_shipping'),
                    DB::raw('SUM(feed) as total_feed'),
                    DB::raw('SUM(amount + amount_shipping + feed) as total'))
                ->first();

            return $data->toArray();

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _purchaseStatus($uuid=null)
    {
        try{

            if (!$uuid) {
                throw new \Exception('uuid null');
            }

            $data = purchase_order::where('uuid',$uuid)
                ->get();

            return [
                'purchase' => $data->toArray()
            ];

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }
}
