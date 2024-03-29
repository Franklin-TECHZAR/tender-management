<?php

namespace App\Http\Controllers;

use App\Models\VendorLog;
use App\Http\Controllers\Controller;
use App\Models\InvoicePurchase;
use App\Models\Vendor;
use App\Models\Tender;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExcelExport;

class VendorLogController extends Controller
{

    public function index(Request $request)
    {
        $purchases = InvoicePurchase::with('purchaseType')->get();
        // $vendors = Vendor::with('vendorLog')->get();
        $vendors = VendorLog::with('vendorpayment')->get();
        $tenders = Tender::pluck('name', 'id');
        $allLogs = array_merge($purchases->toArray(),$vendors->toArray());
        $vendor = VendorPayment::get()->pluck('vendor_name', 'id');
        return view('vendor_payment.vendor_log', compact('allLogs','tenders','vendor','vendors'));
    }

    public function payment_export()
    {
        $payment_log = VendorLog::get()->groupBy('amount_for');


        $jobOrderNames = [];
        foreach ($payment_log as $index => $logs) {
            $jobOrderIds = $logs->pluck('job_order_id')->unique()->toArray();

            foreach ($jobOrderIds as $jobOrderId) {
                $jobOrder = Tender::find($jobOrderId);

                if ($jobOrder) {
                    $jobOrderNames[$jobOrderId] = $jobOrder->name;
                }
            }
        }

        $payment_logs = VendorLog::where('job_order_id', $jobOrderId)->orderBy('date', "ASC")->get()->groupBy('amount_for');
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

        $tender = Tender::find($jobOrderId);

        $data["view_file"] = "excel_export.vendor_log_export";
        $data["export_data"] = $main_array;
        $data["tender_name"] = $tender->name;

        // return view('excel_export.tender_payment_log', compact('data'));

        return Excel::download(new ExcelExport($data), 'vendor_log_export.xlsx');
    }
}
