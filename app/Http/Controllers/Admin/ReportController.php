<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): View
    {
        $mode = $request->input('mode', 'monthly');
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $rows = match ($mode) {
            'daily' => $reports->daily($year, $month),
            'garage_type' => $reports->byGarageType(),
            'tenant' => $reports->byTenant(),
            default => $reports->monthly($year),
        };

        return view('admin.reports.index', compact('rows', 'mode', 'year', 'month'));
    }
}