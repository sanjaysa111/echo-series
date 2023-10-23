<?php

use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

class Order {
    public function __construct(public int $id, public int $amount) {}
}

Route::get('/', function () {
//    OrderStatusUpdated::dispatch(); //event(new OrderStatusUpdated());
//    OrderStatusUpdated::dispatch(new Order(1, 599)); //event(new OrderStatusUpdated(new Order(1, 599)));

    return view('welcome');
});

Route::get('/update', function() {
    OrderStatusUpdated::dispatch(new Order(1, 599)); //event(new OrderStatusUpdated());
});
