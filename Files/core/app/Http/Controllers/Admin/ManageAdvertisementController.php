<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;

class ManageAdvertisementController extends Controller
{
    public function index()
    {
        $pageTitle      = 'Advertisements';
        $search         = request()->search;
        $advertisements = Advertisement::query();

        if ($search) {
            $advertisements = $advertisements->whereHas('user', function ($ad) use ($search) {
                $ad->active()->where('username', 'like', "%$search%");
            });
        }

        $advertisements = $advertisements->latest()->with(['fiat', 'fiatGateway', 'crypto', 'user', 'user.wallets'])->latest()->paginate(getPaginate());
        return view('admin.advertisement.index', compact('pageTitle', 'advertisements'));
    }

    function updateStatus($id)
    {
        $advertisement = Advertisement::findOrFail($id);

        if ($advertisement->status == 1) {
            $advertisement->status = 0;
            $notification = 'Advertisement disabled successfully';
        } else {
            $advertisement->status = 1;
            $notification = 'Advertisement enabled successfully';
        }

        $advertisement->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
