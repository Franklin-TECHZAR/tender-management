<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Material;
use App\Models\Tender;
use App\Models\Vendor;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchase.index');
    }

    public function create()
    {
        $company_settings = CompanySetting::first();
        $vendors = Vendor::get();
        $materials = Material::get();
        $job_orders = Tender::where("job_order" , 1)->where("status", 1)->get();
        return view('purchase.create_edit', compact('company_settings', 'vendors', 'materials', 'job_orders'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'vendor' => 'required',
            "material.*" => 'required',
            "qty.*" => 'required',
            "unit.*" => 'required',
            "amount.*" => 'required',
            "gst.*" => 'required',
            "total.*" => 'required',
        ]);
    }
}
