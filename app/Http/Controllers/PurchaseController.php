<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Vendor;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        return view('purchase.index');
    }

    public function create()
    {
        $company_settings = CompanySetting::first();
        $vendors = Vendor::get();
        return view('purchase.create_edit', compact('company_settings', 'vendors'));
    }
}
