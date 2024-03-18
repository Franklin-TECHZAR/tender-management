<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\VendorPayment;
use App\Models\VendorLog;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VendorPaymentController extends Controller
{

    public function create()
    {
        $tenders = Tender::where('job_order', 1)
                        ->where('status', 1)
                        ->pluck('name', 'id');
        return view('vendor_payment.vendor', compact('tenders'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_order' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'required',
            'payment_mode' => 'required',
            'payment_details' => 'required',
        ]);

        if ($request->edit_id) {
            $vendor = VendorPayment::findOrFail($request->edit_id);
            $message = "Payment updated successfully";
        } else {
            $vendor = new VendorPayment();
            $message = "Payment created successfully";
        }

        $vendor->job_order_id = $request->job_order;
        $vendor->date = $request->date;
        $vendor->amount = $request->amount;
        $vendor->description = $request->description;
        $vendor->payment_mode = $request->payment_mode;
        $vendor->payment_details = $request->payment_details;
        $vendor->save();

        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function fetch()
    {
        $data = VendorPayment::orderBy('date', "DESC")->get();
        $data->transform(function ($item) {
            $item->date = Carbon::parse($item->date)->format('d-m-Y');
            return $item;
        });
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('amount', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->amount, 2) . "</span>";
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                            <a href="' . url('vendor_payment/payments') . '/' . $row->id . '" class="dropdown-item"><i class="bi bi-cash-stack"></i> Payments</a>
                            <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                            <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action', 'amount'])
            ->make(true);
    }

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
        $vendor_payment = VendorPayment::find($id);
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
            'payment_for' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'payment_mode' => 'required',
            'payment_details' => 'required',
            'description' => 'required',
            'vendor_payment_id ' => '1',
        ]);

        $vendor_log = new VendorLog();
        $vendor_log->vendor_payment_id  = $request->vendor_payment_id ;
        $vendor_log->date = $request->date;
        $vendor_log->payment_for = $request->payment_for;
        $vendor_log->amount = $request->amount;
        $vendor_log->type = $request->type;
        $vendor_log->payment_mode = $request->payment_mode;
        $vendor_log->payment_details = $request->payment_details;
        $vendor_log->description = $request->description;
        $vendor_log->save();

        return response()->json(['status' => 1, 'message' => 'Payment Created Successfully']);
    }


    public function fetch_payment_log(Request $request)
    {
        $this->validate($request, [
            'vendor_payment_id' => 'required',
        ]);

        $vendor_payment_id = $request->vendor_payment_id;
        $payment_logs = VendorLog::where('vendor_payment_id', $vendor_payment_id)->orderBy('date', "ASC")->get()->groupBy('payment_for');

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
        VendorLog::find($id)->delete(); // Changed model name
        return ['status' => 1, 'message' => 'Log deleted successfully'];
    }

    public function payment_export($vendor_payment_id)
    {
        $vendor_logs = VendorLog::where('vendor_payment_id', $vendor_payment_id)->orderBy('date', 'ASC')->get()->groupBy('payment_for'); // Changed model name
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

        $vendor = VendorPayment::findOrFail($vendor_payment_id);
        $job_order = $vendor->job_order_id;

        $tender = Tender::find($job_order);


        $tender_name = $tender ? $tender->name : 'Unknown Tender';

        $data["view_file"] = "excel_export.vendor_payment_log";
        $data["export_data"] = $main_array;
        $data["vendor_payment_name"] = $tender_name;

        return Excel::download(new ExcelExport($data), 'payment_log.xlsx');
    }
}
