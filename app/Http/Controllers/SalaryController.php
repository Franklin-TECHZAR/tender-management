<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\Labour;
use App\Models\Salary;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class SalaryController extends Controller
{
    public function create()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $Labour = Labour::get('name');
        return view('salaries.create', compact('tenders', 'Labour'));
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
        $data = Salary::orderBy('date', "DESC")->get();
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
                            <a href="/generatesalary-pdf/' . $row->id . '" class="dropdown-item" target="_blank"><i class="dw dw-download"></i> Download Receipt</a>
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

    public function export(Request $request)
    {
        $query = Salary::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $query->where('job_order', $request->job_order);
        }

        $salary = $query->get();
        if ($salary->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $total_amount = $salary->sum('amount');

        $date_range = $request->date_range;

        $export_data = $salary->map(function ($salary, $index) {
            $jobOrderName = Tender::find($salary->job_order)->name;
            return [
                'S.No' => $index + 1,
                'Job Order' => $jobOrderName,
                'Labour' => $salary->labour,
                'Date' => date("d-m-Y", strtotime($salary->date)),
                'Type' => $salary->type,
                'Description' => $salary->description,
                'Payment Mode' => $salary->payment_mode,
                'Payment Details' => $salary->payment_details,
                'Amount' => '₹' . number_format($salary->amount, 2),
            ];
        });

        $total_amount = "₹" . number_format($total_amount, 2);

        $export_data[] = [
            'S.No' => '',
            'Job Order' => '',
            'Labour' => '',
            'Date' => '',
            'Type' => '',
            'Description' => '',
            'Payment Mode' => '',
            'Payment Details' => 'Total',
            'Amount' => $total_amount,
        ];

        $data = [
            'view_file' => 'excel_export.salary_export',
            'export_data' => $export_data,
            'date_range' =>  $date_range,
        ];

        return Excel::download(new ExpenseExport($data), 'salaries.xlsx');
    }
}
