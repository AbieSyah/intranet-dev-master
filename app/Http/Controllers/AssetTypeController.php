<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AssetTypeController extends Controller
{
    public function getData() {
        $query = AssetType::query();
        return DataTables::of($query)
            ->addColumn('encrypted_id', function($row) {
                return encrypt($row->id);
            })
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.administrator.it-asset.asset-type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:asset_types,name', 'estimated_lifespan' => 'required|numeric']);
        AssetType::create($request->all());
        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'insert',
            'description' => "{$user->employee->fullname} create new Asset Type"
        ]);
        return response()->json(['message' => 'Data created successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($id) {
            $assetType = AssetType::findOrFail(decrypt($id));
            $assetType->encrypted_id = encrypt($assetType->id);
            return response()->json($assetType);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required', 'estimated_lifespan' => 'required|numeric']);
        $asset = AssetType::findOrFail(decrypt($id));
        $asset->update($request->all());

        $user = Auth::user();

        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "{$user->employee->fullname} modify Asset Type({$asset->name})"
        ]);

        return response()->json(['message' => 'Data updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $asset = AssetType::findOrFail(decrypt($id));
        AssetType::destroy($asset->id);

        $user = Auth::user();

        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'delete',
            'description' => "{$user->employee->fullname} delete Asset Type({$asset->name})"
        ]);
        
        return response()->json(['message' => 'Data deleted successfully!']);
    }
}
