<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Master\Drug;
use App\Models\Log;
use App\Models\Master\Contract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    public function drug_index(Request $request){
        if ($request->ajax()) {
            $query = Drug::all();
            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    return $button;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.drug.index');
    }

    public function drug_edit(Request $request){
        $id = decrypt($request->input('id'));
        $drug = Drug::find($id);

        if (!$drug) {
            return response()->json(['message' => 'Drug not found'], 404);
        }

        return response()->json($drug);
    }

    public function drug_store(Request $request){
        DB::beginTransaction();

        try {
            $id = $request->input('id');
            $name = $request->input('nama');

            $drug = Drug::updateOrCreate(['id' => $id], ['nama' => $name]);

            DB::commit();

            if ($drug->wasRecentlyCreated) {
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Create New Drug '.'"'.$name.'"';
                $insert->save();
            }else{
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify Drug '.'"'.$name.'"';
                $insert->save();
            }


            return response()->json(['message' => "$drug->nama has been saved"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function contract_index(Request $request){
        $contracts = Contract::all();
        $data = array();
        if($contracts->isNotEmpty()){
            foreach ($contracts as $contract) {
                $index = $contract->id;
                $data[$index] = array();
                $data[$index]['id'] = $contract->id;
                $data[$index]['name'] = $contract->name;
            }
        }else{
            $data = array();
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '';
                    if (Auth::user()->can('hrd.master.contract.update')) {
                        $button .= '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    }
                    if (Auth::user()->can('hrd.master.contract.delete')) {
                        $button .= ' <button data-toggle="tooltip" title="Delete" data-id="' . encrypt($data['id']) . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.contract.index');
    }

    public function contract_form(Request $request){
        $id = decrypt($request->input('id'));
        $contract = Contract::find($id);
        return response()->json($contract);
    }

    public function contract_store(Request $request){
        $data = $request->all();
        $contract = Contract::updateOrCreate(['id' => $data['id']], $data);
        if ($contract->wasRecentlyCreated) {
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => 'Create New Contract '.'"'.$data['name'].'"',
            ]);
        }else{
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => 'Modify Contract '.'"'.$data['name'].'"',
            ]);
        }
        return redirect()->route('contract.index')->with('success', 'Contract '.'"'.$data['name'].' has been saved');
    }

    public function contract_destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $contract = Contract::findOrFail($id);
            $contractName = $contract->name ?? '-';
            $contract->delete();
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => 'Delete Contract "' . $contractName . '"',
            ]);
            return redirect()->route('contract.index')->with('success', 'Contract "' . $contractName . '" has been deleted');
        } catch (Exception $e) {
            return redirect()->route('contract.index')->with('error', 'Failed to delete Contract: ' . $e->getMessage());
        }
    }
}
