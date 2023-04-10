<?php

namespace App\Http\Controllers\Admin;

use App\Models\Deposit;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{
    public function deposit()
    {
        $pageTitle = 'Deposit History';
        $depositData = $this->depositData();
        $deposits = $depositData['data'];
//         echo "<pre>";
//         print_r($depositData);
//         echo "</pre>";
// exit;
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    protected function depositData()
    {
        $deposits = Deposit::with(['user']);

        $request = request();
        //search
        if ($request->search) {
            $search = request()->search;
            $deposits = $deposits->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%$search%")->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%");
                });
            });
        }
        
        //date search
        $deposits = $deposits->dateFilter();

        return [
            'data' => $deposits->orderBy('id','desc')->with('crypto')->paginate(getPaginate()),
        ];
    }

    public function details($id)
    {
        $general   = gs();
        $deposit   = Deposit::where('id', $id)->with(['user', 'crypto'])->firstOrFail();
        $pageTitle = $deposit->user->username.' Deposit requested ' . showAmount($deposit->amount,8) . ' '.$deposit->crypto->code;

        return view('admin.deposit.detail', compact('pageTitle', 'deposit'));
    }
}
