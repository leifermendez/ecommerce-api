<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\_SalesReport;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use App\shop;
use App\User;

class SalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:ventas {email}, {start=off}, {end=off}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de ventas.';

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
        $shops = ($user) ? $user->shops : shop::where('email_corporate' ,$this->argument('email'))->get();
        $toUser = ($user) ? $user : $shops[0]->user;

        if (count($shops) > 0) {
            $pdf = PDF::loadView('pdf.sale', compact(['shops','range', 'desde', 'hasta']))->output();
            Storage::disk()->put(('public/PDF/'.$toUser->id.'_sale.pdf'), $pdf);
            $path = Storage::disk()->url(('public/PDF/'.$toUser->id.'_sale.pdf'));            
            $toUser->notify(new _SalesReport($path));
            $this->info('Reporte Enviado');
        }else{
            $this->error('El email ingresado no existe!');
            return false;
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
