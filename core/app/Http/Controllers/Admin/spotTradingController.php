<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpotTransactionCharge;
use Illuminate\Http\Request;

class SpotTransactionChargeController extends Controller
{
    public function manageCharges()
    {
        $pageTitle = "Spot Transaction Charges";
        return view('admin.spotTrading',compact('pageTitle'));
    }
}