<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Salary;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class SalaryController extends Controller
{
    public function create()
    {
        $tenders = Tender::where('job_order', 1)
                         ->where('status', 1)
                         ->pluck('name');
        return view('salaries.create', compact('tenders'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'job_order' => 'required',
            'labour' => 'required',
            'amount' => 'required|numeric',
            'description' => 'required',
            'payment_mode' => 'required',
            'payment_details' => 'required',
        ]);

        $dates = explode(', ', $request->date);

        if ($request->edit_id) {
            $existingSalary = Salary::find($request->edit_id);
            $message = "Salary Updated Successfully";

            $existingSalary->job_order = $request->job_order;
            $existingSalary->labour = $request->labour;
            $existingSalary->amount = $request->amount;
            $existingSalary->description = $request->description;
            $existingSalary->payment_mode = $request->payment_mode;
            $existingSalary->payment_details = $request->payment_details;
            $existingSalary->save();
            if (count($dates) == 1) {
                $existingSalary->date = $dates[0];
                $existingSalary->save();
            } else {
                foreach ($dates as $date) {
                    if ($date != $existingSalary->date) {
                        $newSalary = new Salary();
                        $newSalary->job_order = $request->job_order;
                        $newSalary->labour = $request->labour;
                        $newSalary->date = $date;
                        $newSalary->amount = $request->amount;
                        $newSalary->description = $request->description;
                        $newSalary->payment_mode = $request->payment_mode;
                        $newSalary->payment_details = $request->payment_details;
                        $newSalary->save();
                    }
                }
            }
        } else {
            foreach ($dates as $date) {
                $newSalary = new Salary();
                $newSalary->job_order = $request->job_order;
                $newSalary->labour = $request->labour;
                $newSalary->date = $date;
                $newSalary->amount = $request->amount;
                $newSalary->description = $request->description;
                $newSalary->payment_mode = $request->payment_mode;
                $newSalary->payment_details = $request->payment_details;
                $newSalary->save();
            }

            $message = "Salaries Created Successfully";
        }

        return array("status" => 1, "message" => $message);
    }




    public function fetch()
    {
        $data = Salary::get();
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
                            <button data-id="' . $row->id . '" class="salary-btn dropdown-item"><i class="dw dw-download"></i> Download Receipt</a>
                            <button data-id="' . $row->id . '" class="view-btn dropdown-item"><i class="dw dw-view"></i> View</button>
                            <button data-id="' . $row->id . '" class="edit-btn dropdown-item"><i class="dw dw-edit2"></i> Edit</button>
                            <button data-id="' . $row->id . '" class="delete-btn dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                            </div>
                        </div>';

                return $btn;
            })
            ->rawColumns(['action','amount'])
            ->make(true);
    }

    public function fetch_edit($id)
    {
        $salary = Salary::find($id);
        return $salary;
    }

    public function delete($id)
    {
        Salary::find($id)->delete();

        return array("status" => 1, "message" => "Salary deleted successfully");
    }

    public function generatePDF($id)
    {
        $salary = Salary::findOrFail($id);
        // Modify the following lines as needed to generate the PDF
        $company_settings = CompanySetting::first();
        $address = $company_settings->address;
        $mobile = $company_settings->mobile;
        $email = $company_settings->email;
        $name = $company_settings->name;
        $data = [
            'salary' => $salary,
            'address' => $address,
            'mobile' => $mobile,
            'email' => $email,
            'name' => $name,
        ];
        $pdf = PDF::loadView('pdf_export.salary_receipt', $data);
        return $pdf->stream('salary_receipt.pdf');
            // return $pdf->download('payment_receipt.pdf');
    }
}
