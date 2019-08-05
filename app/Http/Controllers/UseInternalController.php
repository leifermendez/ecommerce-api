<?php

namespace App\Http\Controllers;

use App\attached_products;
use App\hours;
use App\product_attached;
use App\product_categories;
use App\purchase_detail;
use App\settings;
use App\user_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use App\products;
use App\variation_product;
use App\purchase_order;
use App\shop;
use App\comments;
use App\shopping_cart;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;


class UseInternalController extends Controller
{
    protected $_orm;

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

    public function _detailPurchase($uuid = null, $num = null)
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
                ->take($num)
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

    public function _isAvailableUser($id = null)
    {
        try {
            if (!$id) {
                throw new \Exception('id null');
            }

            if (!User::where('id', $id)
                ->where('status', 'available')
                ->exists()) {
                throw new \Exception('user not found');
            }

            $data = User::where('id', $id)->first();

            if (!$data->confirmed) {
                throw new \Exception('user_not_confirmed');
            }

            return ['data' => $data];

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

            $now = Carbon::now();
            $next_available = null;
            $next_close = null;
            $diff = 0;
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

                if ($hours_shedule_hours) {
                    $openingHours = OpeningHours::create([
                        'monday' => (isset($hours_shedule_hours->monday)) ? $hours_shedule_hours->monday : [],
                        'tuesday' => (isset($hours_shedule_hours->tuesday)) ? $hours_shedule_hours->tuesday : [],
                        'wednesday' => (isset($hours_shedule_hours->wednesday)) ? $hours_shedule_hours->wednesday : [],
                        'thursday' => (isset($hours_shedule_hours->thursday)) ? $hours_shedule_hours->thursday : [],
                        'friday' => (isset($hours_shedule_hours->friday)) ? $hours_shedule_hours->friday : [],
                        'saturday' => (isset($hours_shedule_hours->saturday)) ? $hours_shedule_hours->saturday : [],
                        'sunday' => (isset($hours_shedule_hours->sunday)) ? $hours_shedule_hours->sunday : [],
                        'exceptions' => $hours_exceptions
                    ]);
                    $next_available = $openingHours->nextOpen(Carbon::now());
                    $diff = Carbon::parse($next_available)->diffInMinutes($now);
                    $next_available = Carbon::parse($next_available)->toArray();
                    $next_close = $openingHours->nextClose(Carbon::now());
                    $next_close = Carbon::parse($next_close)->toArray();
                    $shedule = $openingHours->isOpenAt(Carbon::now());

                }

                return [
                    'isAvailable' => $shedule,
                    'nextOpen' => $next_available,
                    'nextClose' => $next_close,
                    'minutes' => ($diff === 0) ? ($diff + 1) : $diff
                ];

            };

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _v2_isAvailableProduct($schedule = null, $exceptions = null)
    {
        try {

            $now = Carbon::now();
            $next_available = null;
            $next_close = null;
            $diff = 0;
            $shedule = array();
            $exceptions = array();

            if (!$schedule) {
                return [
                    'isAvailable' => false,
                    'nextOpen' => false,
                    'nextClose' => false,
                ];
            }


            if ($schedule) {
                $hours_shedule_hours = ($schedule && json_decode($schedule)) ?
                    json_decode($schedule) : null;

                $hours_exceptions = ($exceptions && json_decode($exceptions)) ?
                    json_decode($exceptions) : null;

                if ($hours_shedule_hours) {
                    $openingHours = OpeningHours::create([
                        'monday' => (isset($hours_shedule_hours->monday)) ? $hours_shedule_hours->monday : [],
                        'tuesday' => (isset($hours_shedule_hours->tuesday)) ? $hours_shedule_hours->tuesday : [],
                        'wednesday' => (isset($hours_shedule_hours->wednesday)) ? $hours_shedule_hours->wednesday : [],
                        'thursday' => (isset($hours_shedule_hours->thursday)) ? $hours_shedule_hours->thursday : [],
                        'friday' => (isset($hours_shedule_hours->friday)) ? $hours_shedule_hours->friday : [],
                        'saturday' => (isset($hours_shedule_hours->saturday)) ? $hours_shedule_hours->saturday : [],
                        'sunday' => (isset($hours_shedule_hours->sunday)) ? $hours_shedule_hours->sunday : [],
                        'exceptions' => $hours_exceptions
                    ]);
                    $next_available = $openingHours->nextOpen(Carbon::now());
                    $diff = Carbon::parse($next_available)->diffInMinutes($now);
                    $next_available = Carbon::parse($next_available)->toArray();
                    $next_close = $openingHours->nextClose(Carbon::now());
                    $next_close = Carbon::parse($next_close)->toArray();
                    $shedule = $openingHours->isOpenAt(Carbon::now());

                }

                return [
                    'isAvailable' => $shedule,
                    'nextOpen' => $next_available,
                    'nextClose' => $next_close,
                    'minutes' => ($diff === 0) ? ($diff + 1) : $diff
                ];

            };

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _getLabels($product_id = null)
    {
        try {
            $tmp = [];
            if (!$product_id) {
                throw new \Exception('id null');
            }
            $exists = false;
            $data = product_attached::where('product_attacheds.product_id', $product_id)
                ->join('attacheds', 'product_attacheds.attached_id', '=', 'attacheds.id')
                ->join('labels_products', 'labels_products.attacheds_id', '=', 'attacheds.id')
                ->select('labels_products.labels')
                ->get();
            
            if($data){
                foreach ($data as $key => $value) {
                    $tmp[] = $value->labels;
                }
                $tmp = implode(',',$tmp);
               $encrypted_label = Crypt::encryptString($tmp);
            }
            if(strlen($tmp)){
                $string_label = str_replace(",", "%' OR products.label LIKE '%", $tmp);
                $exists = products::whereRaw("(products.label LIKE '%$string_label%')")
                ->exists();
            }

            return [
                'exists' => $exists,
                'label' => $encrypted_label,
                'string' => $tmp
            ];

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

            $data = product_attached::where('product_attacheds.product_id', $id)
                ->join('attacheds', 'product_attacheds.attached_id', '=', 'attacheds.id')
                ->select('attacheds.*', 'product_attacheds.product_id as product_id',
                    'attacheds.large as big')
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
        try {
            if (!$id) {
                throw new \Exception('id null');
            }

            $data = comments::where('shop_id', $id)
                ->sum('score');
            $count = comments::where('shop_id', $id)
                ->count();

            return [
                'score' => $data,
                'count' => $count
            ];

        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _parseVariation($string = null)
    {

        if ($string) {
            $tmp_explode = explode('|', $string);
            $tmp = [];
            foreach ($tmp_explode as $value) {
                if (json_decode($value, true)) {
                    $tmp[] = json_decode($value, true);
                }
            }

            return $tmp;
        }
    }

    public function _getVariations($id = null, $sort = 'ASC', $limit = null)
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
                ->where('variation_products.status', 'available')
                ->join('product_categories', 'product_categories.product_id', '=', 'variation_products.product_id')
                ->join('categories', 'categories.id', '=', 'product_categories.category_id')
                ->select('variation_products.*',
                    'categories.id as categories_id',
                    DB::raw('(SELECT attacheds.small FROM attacheds 
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_small'),
                    DB::raw('(SELECT attacheds.medium FROM attacheds 
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_medium'),
                    DB::raw('(SELECT attacheds.large FROM attacheds 
                    WHERE attacheds.id = variation_products.attached_id limit 1) as attacheds_large')
                )
                ->orderBy('variation_products.price_normal', $sort);

            $data = ($limit) ? $data->take($limit)->get() : $data->get();

            $data->map(function ($item, $key) use ($id) {

                $getMediaVariatons = product_attached::where('variation_product_id', $id)
                    ->get();
                $item->gallery = $getMediaVariatons;
                return $item;
            });

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
            $discount_to_supplier = $this->_getSetting('discount_to_supplier');
            $data = [];

            if (!$uuid) {
                throw new \Exception('uuid null');
            }

            if (!purchase_order::where('uuid', $uuid)->exists()) {
                throw new \Exception('not found');
            }
            $sql = ($discount_to_supplier == 1) ? 'amount + amount_shipping ' : 'amount + amount_shipping + feed';
            $data = purchase_order::where('uuid', $uuid)
                ->select(DB::raw('SUM(amount) as total_products'),
                    DB::raw('SUM(amount_shipping) as total_shipping'),
                    DB::raw('SUM(feed) as total_feed'),
                    DB::raw('SUM(' . $sql . ') as total'))
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

    public function _checkBank($shop = null, $exception = true)
    {
        if ($exception) {
            try {
                if (!$shop) {
                    throw new \Exception('shop null');
                }

                $data = shop::disableCache()
                    ->where('shops.id', $shop)
                    ->join('user_payments', 'user_payments.user_id', '=', 'shops.users_id')
                    ->where('user_payments.primary', 1);

                if (!$data->exists()) {
                    throw new \Exception('shop payment not found');
                }

                $data = $data->first();

                return $data->toArray();


            } catch (\Execption $e) {
                return $e->getMessage();
            }
        } else {
            $data = shop::disableCache()
                ->where('shops.id', $shop)
                ->join('user_payments', 'user_payments.user_id', '=', 'shops.users_id')
                ->where('user_payments.primary', 1);

            return $data->exists();
        }
    }

    public function _checkSchedule($shop = null, $exception = true)
    {
        if ($exception) {
            try {
                if (!$shop) {
                    throw new \Exception('shop null');
                }

                $data = hours::where('shop_id', $shop);

                if (!$data->exists()) {
                    throw new \Exception('shop payment not found');
                }

                $data = $data->first();

                return $data->toArray();


            } catch (\Execption $e) {
                return $e->getMessage();
            }
        } else {
            $data = hours::where('shop_id', $shop)->exists();
            return $data;
        }
    }

    public function _isLogged()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return false;
            } else if ($user->status !== 'available') {
                return false;
            }
        } catch (TokenExpiredException $e) {

            return false;
        } catch (TokenInvalidException $e) {
            return false;
        } catch (JWTException $e) {

            return false;
        }

        return $user;
    }

    public function _measureShop(
        $lat = null, $lng = null,
        $km = 10, $operation = '<', $select = 'distance_in_km')
    {

        try {
            if (!$lat) {
                return [];
            }

            if (!$lng) {
                return [];
            }

            $data = DB::select(
                DB::raw('SELECT ' . $select . ' FROM(
                SELECT a.`name` as name_shop, a.id AS shop_id,
                111.111 *
                DEGREES(ACOS(LEAST(COS(RADIANS(a.lat))
                * COS(RADIANS(' . $lat . '))
                * COS(RADIANS(a.lng - ' . $lng . '))
                + SIN(RADIANS(a.lat))
                * SIN(RADIANS(' . $lat . ')), 1.0))) AS distance_in_km
                FROM shops as a) as b WHERE b.distance_in_km ' . $operation . ' ' . $km)
            );
            return $data;
        } catch (\Execption $e) {
            return [];
        }


    }

    public function _isMyProduct($id = null)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $isMy = products::where('products.id', $id)
                ->join('shops', 'shops.id', '=', 'products.shop_id')
                ->where('shops.users_id', $user->id)
                ->exists();
            return $isMy;
        } catch (\Execption $e) {
            return false;
        }
    }

    public function _isMyShop($id = null)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $isMy = shop::where('shops.id', $id)
                ->join('users', 'users.id', '=', 'shops.users_id')
                ->where('users.id', $user->id)
                ->first();
            return $isMy;
        } catch (\Execption $e) {
            return false;
        }
    }

    public function _getCoverImageProduct($id = null)
    {
        try {
            $data = product_attached::where('product_attacheds.product_id', $id)
                ->whereNull('product_attacheds.variation_product_id')
                ->join('attacheds', 'product_attacheds.attached_id', '=', 'attacheds.id')
                ->select('attacheds.*')
                ->first();
            return $data;
        } catch (\Execption $e) {
            return false;
        }
    }

    public function _getCategories($id = null)
    {
        try {
            if (!$id) {
                throw new \Exception('id product null');
            }
            $data = product_categories::where('product_categories.product_id', $id)
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->select('categories.*')
                ->get();
            return $data;
        } catch (\Execption $e) {
            return $e->getMessage();
        }
    }

    public function _sumList($list = array(), $key = null)
    {
        $total = 0;
        foreach ($list as $l) {
            $total += $l['feed_amount']['application_feed_amount'];
        }
        return $total;
    }

    public function _getAttribute($orm)
    {
        $this->_orm = $orm;
        return $this->_orm;
    }
}
