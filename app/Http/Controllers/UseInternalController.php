<?php

namespace App\Http\Controllers;

use App\attached_products;
use App\purchase_detail;
use App\settings;
use App\user_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use App\products;
use App\variation_product;
use App\purchase_order;
use App\shop;
use App\comments;
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
                    'variation_products.id as variation_product_id',
                    'variation_products.delivery as variation_delivery',
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

    public function _detailPurchase($uuid = null)
    {
        try {
            if (!$uuid) {
                throw new \Exception('uuid null');
            }

            $data = purchase_order::where('purchase_orders.uuid', $uuid)
                ->select(DB::raw('SUM(amount + amount_shipping + feed) as total'),
                    DB::raw('SUM(feed) as feed_total'),
                    DB::raw('SUM(amount_shipping) as shipping_total'),
                    DB::raw('SUM(amount) as amount_total')
                )
                ->get();

            $data_detail = purchase_detail::where('purchase_uuid', $uuid)
                ->get();

            return [
                'list' => $data->toArray(),
                'detail' => $data_detail->toArray()
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
                ->where('status', 'available')
                ->exists()) {
                return [
                    'isAvailable' => false,
                    'nextOpen' => false,
                    'nextClose' => false,
                ];
            }

            $now = Carbon::now()->toArray();

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
                    $diff = Carbon::parse($next_available)->diffInMinutes($now);
                }

                return [
                    'isAvailable' => $shedule,
                    'nextOpen' => $next_available,
                    'nextClose' => $next_close,
                    'now' => $diff
                ];

            };

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _getImages($id)
    {
        try {
            if (!$id) {
                throw new \Exception('id null');
            }

            $data = attached_products::where('attached_products.product_id', $id)
                ->join('attacheds', 'attached_products.attached_id', '=', 'attacheds.id')
                ->select('attacheds.*', 'attached_products.product_id as product_id')
                ->take(15)
                ->get();

            return $data;

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _getComments()
    {

    }

    public function _getScoreShop($id = null)
    {
        try{
            if (!$id) {
                throw new \Exception('id null');
            }

            $data = comments::where('shop_id',$id)
            ->sum('score');
            $count = comments::where('shop_id',$id)
            ->count();

            return [
                'score' => $data,
                'count' => $count
            ];

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

    public function _getFeedAmount($amount = 0)
    {
        try {
            if ($amount < 1) {
                throw new \Exception('invalid amount');
            }
            $total = $amount;

            $feed_percentage = $this->_getSetting('feed_percentage');
            $feed_amount = $this->_getSetting('feed_amount');
            $feed_limit_price = $this->_getSetting('feed_limit_price');

            if ($amount >= $feed_limit_price) {
                $percentage_feed = $amount * $feed_percentage;
                $amount = ($amount - $percentage_feed);
                $application_feed = $percentage_feed;

                return [
                    'amount_with_feed' => round($total, 2),
                    'amount_without_feed' => round($amount), 2,
                    'application_feed_amount' => round($application_feed, 2)
                ];

            } else {
                $amount = ($amount - $feed_amount);
                $application_feed = $feed_amount;

                return [
                    'amount_with_feed' => round($total, 2),
                    'amount_without_feed' => round($amount, 2),
                    'application_feed_amount' => round($application_feed, 2)
                ];
            }

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _purchaseStatus($uuid = null)
    {
        try {

            if (!$uuid) {
                throw new \Exception('uuid null');
            }

            $data = purchase_order::where('uuid', $uuid)
                ->get();

            return [
                'purchase' => $data->toArray()
            ];

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _checkBank($shop = null)
    {
        try {
            if (!$shop) {
                throw new \Exception('shop null');
            }

            $data = shop::where('shops.id', $shop)
                ->join('user_payments', 'user_payments.user_id', '=', 'shops.users_id')
                ->where('user_payments.primary', 1);

            if (!$data->exists()) {
                throw new \Exception('(' . $shop . ') shop payment not found');
            }

            $data = $data->first();

            return $data->toArray();


        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }
}
