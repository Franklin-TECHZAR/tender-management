<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class SalaryExport implements FromView
{
    public $data = array();

    public function __construct($data)
    {
        $this->data =  $data;
    }

    public function view(): View
    {
        return view($this->data['view_file'], ['data' => $this->data]);
    }
}
