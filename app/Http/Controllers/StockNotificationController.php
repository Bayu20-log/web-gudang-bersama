<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockNotificationController extends Controller
{
    public function index()
{
    return view('stock-notifications.index');
}
}
