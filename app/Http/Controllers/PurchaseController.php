<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Material;
use App\Models\Tender;
use App\Models\Vendor;
use App\Models\Purchase;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PurchaseExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class PurchaseController extends Controller
{
    public function index()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        return view('purchase.index', compact('tenders'));
    }

    public function create()
    {
        $purchase = new Purchase();
        $company_settings = CompanySetting::first();
        $vendors = Vendor::get();
        $materials = Material::get();
        $job_orders = Tender::where("job_order", 1)->where("status", 1)->get();
        $ExpenseType = ExpenseType::get()->pluck('name');
        return view('purchase.create_edit', compact('company_settings', 'vendors', 'materials', 'job_orders', 'ExpenseType', 'purchase'));
    }

    public function edit($id)
    {
        $company_settings = CompanySetting::first();
        $vendors = Vendor::get();
        $materials = Material::get();
        $job_orders = Tender::where("job_order", 1)->where("status", 1)->get();
        $purchase = Purchase::findOrFail($id);
        $ExpenseType = ExpenseType::get()->pluck('name');
        return view('purchase.create_edit', compact('company_settings', 'vendors', 'materials', 'job_orders', 'ExpenseType', 'purchase'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'job_order' => 'required',
            'invoice_no' => 'required',
            'type' => 'required',
            'date' => 'required|date',
            'vendor' => 'required',
            'material.*' => 'required',
            'qty.*' => 'required',
            'unit.*' => 'required',
            'amount.*' => 'required',
            'gst.*' => 'required',
            'total.*' => 'required',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_order' => 'required',
            'invoice_no' => 'required',
            'type' => 'required',
            'date' => 'required|date',
            'vendor' => 'required',
            'material.*' => 'required',
            'qty.*' => 'required',
            'unit.*' => 'required',
            'amount.*' => 'required',
            'gst.*' => 'required',
            'total.*' => 'required',
        ]);

        if ($request->edit_id) {
            $purchase = Purchase::find($request->edit_id);
            $message = "Purchase Updated Successfully";
        } else {
            $purchase = new Purchase();
            $message = "Purchase Created Successfully";
        }
        $purchase->job_order_id = $request->job_order;
        $purchase->invoice_no = $request->invoice_no;
        $purchase->type = $request->type;
        $purchase->date = $request->date;
        $purchase->vendor_id = $request->vendor;
        $purchase->material_id = $request->material[0];
        $purchase->quantity = $request->qty[0];
        $purchase->unit = $request->unit[0];
        $purchase->amount = $request->amount[0];
        $purchase->gst = $request->gst[0];
        $purchase->total = $request->total[0];
        $purchase->save();

        for ($i = 1; $i < count($request->material); $i++) {
            $purchase = new Purchase();
            $purchase->job_order_id = $request->job_order;
            $purchase->invoice_no = $request->invoice_no;
            $purchase->type = $request->type;
            $purchase->date = $request->date;
            $purchase->vendor_id = $request->vendor;
            $purchase->material_id = $request->material[$i];
            $purchase->quantity = $request->qty[$i];
            $purchase->unit = $request->unit[$i];
            $purchase->amount = $request->amount[$i];
            $purchase->gst = $request->gst[$i];
            $purchase->total = $request->total[$i];
            $purchase->save();
        }

        return array("status" => 1, "message" => $message);
    }


    public function fetch()
    {
        $data = Purchase::with(['vendor:id,agency_name', 'material:id,name'])
            ->orderBy('id', 'DESC')
            ->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('amount', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->amount, 2) . "</span>";
            })
            ->addColumn('total', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->total, 2) . " /-</span>";
            })
            ->addColumn('vendor', function ($row) {
                return $row->vendor ? $row->vendor->agency_name : '';
            })
            ->addColumn('material', function ($row) {
                return $row->material ? $row->material->name : '';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="dropdown">
                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="dw dw-more"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                    <a href="/generatePurchase-pdf/' . $row->id . '" class="dropdown-item" target="_blank"><i class="dw dw-download"></i> Download Receipt</a>
                                    <a href="/purchase/create/' . $row->id . '" class="dropdown-item"><i class="dw dw-edit2"></i> Edit</a>
                                    <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                                    </div>
                                    </div>';
                return $btn;
            })
            ->rawColumns(['action', 'amount', 'total'])
            ->make(true);
    }

    public function fetch_edit($id)
    {
        $purchase = Purchase::with(['vendor:id,agency_name', 'material:id,name'])->find($id);
        return $purchase;
    }


    public function delete($id)
    {
        Purchase::find($id)->delete();

        return array("status" => 1, "message" => "Purchase deleted successfully");
    }

    public function generatePDF($id)
    {
        $purchase = Purchase::findOrFail($id);
        $company_settings = CompanySetting::first();
        $job_orders = Tender::where("job_order", 1)->where("id", $purchase->job_order_id)->get();
        $address = $company_settings->address;
        $mobile = $company_settings->mobile;
        $email = $company_settings->email;
        $name = $company_settings->name;
        $data = [
            'purchase' => $purchase,
            'job_orders' => $job_orders,
            'address' => $address,
            'mobile' => $mobile,
            'email' => $email,
            'name' => $name,
        ];
        $pdf = PDF::loadView('pdf_export.purchase_receipt', $data);
        return $pdf->stream('purchase_receipt.pdf');
    }

    public function export(Request $request)
    {
        $query = Purchase::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $query->where('job_order_id', $request->job_order);
        }

        $purchase = $query->get();

        if ($purchase->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $total_amount = $purchase->sum('total');

        $export_data = $purchase->map(function ($purchase, $index) {
            $jobOrderName = Tender::find($purchase->job_order_id)->name;
            return [
                'S.No' => $index + 1,
                'Job Order' => $jobOrderName,
                'Type' => $purchase->type,
                'Date' => date("d-m-Y", strtotime($purchase->date)),
                'Invoice No' => $purchase->invoice_no,
                'Vendor' => $purchase->vendor->agency_name,
                'Product/Material' => $purchase->material->name,
                'Quantity' => $purchase->quantity,
                'Amount' => '₹' . number_format($purchase->amount, 2),
                'GST' => $purchase->gst . '%',
                'Total' => '₹' . number_format($purchase->total, 2),
            ];
        });

        $total_amount = "₹" . number_format($total_amount, 2);

        $export_data[] = [
            'S.No' => '',
            'Job Order' => '',
            'Type' => '',
            'Date' => '',
            'Invoice No' => '',
            'Vendor' => '',
            'Product/Material' => '',
            'Quantity' => '',
            'Amount' => '',
            'GST' => '',
            'Total' => $total_amount,
        ];

        $data = [
            'view_file' => 'excel_export.purchase_export',
            'export_data' => $export_data,
        ];

        return Excel::download(new PurchaseExport($data), 'purchase.xlsx');
    }

}
