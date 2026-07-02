<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Log;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    public function index(Request $request){
        $vendors = Vendor::all();
        if($vendors->isNotEmpty()){
            foreach ($vendors as $vendor) {
                $index = $vendor->id;
                $data[$index] = array();
                $data[$index]['id'] = $vendor->id;
                $data[$index]['nama'] = $vendor->nama;
                $data[$index]['alamat'] = $vendor->alamat;
                $data[$index]['tipe'] = $vendor->tipe;
            }
        }else{
            $data = array();
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.master.vendor.update')){
                        $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    }else{
                        $button = '';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.vendor.index');
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $vendor = Vendor::find($id);

        return response()->json($vendor);
    }

    public function store(Request $request){
        $data = $request->all();
        $vendor = Vendor::updateOrCreate(['id' => $data['id']], $data);

        if ($vendor->wasRecentlyCreated) {
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create New Vendor '.'"'.$data['nama'].'"';
            $insert->save();        
        }else{
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify Vendor '.'"'.$data['nama'].'"';
            $insert->save();        
        }

        return redirect()->route('vendor.index')->with('success', 'Add Vendor Successfully.');
    }
}
