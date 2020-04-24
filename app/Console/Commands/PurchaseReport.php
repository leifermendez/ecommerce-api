<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Notifications\_PurchaseReport;
use Barryvdh\DomPDF\Facade as PDF;
use App\purchase_order;
use Carbon\Carbon;
use App\User;

class PurchaseReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:compra {email}, {start=off}, {end=off}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de Compra';

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
        $range = $this->getRangeDate($this->argument('start'), $this->argument('end'));
        $desde = ($range[0]->format('d-m-Y'));
        $hasta = ($range[1]->format('d-m-Y'));

        $user = user::where('email' ,$this->argument('email'))->first();

        if ($user) {
            $orders = purchase_order::where('status','success')->where('user_id',$user->id)->whereBetween('updated_at', $range)->get();
            $pdf = PDF::loadView('pdf.purchase', compact(['user', 'orders','range', 'desde', 'hasta']))->output();
            Storage::disk()->put(('public/PDF/'.$user->id.'_purchase.pdf'), $pdf);
            $path = Storage::disk()->url(('public/PDF/'.$user->id.'_purchase.pdf'));
            $user->notify(new _PurchaseReport($path));
            $this->info('Reporte Enviado');           
        }else{
            $this->error('El email ingresado no existe!');
        }
    }

    public function getRangeDate($start, $end){
        $range = [];
        if (($start == 'off') || ($end == 'off')) {
            $start = new Carbon('first day of last month 00:00:00');
            $end = new Carbon('last day of last month 23:59:59');
        }else{
            $start = Carbon::create($start);
            $end   = Carbon::create($end.' 23:59:59');
        }
        $range[0] = $start;
        $range[1] = $end;
        return $range;   
    }
}
