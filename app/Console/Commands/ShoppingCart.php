<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\_CartShopping;
use App\variation_product;
use App\shopping_cart;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use App\products;
use App\User;


class ShoppingCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:shopping-cart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar Email de Recordatorio de carrito de compras';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = [];
        $datos = shopping_cart::all();
        foreach ($datos as $value) {
            $fecha = ($value->created_at);
            if (array_key_exists($value->user_id, $users)) {
                $users[$value->user_id][$value->product_variation_id] = $fecha;
            }else{
                $users[$value->user_id] = [$value->product_variation_id => $fecha];
            }
        }
        foreach ($users as $key => $value) {
            $date = explode("-", (max($value))->format('d-m-Y'));
            $fecha = CarbonImmutable::createFromDate($date['2'],$date['1'],$date['0']);
            
            if ($this->validate($fecha)) {
                $user = User::find($key);
                $datos['user'] = $user;
                $datos['products'] = $this->getProducts($value);
                $user->notify(new _CartShopping($datos));
            }
        }
        $this->info('Email enviados');
    }
    public function validate($fecha){
        $today       = Carbon::now()->format('d-m-Y');
        $firstEmail  = $fecha->addDays(2)->format('d-m-Y');
        $secondEmail = $fecha->addDays(4)->format('d-m-Y');
        $thirdEmail  = $fecha->addDays(6)->format('d-m-Y');

        if (($today == $firstEmail) || ($today == $secondEmail) ||  ($today == $thirdEmail)) {
            return true;
        }
        return false;
    }

    public function getProducts($variationProductos){
        $products = [];
        foreach ($variationProductos as $key => $value) {
            $vProduct = variation_product::find($key);
            $product = products::find($vProduct->product_id);
            $url =  env('APP_URL').'/api/1.0/rest/products-variations/'. $key;
            array_push($products, ['name' => $product->name, 'price'=>$vProduct->price_normal, 'url' => $url ]);
        }
        return $products;
    }
}
