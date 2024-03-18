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
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class LabourReportController extends Controller
{
    public function index()
    {
        $tenders = Tender::where('job_order', 1)
            ->where('status', 1)
            ->pluck('name', 'id');
        $Labour = Labour::pluck('name', 'id');
        $labourReport = LabourReport::all();
        return view('daily_report.index', compact('tenders', 'Labour', 'labourReport'));
    }

    public function store(Request $request)
    {
        $isEdit = !empty($request->edit_id);

        if (!$isEdit) {
            $existingReport = LabourReport::where('job_order', $request->job_order)
                ->where('date', $request->date)
                ->where('id', '!=', $request->edit_id)
                ->exists();

            if ($existingReport) {
                return response()->json(['status' => 0, 'message' => 'A Labour Report already exists for the selected Job Order and Date.']);
            }
        }

        // $labourNames = $request->input('labour');
        // $existingLabourReport = LabourReport::where('job_order', $request->job_order)
        //     ->where('date', $request->date)
        //     ->whereIn('labour', $labourNames)
        //     ->exists();

        // if ($existingLabourReport) {
        //     return response()->json(['status' => 0, 'message' => 'A Labour Report with one of the selected Labour names already exists for the selected Job Order and Date.']);
        // }

        // Define validation rules based on conditions
        $dateValidationRules = [];
        if (!$request->input('date_disabled') && !$request->input('date_readonly') && !$isEdit) {
            $dateValidationRules = [
                'date' => [
                    'required',
                    Rule::unique('labour_reports')->where(function ($query) use ($request) {
                        return $query->where('job_order', $request->job_order);
                    })->ignore($request->edit_id)
                ]
            ];
        }

        // Validate the request
        $this->validate($request, array_merge([
            'job_order' => 'required',
            'labour' => 'required|array',
            'description' => 'required',
        ], $dateValidationRules), [
            'date.required' => 'The date field is required.',
            'date.unique' => 'A Labour Report already exists for the selected Job Order and Date.'
        ]);

        // Update or create the Labour Report
        if ($isEdit) {
            $existingReport = LabourReport::find($request->edit_id);
            $message = "Labour Report Updated Successfully";

            $existingReport->job_order = $request->job_order;
            $existingReport->labour_id = implode(',', $request->labour);
            $existingReport->desc = $request->description;
            $existingReport->date = $request->date;
            $existingReport->save();
        } else {
            $labourReport = new LabourReport();
            $labourReport->job_order = $request->job_order;
            $labourReport->labour_id = implode(',', $request->labour);
            $labourReport->desc = $request->description;
            $labourReport->date = $request->date;
            $labourReport->save();

            $message = "Labour Report Created Successfully";
        }

        // Return response
        return response()->json(['status' => 1, 'message' => $message]);
    }



    public function checkDate(Request $request)
    {
        $jobOrder = $request->input('job_order');
        $date = $request->input('date');
        $editId = $request->input('edit_id');
        $existingReport = LabourReport::where('job_order', $jobOrder)
            ->where('date', $date)
            ->where('id', '!=', $editId)
            ->exists();

        if ($existingReport) {
            return response()->json(['status' => 0, 'message' => 'A Labour Report already exists for the selected Job Order and Date.']);
        }

        return response()->json(['status' => 1, 'message' => 'No existing Labour Report for the selected Job Order and Date.']);
    }



    public function fetch()
    {
        $data = LabourReport::with('labour:id,name')->orderBy('date', 'DESC')->get();
        $labourIds = $data->pluck('labour_id')->map(function ($ids) {
            return explode(',', $ids);
        })->flatten()->unique();

        $labourNames = Labour::whereIn('id', $labourIds)->pluck('name', 'id');

        $data->transform(function ($item) use ($labourNames) {
            $item->date = Carbon::parse($item->date)->format('d-m-Y');
            $labourIds = explode(',', $item->labour_id);
            $item->labour_names = $labourNames->only($labourIds)->implode(', ');
            return $item;
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('labour', function ($row) {
                return $row->labour_names ?? '';
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

            $start_date = date('d-m-Y', strtotime($dates[0]));
            $end_date = date('d-m-Y', strtotime($dates[1]));

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
