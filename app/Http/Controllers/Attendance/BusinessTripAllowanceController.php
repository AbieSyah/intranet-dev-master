<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTripAllowance;
use App\Models\Level;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BusinessTripAllowanceController extends Controller
{
    public function index(Request $request)
    {
        $levels = Level::all();
        if ($request->ajax()) {
            $data = BusinessTripAllowance::with([
                'level.employees'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('level', function ($row) {
                    return $row->level?->nama ?? '-';
                })
                ->addColumn('trip_type', function ($row) {
                    return ucfirst($row->trip_type ?? '-');
                })
                ->addColumn('category', function ($row) {
                    return ucfirst($row->category ?? '-');
                })
                ->addColumn('minimum_hours', function ($row) {
                    return $row->minimum_hours
                        ? $row->minimum_hours . ' Jam'
                        : '-';
                })
                ->addColumn('amount', function ($row) {
                    if (!$row->amount) {
                        return '-';
                    }
                    return number_format($row->amount, 0, ',', '.') .
                        ' ' . $row->currency;
                })
                ->addColumn('total_employee', function ($row) {
                    return $row->level?->employees?->count() ?? 0;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex justify-content-center gap-2">
                            <button
                                title="Edit"
                                data-id="' . encrypt($row->id) . '"
                                class="btn btn-warning btn-sm edit-btn">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button
                                title="Delete"
                                data-id="' . encrypt($row->id) . '"
                                class="btn btn-danger btn-sm delete-btn">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    ';
                })

                ->rawColumns(['action'])

                ->make(true);
        }

        return view(
            'pages.attendance.master.business-trip-allowance.index',
            compact('levels')
        );
    }
    public function checkExisting(Request $request)
    {
        $exists = BusinessTripAllowance::where('level_id', $request->level_id)
            ->where('category', $request->category)
            ->where('trip_type', $request->trip_type)
            ->where('minimum_hours', $request->minimum_hours)
            ->first();

        return response()->json([
            'exists' => !!$exists,
            'data' => $exists
        ]);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // ================= CLEAN AMOUNT =================
            $cleanAmount = preg_replace(
                '/[^0-9]/',
                '',
                $request->amount
            );
            // ================= MERGE CLEAN DATA =================
            $request->merge([
                'amount' => $cleanAmount
            ]);
            // ================= VALIDATION =================
            $request->validate([
                'level_id' => [
                    'required',
                    'exists:master_level,id'
                ],

                'trip_type' => [
                    'required',
                    'in:domestic,overseas'
                ],

                'category' => [
                    'required',
                    'string'
                ],

                'minimum_hours' => [
                    'nullable',
                    'in:4,8'
                ],

                'currency' => [
                    'required',
                    'string',
                    'max:10'
                ],

                'amount' => [
                    'required',
                    'numeric',
                    'min:0'
                ],
            ]);

            // ================= SAVE =================
            BusinessTripAllowance::updateOrCreate(
                [
                    'level_id' => $request->level_id,

                    'trip_type' => $request->trip_type,

                    'category'  => $request->category,

                    'minimum_hours' => filled($request->minimum_hours)
                        ? $request->minimum_hours
                        : 0,
                ],
                [
                    'currency' => $request->currency,

                    'amount'   => (float) $cleanAmount,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business trip allowance berhasil disimpan'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
    public function edit($id)
    {
        try {

            $allowance = BusinessTripAllowance::with('level')
                ->findOrFail(decrypt($id));

            return response()->json([
                'id'         => encrypt($allowance->id),
                'level_id'   => $allowance->level_id,
                'level_name' => $allowance->level?->nama ?? '-',
                'category'   => $allowance->category,
                'trip_type'  => $allowance->trip_type,
                'amount'     => $allowance->amount,
                'currency'   => $allowance->currency,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Data allowance tidak ditemukan'
            ], 404);

        }
    }
    public function update(Request $request, $id)
    {
        try {

            $validated = $request->validate([
                'amount'   => 'required|numeric|min:0',
                'currency' => 'required|string|max:10',
            ]);

            $allowance = BusinessTripAllowance::findOrFail(
                decrypt($id)
            );

            $allowance->update([
                'amount'   => $validated['amount'],
                'currency' => strtoupper($validated['currency']),
            ]);

            return response()->json([
                'message' => 'Allowance berhasil diperbarui'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }
    public function destroy($id)
    {
        try {
            $allowance = BusinessTripAllowance::findOrFail(decrypt($id));
            $allowance->delete();
            return response()->json([
                'success' => true,
                'message' => 'Business trip allowance berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
}
