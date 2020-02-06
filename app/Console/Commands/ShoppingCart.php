<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShoppingCartReminder;
use App\shopping_cart;
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
    protected $signature = 'notifications:shoppingcart';

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
                $users[$value->user_id][$value->product_id] = $fecha;
            }else{
                $users[$value->user_id] = [$value->product_id => $fecha];
            }
        }
        foreach ($users as $key => $value) {
            $fecha = max($value);
            if ($this->validate($fecha, $fecha, $fecha)) {
                $user = User::find($key);
                $correo['user'] = $user;
                $correo['products'] = $this->getProducts($value);
                Mail::to($user->email)->send(new ShoppingCartReminder($correo));
            }
        }
    }

    public function validate($first, $second, $third){
        $today       = Carbon::now()->format('d-m-Y');
        $firstEmail  = $first->addDays(2)->format('d-m-Y');
        $secondEmail = $second->addDays(4)->format('d-m-Y');
        $thirdEmail  = $third->addDays(6)->format('d-m-Y');

        if (($today == $firstEmail) || ($today == $secondEmail) ||  ($today == $thirdEmail)) {
            return true;
        }
        return false;
    }

    public function getProducts($productos){
        $products = [];
        foreach ($productos as $key => $value) {
            $product = products::find($key);
            array_push($products, $product);
        }
        return $products;
    }
}
