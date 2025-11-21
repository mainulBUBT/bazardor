<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitStoreUpdateRequest;
use App\Services\UnitService;
use Brian2694\Toastr\Facades\Toastr;
use App\Exports\UnitsExport;
use App\Imports\UnitsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UnitController extends Controller
{
    public function __construct(protected UnitService $unitService) {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = $this->unitService->getUnits();
        return view("admin.units.index", compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitStoreUpdateRequest $unitStoreUpdateRequest)
    {
        $this->unitService->storeUnit($unitStoreUpdateRequest->validated());

        toastr()->success(translate("messages.Unit created successfully"));
        return redirect()->route("admin.units.index");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unit = $this->unitService->findById($id);
        return view("admin.units.edit", compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitStoreUpdateRequest $unitStoreUpdateRequest, string $id)
    {
        $this->unitService->updateUnit($unitStoreUpdateRequest->validated(), $id);

        toastr()->success(translate("messages.Unit updated successfully"));
        return redirect()->route("admin.units.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->unitService->deleteUnit($id);

        toastr()->success(translate("messages.Unit deleted successfully"));
        return redirect()->route("admin.units.index");
    }
    
    /**
     * Display the import/export page.
     */
    public function importExport()
    {
        return view('admin.units.import_export');
    }

    /**
     * Import units from Excel/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new UnitsImport, $request->file('file'));
            toastr()->success(translate('messages.Units imported successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            toastr()->error(translate('messages.Import failed') . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Export units.
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'xlsx');
        $extension = 'xlsx';
        $writerType = \Maatwebsite\Excel\Excel::XLSX;

        if ($format === 'csv') {
            $extension = 'csv';
            $writerType = \Maatwebsite\Excel\Excel::CSV;
        } elseif ($format === 'pdf') {
            $extension = 'pdf';
            $writerType = \Maatwebsite\Excel\Excel::MPDF;
        }

        return Excel::download(new UnitsExport, 'units_' . date('Y-m-d_H-i-s') . '.' . $extension, $writerType);
    }
}
