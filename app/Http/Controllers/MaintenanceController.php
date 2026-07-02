<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ITAsset;
use App\Models\Log;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class MaintenanceController extends Controller
{
    public function getData(Request $request)
    {
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // $maintenances = Maintenance::select('year', 'month', DB::raw('count(*) as total'))
        // ->groupBy('year', 'month')
        // ->orderByRaw("
        //     CASE 
        //         /* Prioritas 0 untuk bulan sekarang atau masa depan */
        //         WHEN year > $currentYear OR (year = $currentYear AND month >= $currentMonth) THEN 0
        //         /* Prioritas 1 untuk bulan yang sudah lewat */
        //         ELSE 1 
        //     END ASC
        // ")
        // ->orderBy('year', 'asc')
        // ->orderBy('month', 'asc')
        // ->get();

        // return DataTables::of($maintenances)
        // ->addColumn('formatted_maintenance_date', function ($maintenance) {
        //     return Carbon::createFromDate($maintenance->year, $maintenance->month, 1)->format('F Y');
        // })
        // ->addColumn('delete_url', function ($maintenance) {
        //     return route('maintenance.destroy', encrypt(Carbon::createFromDate($maintenance->year, $maintenance->month, 1)->format('F Y')));
        // })
        // ->make(true);

        $maintenances = Maintenance::with('asset', 'owner');
        if ($request->month) {
            $maintenances->where('month', $request->month);
        }

        if ($request->year) {
            $maintenances->where('year', $request->year);
        } else {
            $maintenances->where('year', $currentYear);
        }        

        $maintenances->where('year', $currentYear)
        ->orderByRaw("
            CASE 
                /* Prioritas 0 untuk bulan sekarang atau masa depan */
                WHEN year > $currentYear OR (year = $currentYear AND month >= $currentMonth) THEN 0
                /* Prioritas 1 untuk bulan yang sudah lewat */
                ELSE 1 
            END ASC
        ")
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc');

        
        $maintenances = $maintenances->get();

        return DataTables::of($maintenances)
            ->addColumn('encrypted_id', function ($maintenance) {
                return encrypt($maintenance->id);
            })
            ->addColumn('formatted_maintenance_date', function ($maintenance) {
                return Carbon::createFromDate($maintenance->year, $maintenance->month, 1)->format('F Y');
            })
            ->addColumn('asset_code', function ($maintenance) {
                return $maintenance->asset->asset_code ?? 'N/A';
            })
            ->addColumn('brand', function ($maintenance) {
                return $maintenance->asset->brand ?? 'N/A';
            })
            ->addColumn('edit_url', function ($maintenance) {
                return route('maintenance.edit', encrypt($maintenance->id));
            })
            ->addColumn('delete_url', function ($maintenance) {
                return route('maintenance.destroy', encrypt($maintenance->id));
            })
            ->make(true);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $maintenances = Maintenance::select('year', 'month')->groupBy('year', 'month')->orderBy('year', 'asc')->orderBy('month', 'asc')->get();
        // $ongoingMaintenanceCount = $maintenances->filter(function ($maintenance) {
        //     $maintenanceDate = Carbon::createFromDate($maintenance->year, $maintenance->month, 1);
        //     return $maintenanceDate->isCurrentMonth() || $maintenanceDate->isFuture();
        // })->count();

        // $passedMaintenanceCount = $maintenances->filter(function ($maintenance) {
        //     $maintenanceDate = Carbon::createFromDate($maintenance->year, $maintenance->month, 1);
        //     return $maintenanceDate->isPast() && !$maintenanceDate->isCurrentMonth();
        // })->count();
                                                                    // , compact('ongoingMaintenanceCount', 'passedMaintenanceCount')
        return view('pages.administrator.it-asset.maintenance.index');
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
    public function store(Request $request)
    {
        $preprocessed = $request->all();

        $preprocessed['maintenance_date'] = Carbon::parse($request->maintenance_date);
        // dd($preprocessed);
        $preprocessed['assets'] = collect($preprocessed['assets'])->map(function ($asset) use ($preprocessed) {
            return [
                'it_asset_id' => decrypt($asset['encrypted_id']),
                'year' => $preprocessed['maintenance_date']->year,
                'month' => $preprocessed['maintenance_date']->month,
                // 'day' => $asset['day']? (int) $asset['day'] : null,
                'owner_id' => decrypt($asset['encrypted_employee_id']),
            ];
        })->toArray();

        $owners = Employee::whereIn('id', collect($preprocessed['assets'])->pluck('owner_id'))->get()->keyBy('id')->load('department', 'building', 'area');

        $preprocessed['assets'] = collect($preprocessed['assets'])->map(function ($asset) use ($owners) {
            $owner = $owners->get($asset['owner_id']);
            return array_merge($asset, [
                'department' => $owner->department->name?? 'N/A',
                'building' => $owner->building->nama ?? 'N/A',
                'area' => $owner->area->name ?? 'N/A',
            ]);
        })->toArray();


        Validator::make($preprocessed, [
            'maintenance_date' => "required|date",
            'assets.*.it_asset_id' => 'required|min:1',
            // 'assets.*.day' => 'nullable|integer|between:1,31',
            'assets.*.owner_id' => 'required|exists:employees,id',
        ], [
            'maintenance_date.required' => 'The maintenance date is required.',
            'maintenance_date.date' => 'The maintenance date must be a valid date.',
            'assets.*.it_asset_id.required' => 'At least one asset must be selected.',
            // 'assets.*.day.integer' => 'The day must be an integer.',
            // 'assets.*.day.between' => 'The day must be between 1 and 31.',
        ])->validate();

        try {
            DB::beginTransaction();
            $maintenances = Maintenance::where('month', $preprocessed['maintenance_date']->month)
                ->where('year', $preprocessed['maintenance_date']->year)
                ->with('asset')
                ->get();

            $removedMaintenances = $maintenances->filter(function ($maintenance) use ($preprocessed) {
                return !$maintenance->month == $preprocessed['maintenance_date']->month
                    || !$maintenance->year == $preprocessed['maintenance_date']->year
                    || !collect($preprocessed['assets'])->pluck('it_asset_id')->contains($maintenance->it_asset_id);
            });

            // $updatedMaintenances = $maintenances->filter(function ($maintenance) use ($preprocessed) {
            //     return $maintenance->month == $preprocessed['maintenance_date']->month
            //         && $maintenance->year == $preprocessed['maintenance_date']->year
            //         && collect($preprocessed['assets'])->pluck('it_asset_id')->contains($maintenance->it_asset_id);
            // });

            // $updatedMaintenances = Maintenance::upsert(
            //     $preprocessed['assets'],
            //     uniqueBy: ['year', 'month', 'it_asset_id'], // Unique by year, month, and it_asset_id
            //     update: ['day', 'area', 'building', 'department', 'owner_id'] // Update if exists
            // );
            collect($preprocessed['assets'])->each(function ($asset) {
                Maintenance::updateOrCreate(
                    [
                        'year' => $asset['year'],
                        'month' => $asset['month'],
                        'it_asset_id' => $asset['it_asset_id'],
                    ],
                    [
                        // 'day' => $asset['day'],
                        'area' => $asset['area'],
                        'building' => $asset['building'],
                        'department' => $asset['department'],
                        'owner_id' => $asset['owner_id'],
                    ]
                );
            });

            if ($removedMaintenances->isNotEmpty()) {
                Maintenance::whereIn('id', $removedMaintenances->pluck('id'))->delete();
            }

            Log::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'action' => "update",
                'description' => "Updated asset maintenance schedule for " . $preprocessed['maintenance_date']->format('F Y')
            ]);

            DB::commit();

            return response()->json(['message' => 'Maintenance schedule created successfully. Removed ' . $removedMaintenances->count() . ' maintenances, updated/added ' . collect($preprocessed['assets'])->count() . ' maintenances.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create maintenance schedule', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $maintenance = Maintenance::with('asset', 'owner')->find(decrypt($id));

        if (!$maintenance) {
            return response()->json(['message' => 'Maintenance record not found.'], 404);
        }

        return response()->json([
            'maintenance' => $maintenance,
            'formatted_maintenance_date' => Carbon::createFromDate($maintenance->year, $maintenance->month, 1)->format('F Y'),
            'asset_code' => $maintenance->asset->asset_code ?? 'N/A',
            'brand' => $maintenance->asset->brand ?? 'N/A',
            'owner_name' => $maintenance->owner->name ?? 'N/A',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::find(decrypt($id));

        if (!$maintenance) {
            return response()->json(['message' => 'Maintenance record not found.'], 404);
        }

        $preprocessed = $request->all();
        $preprocessed['new_date'] = Carbon::parse($request->new_date);

        Validator::make($preprocessed, [
            'new_date' => "required|date",
        ], [
            'new_date.required' => 'The maintenance date is required.',
            'new_date.date' => 'The maintenance date must be a valid date.',
        ])->validate();

        try {
            DB::beginTransaction();

            $maintenance->update([
                'year' => $preprocessed['new_date']->year,
                'month' => $preprocessed['new_date']->month,
            ]);


            Log::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'action' => "update",
                'description' => "Updated asset maintenance schedule for " . Carbon::createFromDate($maintenance->year, $maintenance->month, 1)->format('F Y')
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Maintenance schedule updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update maintenance schedule', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // dd(decrypt($id));
        try {
            DB::beginTransaction();

            $maintenance = Maintenance::find(decrypt($id));
            $maintenance->delete();

            Log::create([
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'action' => "delete",
                'description' => "Deleted asset maintenance schedule for " . $maintenance->formatted_maintenance_date . " which had 1 record"
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Successfully deleted maintenance record."]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error occurred while deleting maintenance records. ' . $e->getMessage()], 400);
        }
    }
}
