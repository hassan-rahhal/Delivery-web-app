<?php

namespace App\Http\Controllers;

use App\Exports\ApprovedDriversExport;
use App\Models\Driver;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminExportController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new ApprovedDriversExport, 'approved_drivers.xlsx');
    }

    public function exportPDF()
    {
        $drivers = Driver::where('is_active', true)->get();

        $pdf = Pdf::loadView('admin.drivers.pdf', compact('drivers'));
        return $pdf->download('approved_drivers.pdf');
    }
}
