<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte de Ventas</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="py-1">
            <div >
                <div class="row">
                    <div class="col-md-12">
                    @foreach($shops as $shop)
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="float-left">
                                    <h4>{{$shop->name}}</h4>
                                </div>
                                <div class="float-right">Reporte de ventas: del 01-01-2020  hasta 02-02-2020</div>
                                <br><br>

                                @if(count($shop->purchase_orders()->where('status','success')->whereBetween('updated_at', $range)->get()) > 0)

                                    @foreach($shop->purchase_orders()->where('status','success')->whereBetween('updated_at', $range)->get() as $order)
                                        <br>
                                        Venta
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                  <th WIDTH="60">Productos</th>
                                                  <th WIDTH="7">Cantidad</th>
                                                  <th WIDTH="7">Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->purchase_details as $detail)
                                                    <tr>
                                                        <td>{{$detail->product_label}}</td>
                                                        <td>{{$detail->product_qty}}</td>
                                                        <td>{{$detail->product_amount}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table">
                                            <tbody>                                
                                                <tr>
                                                    <td class="text-right"><strong>Feed:</strong> {{$order->feed}}</td>
                                                    <td class="text-right"><strong>Total:</strong> {{$order->amount}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endforeach 
                                @else
                                    <div class="text-center">
                                        <h4>No existen Ventas para estas fechas</h4>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>