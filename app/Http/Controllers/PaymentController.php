<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Labour;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{

    public function create()
    {
        $tenders = Tender::where('job_order', 1)
                         ->where('status', 1)
                         ->pluck('name');
        $Labour = Labour::get('name');
        return view('payment.purchase_dept', compact('tenders','Labour'));
    }
}
