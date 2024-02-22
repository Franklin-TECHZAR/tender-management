<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Tender;
use App\Models\Labour;
use App\Models\LabourReport;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class LabourReportController extends Controller
{
    public function index()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $Labour = Labour::get('name');
        $labourReport = LabourReport::all();
        return view('daily_report.index', compact('tenders', 'Labour', 'labourReport'));
    }

    public function create()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $Labour = Labour::get('name');
        return view('daily_report.create', compact('tenders', 'Labour'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_order' => 'required',
            'labour' => 'required',
            'description' => 'required',
        ]);

        $dates = explode(', ', $request->date);

        if ($request->edit_id) {
            $existingReport = LabourReport::find($request->edit_id);
            $message = "Labour Report Updated Successfully";

            $existingReport->job_order = $request->job_order;
            $existingReport->labour = $request->labour;
            $existingReport->desc = $request->description;
            $existingReport->save();

            if (count($dates) == 1) {
                $existingReport->date = $dates[0];
                $existingReport->save();
            } else {
                foreach ($dates as $date) {
                    if ($date != $existingReport->date) {
                        $labourReport = new LabourReport();
                        $labourReport->job_order = $request->job_order;
                        $labourReport->labour = $request->labour;
                        $labourReport->desc = $request->description;
                        $labourReport->date = $date;
                        $labourReport->save();
                    }
                }
            }
        } else {
            foreach ($dates as $date) {
                $labourReport = new LabourReport();
                $labourReport->job_order = $request->job_order;
                $labourReport->labour = $request->labour;
                $labourReport->desc = $request->description;
                $labourReport->date = $date;
                $labourReport->save();
            }

            $message = "Labour Reports Created Successfully";
        }

        return ["status" => 1, "message" => $message];
    }



    public function fetch()
    {
        $data = LabourReport::orderBy('date', 'DESC')->get();
        return DataTables::of($data)
            ->addIndexColumn()
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
            ->rawColumns(['action'])
            ->make(true);
    }

    public function fetch_edit($id)
    {
        $labourReport = LabourReport::find($id);
        return $labourReport;
    }

    public function delete($id)
    {
        LabourReport::find($id)->delete();

        return array("status" => 1, "message" => "Labour deleted successfully");
    }

    public function export(Request $request)
    {
        $query = LabourReport::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);

            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->has('job_order')) {
            $query->where('job_order', $request->job_order);
        }

        $labourReports = $query->orderBy('date', 'ASC')->get();
        if ($labourReports->isEmpty()) {
            return redirect()->back()->with('error', 'No data found based on the selected criteria.');
        }

        $export_data = $labourReports->map(function ($labourReport, $index) {
            $jobOrderName = Tender::find($labourReport->job_order)->name;
            return [
                'S.No' => $index + 1,
                'Job Order' => $jobOrderName,
                'Labour' => $labourReport->labour,
                'Date' => date("d-m-Y", strtotime($labourReport->date)),
                'Description' => $labourReport->desc,
            ];
        });

        $data = [
            'view_file' => 'excel_export.labour_report_export',
            'export_data' => $export_data,
            'date_range' =>  $request->date_range,
        ];

        return Excel::download(new ExcelExport($data), 'labour_reports.xlsx');
    }
}
