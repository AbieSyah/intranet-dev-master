<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\ServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ServiceCatalogController extends Controller
{
    public $categories = [
        ServiceCatalog::CATEGORY_BUSINESS_APP,
        ServiceCatalog::CATEGORY_COMMUNICATION,
        ServiceCatalog::CATEGORY_HARDWARE,
        ServiceCatalog::CATEGORY_INFRASTRUCTURE,
        ServiceCatalog::CATEGORY_SOFTWARE,
    ];

    public function getData() {
        $query = ServiceCatalog::query()->orderBy('category', 'asc');

        // $catalogs->each(function($catalog) {
        //     $data[$catalog->category][] = $catalog->service_catalog;
        // });

        // dd($catalogs, $data);
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
        
        return view('pages.administrator.service-management.service-catalog.index', [
            'categories' => $this->categories
        ]);
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
        // dd($request->all());
        $request->validate([
            'category' => "required|in:".implode(',', $this->categories), 
            'service_catalog' => 'required',
            'description' => 'required',
        ]);
        $catalog = ServiceCatalog::create($request->all());
        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'insert',
            'description' => "{$user->employee->fullname} create new Service Catalog {$catalog->category} - {$catalog->service_catalog}"
        ]);
        return response()->json(['message' => 'Data created successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($id) {
            $serviceCatalog = ServiceCatalog::findOrFail(decrypt($id));
            $serviceCatalog->encrypted_id = encrypt($serviceCatalog->id);
            return response()->json($serviceCatalog);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category' => "required|in:".implode(',', $this->categories), 
            'service_catalog' => 'required',
            'description' => 'required',
        ]);
        $catalog = ServiceCatalog::findOrFail(decrypt($id));
        $catalog->update($request->all());

        $user = Auth::user();

        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'update',
            'description' => "{$user->employee->fullname} modify Service Catalog {$catalog->category} - {$catalog->service_catalog}"
        ]);

        return response()->json(['message' => 'Data updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $catalog = ServiceCatalog::findOrFail(decrypt($id));
        ServiceCatalog::destroy($catalog->id);

        $user = Auth::user();

        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'delete',
            'description' => "{$user->employee->fullname} delete Service Catalog {$catalog->category} - {$catalog->service_catalog}"
        ]);
        
        return response()->json(['message' => 'Data removed!']);
    }
}
