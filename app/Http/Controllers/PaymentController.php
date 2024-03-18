<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Labour;
use App\Models\Payment;
use App\Models\PaymentLog;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{

    public function create()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $Labour = Labour::get('name');
        return view('payment.purchase_dept', compact('tenders', 'Labour'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_order' => 'required',
            'payment_for' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_mode' => 'required',
            'payment_details' => 'required',
        ]);

        if ($request->edit_id) {
            $payment = Payment::findOrFail($request->edit_id);
            $message = "Payment updated successfully";
        } else {
            $payment = new Payment();
            $message = "Payment created successfully";
        }

        $payment->job_order = $request->job_order;
        $payment->payment_for = $request->payment_for;
        $payment->date = $request->date;
        $payment->amount = $request->amount;
        $payment->payment_mode = $request->payment_mode;
        $payment->payment_details = $request->payment_details;
        $payment->save();

        return response()->json(['status' => 1, 'message' => $message]);
    }


    public function fetch()
    {
        $data = Payment::orderBy('date', "DESC")->get();
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
                            <a href="' . url('purchase_dept/payments') . '/' . $row->id . '" class="dropdown-item"><i class="bi bi-cash-stack"></i> Payments</a>
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
        $Payment = Payment::find($id);
        return $Payment;
    }

    public function delete($id)
    {
        Payment::find($id)->delete();

        return array("status" => 1, "message" => "Payments deleted successfully");
    }

    public function payments($id)
    {
        $purchase_dept = Payment::find($id);
        $job_order = $purchase_dept->job_order;

        $tender = Tender::find($job_order);

        if ($tender) {
            $tender_name = $tender->name;
        }
        // dd($tender_name);
        $payment_for = Payment::pluck('Payment_for', 'id');
        return view('Payment.payments', compact('purchase_dept', 'payment_for', 'tender_name'));
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
            'purchase_dept_id' => 'required',
        ]);

        $payment_log = new PaymentLog();
        $payment_log->purchase_dept_id = $request->purchase_dept_id;
        $payment_log->date = $request->date;
        $payment_log->payment_for = $request->payment_for;
        $payment_log->amount = $request->amount;
        $payment_log->type = $request->type;
        $payment_log->payment_mode = $request->payment_mode;
        $payment_log->payment_details = $request->payment_details;
        $payment_log->description = $request->description;
        $payment_log->save();

        return array("status" => 1, "message" => "Payment Created Successfully");
    }

    public function fetch_payment_log(Request $request)
    {
        $this->validate($request, [
            'purchase_dept_id' => 'required',
        ]);

        $purchase_dept_id = $request->purchase_dept_id;
        $payment_fors = Payment::pluck('payment_for', 'id');
        $payment_logs = PaymentLog::where('purchase_dept_id', $purchase_dept_id)
            ->orderBy('date', 'ASC')
            ->get()
            ->groupBy('payment_for');

        $main_array = [];

        foreach ($payment_logs as $payment_for => $logs) {
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
                $data[] = [
                    'id' => $log->id,
                    'date' => date("d-m-Y", strtotime($log->date)),
                    'description' => $log->description,
                    'credit' => $credit,
                    'debit' => $debit,
                    'balance' => $balance_text,
                    'payment_for' => $payment_fors[$log->payment_for],
                ];
            }

            $total_debit = "<b class='pull-right'>₹" . number_format($total_debit, 2) . "</b>";
            $total_credit = "<b class='pull-right'>₹" . number_format($total_credit, 2) . "</b>";

            $data[] = [
                'description' => "<b class='pull-right'>Total</b>",
                'credit' => $total_credit,
                'debit' => $total_debit,
                'balance' => $balance_text,
                'payment_for' => $payment_fors[$payment_for],
            ];

            $main_array[] = [
                'payment_for' => $payment_fors[$payment_for],
                'data' => $data,
            ];
        }

        return $main_array;
    }


    public function remove_payment_log($id)
    {
        PaymentLog::find($id)->delete();
        return array("status" => 1, "message" => "Log deleted successfully");
    }

    public function payment_export($purchase_dept_id)
    {
        $payment_logs = PaymentLog::where('purchase_dept_id', $purchase_dept_id)->orderBy('date', "ASC")->get()->groupBy('payment_for');
        $main_array = [];

        foreach ($payment_logs as $index => $logs) {

            if (count($logs)) {
                $balance = 0;
                $total_credit = 0;
                $total_debit = 0;
                $data = [];
                $sno = 0;
                foreach ($logs as $log) {
                    $sno++;
                    $credit = "";
                    $debit = "";
                    if ($log->type == "Credit") {
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
                        $symbol = "-";
                    }
                    $balance_text = $symbol . "₹" . number_format($temp_balance, 2);
                    $data[] = array('sno' => $sno, 'id' => $log->id, "date" => date("d-m-Y", strtotime($log->date)), "description" => $log->description, "credit" => $credit, "debit" => $debit, "balance" => $balance_text, 'payment_for' => $log->payment_for);
                }

                $total_debit = "₹" . number_format($total_debit, 2);
                $total_credit = "₹" . number_format($total_credit, 2);

                $data[] = array('sno' => "", 'date' => '', "description" => "Total", "credit" => $total_credit, "debit" => $total_debit, "balance" => $balance_text, 'payment_for' => $log->payment_for);

                $main_array[] = array("payment_for" => $index, "data" => $data);
            }
        }

        // dd($main_array);
        $payment = Payment::findOrFail($purchase_dept_id);
        $job_order = $payment->job_order;

        $tender = Tender::find($job_order);

        if ($tender) {
            $tender_name = $tender->name;
        } else {
            $tender_name = 'Unknown Tender';
        }

        $data["view_file"] = "excel_export.payment_log";
        $data["export_data"] = $main_array;
        $data["purchase_dept_name"] = $tender_name;
        // dd($data["purchase_dept_name"]);


        // return view('excel_export.payment_log', compact('data'));

        return Excel::download(new ExcelExport($data), 'payment_log.xlsx');
    }
}
