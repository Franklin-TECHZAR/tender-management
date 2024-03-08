<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Tender;
use App\Models\TenderPaymentLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $show = $request->show;
        return view('tender.index', compact('show'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'ed_amount' => 'required',
            'pg_amount' => 'required',
        ]);

        if ($request->edit_id) {
            $tender = Tender::find($request->edit_id);
            $message = "Tender Updated Successfully";
        } else {
            $tender = new Tender();
            $message = "Tender Created Successfully";
            $tender->status = 1;
            $tender->job_order = 0;
        }

        $tender->name = $request->name;
        $tender->city = $request->city;
        $tender->address = $request->address;
        $tender->ed_amount = $request->ed_amount;
        $tender->pg_amount = $request->pg_amount;
        $tender->description = $request->description;

        $tender->save();

        return array("status" => 1, "message" => $message);
    }

    public function fetch(Request $request)
    {
        if ($request->show == 'New') {
            $job_order = 0;
        } else {
            $job_order = 1;
        }
        $data = Tender::where('job_order', $job_order)->orderBy('status', "DESC")->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">InActive</span>';
                }
            })
            ->addColumn('ed_amount_text', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->ed_amount, 2) . "</span>";
            })
            ->addColumn('pg_amount_text', function ($row) {
                return "<span class='pull-right'>₹" . number_format($row->pg_amount, 2) . "</span>";
            })
            ->addColumn('action', function ($row) {

                if ($row->status == 0) {
                    $status_btn = '<button data-id="' . $row->id . '" data-status="Active" class="change-status-btn dropdown-item"><i class="bi bi-arrow-up-right-circle"></i> Change to Active</button>';
                } else {
                    $status_btn = '<button data-id="' . $row->id . '" data-status="InActive" class="change-status-btn dropdown-item"><i class="bi bi-arrow-up-right-circle"></i> Change to InActive</button>';
                }

                if ($row->job_order == 0) {
                    $job_order = '<button data-id="' . $row->id . '" data-status="Add" class="job-order-change-btn dropdown-item"><i class="bi bi-plus-circle"></i> Add to Job Order</button>';
                } else {
                    $job_order = '<button data-id="' . $row->id . '" data-status="Remove" class="job-order-change-btn dropdown-item"><i class="bi bi-x-circle"></i> Remove from Job Order</button>';
                }

                $btn = '<div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                                ' . $job_order . $status_btn . '
                                <a href="' . url('tender/payments') . '/' . $row->id . '" class="dropdown-item"><i class="bi bi-cash-stack"></i> Payments</a>
                                <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action', 'status', 'ed_amount_text', 'pg_amount_text'])
            ->make(true);
    }
    public function fetch_edit($id)
    {
        $tender = Tender::find($id);
        return $tender;
    }

    public function delete($id)
    {
        Tender::find($id)->delete();
        return array("status" => 1, "message" => "Tender deleted successfully");
    }

    public function chage_status(Request $request)
    {
        $id = $request->edit_id;
        $status = $request->status;
        $tender = Tender::find($id);
        if ($status == "Active") {
            $tender->status = 1;
        } else if ($status == "InActive") {
            $tender->status = 0;
        } else if ($status == "Add") {
            $tender->job_order = 1;
        } else if ($status == "Remove") {
            $tender->job_order = 0;
        }
        $tender->save();
        return array("status" => 1, "message" => "Status Updated successfully");
    }

    public function payments($tender_id)
    {
        $tender = Tender::find($tender_id);
        return view('tender.payments', compact('tender'));
    }
    public function payment_store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'amount_for' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'payment_mode' => 'required',
            'payment_details' => 'required',
            'description' => 'required',
            'tender_id' => 'required',
        ]);

        $payment_log = new TenderPaymentLog();
        $payment_log->tender_id = $request->tender_id;
        $payment_log->date = $request->date;
        $payment_log->amount_for = $request->amount_for;
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
            'tender_id' => 'required',
        ]);
        $tender_id = $request->tender_id;
        $payment_logs = TenderPaymentLog::where('tender_id', $tender_id)->orderBy('date', "ASC")->get()->groupBy('amount_for');
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
        TenderPaymentLog::find($id)->delete();
        return array("status" => 1, "message" => "Log deleted successfully");
    }
    public function payment_export($tender_id)
    {
        $payment_logs = TenderPaymentLog::where('tender_id', $tender_id)->orderBy('date', "ASC")->get()->groupBy('amount_for');
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
                    $balance_text = $symbol . "₹" . number_format($temp_balance, 2) ;
                    $data[] = array('sno' => $sno, 'id' => $log->id, "date" => date("d-m-Y", strtotime($log->date)), "description" => $log->description, "credit" => $credit, "debit" => $debit, "balance" => $balance_text, 'amount_for' => $log->amount_for);
                }

                $total_debit = "₹" . number_format($total_debit, 2);
                $total_credit = "₹" . number_format($total_credit, 2);

                $data[] = array('sno' => "", 'date' => '', "description" => "Total", "credit" => $total_credit, "debit" => $total_debit, "balance" => $balance_text, 'amount_for' => $log->amount_for);

                $main_array[] = array("payment_for" => $index, "data" => $data);
            }
        }

        // dd($main_array);

        $tender = Tender::find($tender_id);

        $data["view_file"] = "excel_export.tender_payment_log";
        $data["export_data"] = $main_array;
        $data["tender_name"] = $tender->name;

        // return view('excel_export.tender_payment_log', compact('data'));

        return Excel::download(new ExcelExport($data), 'tender_payment_log.xlsx');
    }
}
