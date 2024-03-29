<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\VendorPayment;
use App\Models\VendorLog;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use App\Models\InvoicePurchase;
use App\Models\Vendor;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VendorPaymentController extends Controller
{

    public function create()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $vendors = Vendor::get()->pluck('agency_name', 'id');
        return view('vendor_payment.vendor', compact('tenders', 'vendors'));
    }


    public function store(Request $request)
    {
        if ($request->has('data') && !empty($request->input('data'))) {
            $this->validate($request, [
                'data.*.agency_name' => 'required',
                'data.*.gst_number' => 'required',
                'data.*.out_standing' => 'required|string',
            ]);

            foreach ($request->input('data') as $rowData) {
                $outStanding = str_replace('₹', '', $rowData['out_standing']);

                $existingVendor = VendorPayment::where('vendor_id', $rowData['id'])->first();

                if (!$existingVendor) {
                    VendorPayment::create([
                        'vendor_id' => $rowData['id'],
                        'vendor_name' => $rowData['agency_name'],
                        'gst_number' => $rowData['gst_number'],
                        'out_standing' => $outStanding,
                    ]);
                } else {
                    $existingVendor->update([
                        'out_standing' => $outStanding,
                    ]);
                }
            }

            return response()->json(["status" => 1, "message" => "Data stored successfully"]);
        } else {
            return response()->json(["status" => 0, "message" => "No data provided"]);
        }
    }


    public function fetch()
    {
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        $main_array = [];

        foreach ($vendors as $vendor) {
            $vendor_id = $vendor->id;
            $VendorPayment = VendorPayment::where('vendor_id', $vendor_id)->first();

            if ($VendorPayment) {
                $VendorPayment_id = $VendorPayment->id;

                $total_credit = VendorLog::where('vendor_balance_id', $VendorPayment_id)
                    ->where('type', 'Credit')
                    ->sum('amount');

                $total_invoice_amount = InvoicePurchase::where('vendor_id', $vendor_id)
                    ->sum('final_total');

                $total_debit = VendorLog::where('vendor_balance_id', $VendorPayment_id)
                    ->where('type', 'Debit')
                    ->sum('amount');

                $balance = $total_credit + $total_invoice_amount - $total_debit;

                $data = [
                    'vendor_id' => $vendor->id,
                    'agency_name' => $vendor->agency_name,
                    'gst_number' => $vendor->gst_number,
                    'VendorPayment_id' => $VendorPayment_id,
                    'balance' => $balance,
                ];
            } else {
                $data = [
                    'vendor_id' => $vendor->id,
                    'agency_name' => $vendor->agency_name,
                    'gst_number' => $vendor->gst_number,
                    'VendorPayment_id' => null,
                    'balance' => 0,
                ];
            }

            $main_array[] = [
                'data' => [$data],
            ];
        }

        return response()->json(['main_array' => $main_array]);
    }




    //     return DataTables::of($data)
    //         ->addIndexColumn()
    //         ->addColumn('out_standing', function ($row) {
    //             return $row->out_standing;
    //         })
    //         ->addColumn('action', function ($row) {
    //             $btn = '<div class="dropdown">
    //                             <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
    //                                 <i class="dw dw-more"></i>
    //                             </a>
    //                             <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
    //                             <button data-id="' . $row->id . '" data-vendor-id="' . $row->id . '" class="payments-btn dropdown-item"><i class="bi bi-cash-stack"></i> Payments</button>
    //                             </div>
    //                         </div>';
    //             return $btn;
    //         })
    //         ->rawColumns(['action', 'out_standing'])
    //         ->make(true);
    // }


    public function fetch_edit($id)
    {
        $vendor = VendorPayment::find($id);
        return $vendor;
    }

    public function delete($id)
    {
        VendorPayment::find($id)->delete();

        return array("status" => 1, "message" => "Payments deleted successfully");
    }


    public function payments($id)
    {
        // dd($id);
        $vendor_payment = Vendor::find($id);
        $job_order = $vendor_payment->job_order_id;

        $tender = Tender::find($job_order);

        $tender_name = $tender ? $tender->name : 'Unknown Tender';

        // $vendor_for = VendorPayment::pluck('payment_for', 'id');

        return view('vendor_payment.payments', compact('vendor_payment', 'tender_name'));
    }

    public function payment_store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'amount' => 'required',
            'payment_mode' => 'required',
            'payment_details' => 'required',
            'description' => 'required',
        ]);

        $vendor_log = new VendorLog();
        $vendor_log->vendor_balance_id  = $request->vendor_balance_id;
        $vendor_log->job_order_id  = $request->job_order;
        $vendor_log->date = $request->date;
        $vendor_name = Vendor::where('id', $vendor_log->vendor_balance_id)->pluck('agency_name');
        $vendor_log->payment_for = $vendor_name;
        $vendor_log->amount = $request->amount;
        $vendor_log->type = 'Debit';
        $vendor_log->payment_mode = $request->payment_mode;
        $vendor_log->payment_details = $request->payment_details;
        $vendor_log->description = $request->description;
        $vendor_log->save();

        return response()->json(['status' => 1, 'message' => 'Payment Created Successfully']);
    }


    public function fetch_payment_log(Request $request)
    {
        $this->validate($request, [
            'vendor_balance_id' => 'required',
        ]);

        $vendor_balance_id = $request->vendor_balance_id;
        $payment_logs = VendorLog::where('vendor_balance_id', $vendor_balance_id)->orderBy('date', "ASC")->get()->groupBy('payment_for');

        $main_array = [];

        foreach ($payment_logs as $index => $logs) {

            if (count($logs)) {
                $balance = 0;
                $total_credit = 0;
                $total_debit = 0;
                $data = [];
                foreach ($logs as $log) {
                    $credit = "";
                    $debit = "";
                    if ($log->type == "Credit") {
                        $credit = "<span class='pull-right'>₹" . number_format($log->amount, 2) . "</span>";
                        $balance = $balance + $log->amount;
                        $total_credit = $total_credit + $log->amount;
                    } else {
                        $debit = "<span class='pull-right'>₹" . number_format($log->amount, 2) . "</span>";
                        $balance = $balance - $log->amount;
                        $total_debit = $total_debit + $log->amount;
                    }

                    $symbol = '';
                    $temp_balance = $balance;
                    if ($temp_balance < 0) {
                        $temp_balance = - ($temp_balance);
                        $symbol = "-";
                    }
                    $balance_text = "<b class='pull-right'>" . $symbol . "₹" . number_format($temp_balance, 2) . "</b>";
                    $data[] = array('id' => $log->id, "date" => date("d-m-Y", strtotime($log->date)), "description" => $log->description, "credit" => $credit, "debit" => $debit, "balance" => $balance_text, 'amount_for' => $log->amount_for);
                }

                $total_debit = "<b class='pull-right'>₹" . number_format($total_debit, 2) . "</b>";
                $total_credit = "<b class='pull-right'>₹" . number_format($total_credit, 2) . "</b>";

                $data[] = array("description" => "<b class='pull-right'>Total</b>", "credit" => $total_credit, "debit" => $total_debit, "balance" => $balance_text, 'amount_for' => $log->amount_for);

                $main_array[] = array("payment_for" => $index, "data" => $data);
            }
        }

        return $main_array;
    }



    public function remove_payment_log($id)
    {
        VendorLog::find($id)->delete();
        return ['status' => 1, 'message' => 'Log deleted successfully'];
    }

    public function payment_export($vendor_balance_id)
    {
        $vendor_logs = VendorLog::where('vendor_balance_id', $vendor_balance_id)->orderBy('date', 'ASC')->get()->groupBy('payment_for'); // Changed model name
        $main_array = [];

        foreach ($vendor_logs as $index => $logs) {

            if (count($logs)) {
                $balance = 0;
                $total_credit = 0;
                $total_debit = 0;
                $data = [];
                $sno = 0;
                foreach ($logs as $log) {
                    $sno++;
                    $credit = '';
                    $debit = '';
                    if ($log->type == 'Credit') {
                        $credit = "₹" . number_format($log->amount, 2);
                        $balance = $balance + $log->amount;
                        $total_credit = $total_credit + $log->amount;
                    } else {
                        $debit = "₹" . number_format($log->amount, 2);
                        $balance = $balance - $log->amount;
                        $total_debit = $total_debit + $log->amount;
                    }

                    $symbol = '';
                    $temp_balance = $balance;
                    if ($temp_balance < 0) {
                        $temp_balance = - ($temp_balance);
                        $symbol = '-';
                    }
                    $balance_text = $symbol . "₹" . number_format($temp_balance, 2);
                    $data[] = ['sno' => $sno, 'id' => $log->id, 'date' => date("d-m-Y", strtotime($log->date)), 'description' => $log->description, 'credit' => $credit, 'debit' => $debit, 'balance' => $balance_text, 'payment_for' => $log->payment_for];
                }

                $total_debit = "₹" . number_format($total_debit, 2);
                $total_credit = "₹" . number_format($total_credit, 2);

                $data[] = ['sno' => "", 'date' => '', 'description' => "Total", 'credit' => $total_credit, 'debit' => $total_debit, 'balance' => $balance_text, 'payment_for' => $log->payment_for];

                $main_array[] = ['payment_for' => $index, 'data' => $data];
            }
        }

        $vendor = VendorPayment::findOrFail($vendor_balance_id);
        $job_order = $vendor->job_order_id;

        $tender = Tender::find($job_order);


        $tender_name = $tender ? $tender->name : 'Unknown Tender';

        $data["view_file"] = "excel_export.vendor_payment_log";
        $data["export_data"] = $main_array;
        $data["vendor_payment_name"] = $tender_name;

        return Excel::download(new ExcelExport($data), 'payment_log.xlsx');
    }
}
