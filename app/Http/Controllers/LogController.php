<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function index(Request $request){
        $year = date('Y');
        $min = $year - 2;
        $max = $year;
        $month = date('m');

        if(!empty($request->bulan) && !empty($request->tahun)){
            $form_bulan = $request->bulan;
            $form_tahun = $request->tahun;
            $logs = Log::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])->whereYear('created_at', $form_tahun)->whereMonth('created_at', $form_bulan)->orderBy('id', 'desc')->get();
        }else{
            $logs = Log::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])->whereYear('created_at', $year)->whereMonth('created_at', $month)->orderBy('id', 'desc')->get();
        }
        $document = array();
        if($logs->isNotEmpty()){
            foreach($logs as $log){
                $index = $log->id;
                $document[$index] = array();
                // $document[$index]['id'] = $log->id;
                $document[$index]['datetime'] = date("Y-m-d H:i:s", strtotime($log->created_at));
                $document[$index]['name'] = $log->user->name ?? '-';
                $document[$index]['address'] = $log->ip_address;
                $document[$index]['action'] = $log->action;
                $document[$index]['description'] = $log->description;
            }
        }
        if ($request->ajax()) {
            return Datatables::of($document)
                ->addColumn('action', function($document){
                    $action = strtolower($document['action']); 
                    if($action == 'insert') return '<span class="badge text-bg-info">Insert</span>';
                    if($action == 'update') return '<span class="badge text-bg-warning">Update</span>';
                    if($action == 'delete') return '<span class="badge text-bg-danger">Delete</span>';
                    if($action == 'login') return '<span class="badge text-bg-secondary">Login</span>';
                    if($action == 'logout') return '<span class="badge text-bg-dark">Logout</span>';
                    if($action == 'closing') return '<span class="badge text-bg-success">Closing</span>';
                    if($action == 'approved') return '<span class="badge text-bg-primary">Approve</span>';
                    if($action == 'revised') return '<span class="badge text-bg-danger">Revised</span>';
                    if($action == 'verified') return '<span class="badge text-bg-primary">Verified</span>';
                    if($action == 'verificate') return '<span class="badge text-bg-primary">Verificate</span>';
                    if($action == 'import') return '<span class="badge text-bg-warning">Import</span>';
                    if($action == 'reject') return '<span class="badge text-bg-danger">Reject</span>';
                    
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.administrator.log.index', compact('min','max','month','year'));
    }
}
