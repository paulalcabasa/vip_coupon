<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CouponType;

class CouponTypeController extends Controller
{
    public function index(){
        return CouponType::all();
    }
}
