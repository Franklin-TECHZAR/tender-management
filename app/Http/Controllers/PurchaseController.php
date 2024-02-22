<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Material;
use App\Models\Tender;
use App\Models\Vendor;
use App\Models\InvoicePurchase;
use App\Models\InvoiceProduct;
use App\Models\PurchaseType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PurchaseExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchase = new InvoicePurchase();
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $PurchaseTypes = PurchaseType::get();
        return view('purchase.index', compact('tenders', 'PurchaseTypes', 'purchase'));
    }

    public function create()
    {
        $purchase = new InvoicePurchase();
        $company_settings = CompanySetting::first();
        $vendors = Vendor::get();
        $materials = Material::get();
        $job_orders = Tender::where("job_order", 1)->where("status", 1)->get();
        // $ExpenseType = ExpenseType::get()->pluck('name');
        $PurchaseTypes = PurchaseType::get();
        return view('purchase.create_edit', compact('company_settings', 'vendors', 'materials', 'job_orders', 'PurchaseTypes', 'purchase'));
    }

    public function edit($id)
    {
        $company_settings = CompanySetting::first();
        $vendors = Vendor::get();
        $materials = Material::get();
        $job_orders = Tender::where("job_order", 1)->where("status", 1)->get();
        $purchase = InvoicePurchase::with('invoiceProduct')->findOrFail($id);
        $PurchaseTypes = PurchaseType::get();
        return view('purchase.create_edit', compact('company_settings', 'vendors', 'materials', 'job_orders', 'PurchaseTypes', 'purchase'));
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
            $purchase = InvoicePurchase::find($request->edit_id);
            $purchase->job_order_id = $request->job_order;
            $purchase->invoice_no = $request->invoice_no;
            $purchase->type = $request->type;
            $purchase->date = $request->date;
            $purchase->vendor_id = $request->vendor;
            $purchase->final_total = $request->final_total;
            $purchase->save();

            $purchase->invoiceProduct()->Delete();
            $message = "Purchase Updated Successfully";
        } else {
            $purchase = new InvoicePurchase();
            $purchase->job_order_id = $request->job_order;
            $purchase->invoice_no = $request->invoice_no;
            $purchase->type = $request->type;
            $purchase->date = $request->date;
            $purchase->vendor_id = $request->vendor;
            $purchase->final_total = $request->final_total;
            $purchase->save();
            $message = "Purchase Created Successfully";
        }

        foreach ($request->material as $key => $materialId) {
            $invoice = new InvoiceProduct();
            $invoice->invoice_purchase_id = $purchase->id;
            $invoice->material_id = $materialId;
            $invoice->quantity = $request->qty[$key];
            $invoice->unit = $request->unit[$key];
            $invoice->amount = $request->amount[$key];
            $invoice->gst = $request->gst[$key];
            $invoice->total = $request->total[$key];
            $invoice->save();
        }

        return array("status" => 1, "message" => $message);
    }


    public function fetch()
{
    $data = InvoicePurchase::with(['vendor:id,agency_name', 'material:id,name'])
        ->orderBy('id', 'DESC')
        ->get();
        $data->transform(function ($item) {
            $item->date = Carbon::parse($item->date)->format('d-m-Y');
            return $item;
        });
    return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('amount', function ($row) {
            return "<span class='pull-right'>₹" . number_format($row->amount, 2) . "</span>";
        })
        ->addColumn('total', function ($row) {
            return "<span class='pull-right'>₹" . number_format($row->total, 2) . " /-</span>";
        })
        ->addColumn('final_total', function ($row) {
            return "<span class='pull-right'>₹" . number_format($row->final_total, 2) . "</span>";
        })
        ->addColumn('vendor', function ($row) {
            return $row->vendor ? $row->vendor->agency_name : '';
        })
        ->addColumn('material', function ($row) {
            return $row->material ? $row->material->name : '';
        })
        ->addColumn('invoice_no', function ($row) {
            return $row->invoice_no;
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
        ->rawColumns(['action', 'amount', 'total', 'final_total'])
        ->make(true);
}


    public function fetch_edit($id)
    {
        $purchase = InvoicePurchase::with(['vendor:id,agency_name', 'material:id,name'])->find($id);
        return $purchase;
    }

    public function delete($id)
    {
        $purchase = InvoicePurchase::with('invoiceProduct')->find($id);

        if (!$purchase) {
            return array("status" => 0, "message" => "Purchase not found");
        }

        foreach ($purchase->invoiceProduct as $invoiceProduct) {
            $invoiceProduct->delete();
        }

        $purchase->delete();

        return array("status" => 1, "message" => "Purchase and associated invoices deleted successfully");
    }


    public function generatePDF($id)
    {
        $purchase = InvoicePurchase::findOrFail($id);
        $company_settings = CompanySetting::first();
        $job_orders = Tender::where("job_order", 1)->where("id", $purchase->job_order_id)->get();
        $Vendor = Vendor::where("id", $purchase->vendor_id)->first();
        $address = $company_settings->address;
        $mobile = $company_settings->mobile;
        $email = $company_settings->email;
        $name = $company_settings->name;
        $gst_number = $company_settings->gst_number;

        // Calculate total amount and total GST
        $total_amount = 0;
        $total_gst = 0;
        foreach ($purchase->invoiceProduct as $product) {
            $total_amount += $product->amount;
            $total_gst += ($product->amount * $product->gst) / 100;
        }

        $final_total = $purchase->final_total;

        $data = [
            'purchase' => $purchase,
            'job_orders' => $job_orders,
            'address' => $address,
            'mobile' => $mobile,
            'email' => $email,
            'name' => $name,
            'gst_number' => $gst_number,
            'Vendor' => $Vendor,
            'final_total' => $final_total,
            'total_amount' => $total_amount,
            'total_gst' => $total_gst,
        ];

        $pdf = PDF::loadView('pdf_export.purchase_receipt', $data);
        return $pdf->stream('purchase_receipt.pdf');
    }



    public function export(Request $request)
    {
        $query = InvoicePurchase::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('d-m-Y', strtotime($dates[0]));
            $end_date = date('d-m-Y', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $query->where('job_order', $request->job_order);
        }

        $purchases = $query->with('vendor', 'invoiceProduct.material')->get();

        if ($purchases->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $export_data = [];
        $total_amount = 0;
        $date_range = $request->date_range;

        foreach ($purchases as $purchase) {
            if (!$purchase->deleted_at) {
                foreach ($purchase->invoiceProduct as $invoiceProduct) {
                    if (!$invoiceProduct->deleted_at) {
                        $jobOrderName = Tender::find($purchase->job_order_id)->name;
                        $export_data[] = [
                            'S.No' => count($export_data) + 1,
                            'Job Order' => $jobOrderName,
                            'Type' => $purchase->type,
                            'Date' => date("d-m-Y", strtotime($purchase->date)),
                            'Invoice No' => $purchase->invoice_no,
                            'Vendor' => $purchase->vendor->agency_name,
                            'Product/Material' => $invoiceProduct->material->name,
                            'Quantity' => $invoiceProduct->quantity,
                            'Amount' => '₹' . number_format($invoiceProduct->amount, 2),
                            'GST' => $invoiceProduct->gst . '%',
                            'Total' => '₹' . number_format($invoiceProduct->total, 2),
                        ];

                        $total_amount += $invoiceProduct->total;
                    }
                }
            }
        }

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
            'date_range' =>  $date_range,
        ];

        return Excel::download(new PurchaseExport($data), 'purchase.xlsx');
    }
}
