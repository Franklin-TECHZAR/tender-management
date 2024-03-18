<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnBalance;
use App\Models\Tender;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ReturnBalanceController extends Controller
{
    public function index()
    {
        $tenders = Tender::where('job_order', 1)
                        ->where('status', 1)
                        ->pluck('name', 'id');
        return view('Return_amount.balance', compact('tenders'));
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
            $returnamount = ReturnBalance::findOrFail($request->edit_id);
            $message = "Payment updated successfully";
        } else {
            $returnamount = new ReturnBalance();
            $message = "Payment created successfully";
        }

        $returnamount->job_order_id = $request->job_order;
        $returnamount->date = $request->date;
        $returnamount->amount = $request->amount;
        $returnamount->description = $request->description;
        $returnamount->payment_mode = $request->payment_mode;
        $returnamount->payment_details = $request->payment_details;
        $returnamount->save();

        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function fetch()
    {
        $data = ReturnBalance::orderBy('date', "DESC")->get();
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
        $returnamount = ReturnBalance::find($id);
        return $returnamount;
    }

    public function delete($id)
    {
        ReturnBalance::find($id)->delete();

        return array("status" => 1, "message" => "Payments deleted successfully");
    }

}
