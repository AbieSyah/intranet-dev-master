<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Area;
use App\Models\Level;
use App\Models\Position;
use App\Models\Internalrule;
use App\Models\Log;
use App\Models\Permissioninternalrule;
use Carbon\Carbon;
use Response;
use Auth;
use Yajra\DataTables\Facades\DataTables;

class InternalruleController extends Controller
{
    public function index(Request $request){
        $departments = Department::all();
        $levels = Level::all();
        $positions = Position::all();
        $areas = Area::all();
        $employees = Employee::all(); 
        $query = Internalrule::where('status', 'active')->orWhereNull('status')->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['nama'] = $qry->nama;
                $data[$index]['tgl_berlaku'] = $qry->tgl_berlaku;
                $data[$index]['isi'] = $qry->isi;
                // if(!empty($qry->status)){
                //     $data[$index]['status'] = $qry->status;
                // }else{
                //     $data[$index]['status'] = 'active';
                // }
                if(!empty($qry->tgl_revisi)){
                    $data[$index]['tgl_revisi'] = $qry->tgl_revisi;
                }else{
                    $data[$index]['tgl_revisi'] = '-';
                }
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                // ->addColumn('status', function ($data) {
                //     return '<a href="#"><span class="badge text-bg-success">Active</span></a>';
                // })
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.internal-rules.update')){
                        $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-quill-pen-line"></i></button><input type="hidden" id="preview" name="preview" value="'. route('lampiran.rule',encrypt($data['id'])) .'">';
                    }else{
                        $button = '';
                    }
                    $button .= '&nbsp;';
                    $button .= '<button data-id="'. route('lampiran.rule',encrypt($data['id'])) .'" data-bs-toggle="modal" data-bs-target="#modal-preview-pdf" title="Preview" class="btn btn-danger btn-sm preview-btn"><i class="ri-file-pdf-line"></i></button>';
                    // $button .= '<a href="'. route('lampiran.rule', encrypt($data['id'])).'" target="_blank" title="Preview" class="btn btn-danger btn-sm"><i class="ri-file-pdf-line"></i></a>';
                    // $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.internal-rules.setting')){
                        // $button .= '<button data-id="' . encrypt($data['id']) . '" data-bs-toggle="modal" data-bs-target="#modal-setting" title="Setting" class="btn btn-primary btn-sm setting-btn"><i class="ri-file-settings-line"></i></button>';
                        $button .= '';
                    }else{
                        $button .= '';
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.internal-rules.revisi')){
                        $button .= '<button data-id="' . encrypt($data['id']) . '" data-bs-toggle="modal" data-bs-target="#modal-revisi" title="Revisi" class="btn btn-secondary btn-sm revisi-btn"><i class="ri-edit-box-line"></i></button><input type="hidden" id="preview_revisi" name="preview_revisi" value="'. route('lampiran.rule',encrypt($data['id'])) .'">';
                    }else{
                        $button .= '';
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.internal-rules.detail')){
                        $button .= '<a href="'. route('internal-rule.status', encrypt($data['id'])).'" title="History" class="btn btn-info btn-sm"><i class="ri-file-history-line"></i></a>';
                    }else{
                        $button .= '';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.internal-rules.index', compact('departments','levels','positions','query','areas','employees'));
    }

    public function status(Request $request, $id){
        $rule = Internalrule::find(decrypt($id));
        $query = Internalrule::where('nama', $rule->nama)->orderBy('id', 'desc')->get();
        // dd($query);
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id; 
                $data[$index]['tgl_berlaku'] = $qry->tgl_berlaku; 
                $data[$index]['isi'] = $qry->isi;
                if(!empty($qry->status)){
                    $data[$index]['status'] = $qry->status;
                }else{
                    $data[$index]['status'] = 'active';
                }
                if(!empty($qry->tgl_kedaluwarsa)){
                    $data[$index]['tgl_kedaluwarsa'] = $qry->tgl_kedaluwarsa; 
                }else{
                    $data[$index]['tgl_kedaluwarsa'] = '-'; 
                }                
            }
        }else{
            $data = array();
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('status', function ($data) {
                    if($data['status'] == 'active'){
                        return '<a href="#"><span class="badge text-bg-success">Active</span></a>';
                    }else{
                        return '<a href="#"><span class="badge text-bg-danger">'.$data['status'].'</span></a>';
                    }
                })
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.internal-rules.pdf')){
                        $button = '<button data-id="'. route('lampiran.rule',encrypt($data['id'])) .'" data-bs-toggle="modal" data-bs-target="#modal-preview" title="Preview" class="btn btn-danger btn-sm preview-btn"><i class="ri-file-pdf-line"></i></button>';
                    }else{
                        $button = '';
                    }
                    return $button;
                })
                ->rawColumns(['action','status'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.internal-rules.status', compact('id', 'rule'));
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $rule = Internalrule::find($id);

        return response()->json($rule);
    }

    public function store(Request $request){
        $d_file = $request->file('file');
        if(!empty($d_file)){
            $nama_file = time().'.'.$d_file->getClientOriginalExtension();

            $data = [
                'id' => $request->input('id'),
                'nama' => $request->input('nama'),
                'tgl_berlaku' => $request->input('tgl_berlaku'),
                'isi' => $request->input('isi'),
                'file' => $nama_file
            ];
        }else{
            $data = [
                'id' => $request->input('id'),
                'nama' => $request->input('nama'),
                'tgl_berlaku' => $request->input('tgl_berlaku'),
                'isi' => $request->input('isi')
            ];
        }        
        $post = Internalrule::updateOrCreate(['id' => $data['id']], $data);
        
        if ($post->wasRecentlyCreated) {
            //upload file
            $rule_file = $request->file('file');
            $rule_name = time().'.'.$rule_file->getClientOriginalExtension();
            $request->file->storeAs('public/rules', $rule_name);

            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create New Internal Rule '.'"'.$data['nama'].'"';
            $insert->save();        
        }else{
            //upload file
            $query = Internalrule::find($data['id']); 
            if(!empty($query->file)){
                if(!empty($request->file('file'))){
                    $cek_file = storage_path('app/public/rules/'.$query->file);
                    if (File::exists($cek_file)) {
                        File::delete($cek_file);
                    }
                    $rule_file = $request->file('file');
                    $rule_name = time().'.'.$rule_file->getClientOriginalExtension();
                    $request->file->storeAs('public/rules', $rule_name);

                    $query->update(['file' => $rule_name]);
                }
            }else{
                if(!empty($request->file('file'))){
                    $rule_file = $request->file('file');
                    $rule_name = time().'.'.$rule_file->getClientOriginalExtension();
                    $request->file->storeAs('public/rules', $rule_name);

                    $query->update(['file' => $rule_name]);
                }
            }

            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify Internal Rule '.'"'.$data['nama'].'"';
            $insert->save();
        }

        return redirect()->route('internal-rule.index')->with('success', 'Add Internal Rule Successfully.');
    }

    public function lampiran_rule($id){
        $query = Internalrule::find(decrypt($id));
        $lampiran_rule = public_path('storage/rules/'.$query->file);
        
        // return response()->file($lampiran_rule);
        $file = File::get($lampiran_rule);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'filename=' . '"'.$query->nama.'.pdf"');
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }

    public function download_rule($id){
        $query = Internalrule::find(decrypt($id));
        $unduh_rule = public_path('storage/rules/'.$query->file);
        
        return response()->download($unduh_rule);
    }

    public function edit_setting(Request $request){
        $id = decrypt($request->input('id'));
        $data['rule'] = Internalrule::find($id);
        $query = Permissioninternalrule::where('id_internal_rule', $id)->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $arr_dept[] = $qry->id_dept;
                $uniq_dept = array_unique($arr_dept);
                foreach($uniq_dept as $key =>$val){
                    if($qry->id_dept == $val){
                        $arr_data[$qry->id_dept][] = $qry->id_level;
                    }
                }
            }
            $data['permission'] = $arr_data;
        }else{
            $data['permission'] = '';
        }
        return response()->json($data);
    }

    public function revisi(Request $request){
        // dd('maintenance');
        $old_rule = Internalrule::find($request->id_revisi);
        if(!empty($old_rule->rev)){
            $arr_rev = $old_rule->rev;
            $rev = (int)$arr_rev;
            $expdate = strtotime ( '-1 day' , strtotime ( $request->tgl_berlaku_revisi ) ) ;
            $expdate = date('Y-m-d', $expdate);
            $old_rule->update([
                'status' => 'Revisi '.$rev,
                'tgl_kedaluwarsa' => $expdate
            ]);

            //insert
            $d_file = $request->file('file_revisi');
            $nama_file = time().'.'.$d_file->getClientOriginalExtension();
            $request->file_revisi->storeAs('public/rules', $nama_file);

            $insert = new Internalrule;
            $insert->nama = $request->nama_revisi;
            $insert->tgl_berlaku = $request->tgl_berlaku_revisi;
            $insert->isi = $request->isi_revisi;
            $insert->status = 'active';  
            // $insert->tgl_revisi = date('Y-m-d');  
            $insert->file = $nama_file;  
            $insert->rev = ($rev+1);
            $insert->save();

            $rule = Internalrule::where('status', 'active')->latest()->first();

            //update permission rule
            $permission_rule = Permissioninternalrule::where('id_internal_rule', $old_rule->id)->update([
                'id_internal_rule' => $rule->id
            ]);

            
            $user = auth()->user();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'revised';
            $insert_log->description = 'Revised Internal Rule '.'"'.$request->nama_revisi.'"';
            $insert_log->save();
        }else{
            $expdate = strtotime ( '-1 day' , strtotime ( $request->tgl_berlaku_revisi ) ) ;
            $expdate = date('Y-m-d', $expdate);
            $old_rule->update([
                'status' => 'Revisi 0',
                'tgl_kedaluwarsa' => $expdate,
                'rev' => '0'
            ]);

            //insert
            $d_file = $request->file('file_revisi');
            $nama_file = time().'.'.$d_file->getClientOriginalExtension();
            $request->file_revisi->storeAs('public/rules', $nama_file);

            $insert = new Internalrule;
            $insert->nama = $request->nama_revisi;
            $insert->tgl_berlaku = $request->tgl_berlaku_revisi;
            $insert->isi = $request->isi_revisi;
            $insert->status = 'active';  
            // $insert->tgl_revisi = date('Y-m-d');  
            $insert->file = $nama_file;  
            $insert->rev = '1';
            $insert->save();

            $rule = Internalrule::where('status', 'active')->latest()->first();

            //update permission rule
            $permission_rule = Permissioninternalrule::where('id_internal_rule', $old_rule->id)->update([
                'id_internal_rule' => $rule->id
            ]);
            
            $user = auth()->user();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'revised';
            $insert_log->description = 'Revised Internal Rule '.'"'.$request->nama_revisi.'"';
            $insert_log->save();        
        }

        return redirect()->route('internal-rule.index')->with('success', 'Revised Internal Rule Successfully.');
    }

    public function setting(Request $request){
        // dd('maintenance');
        $data = $request->input();
        $rule = Internalrule::find(decrypt($data['id_set']));
        for($i = 0; $i < count($data['no_urut']); $i++){
            if($data['id_dept-'.$data['no_urut'][$i]] && $data['id_level-'.$data['no_urut'][$i]]){
                $id_dept = $data['id_dept-'.$data['no_urut'][$i]];
                $id_level = $data['id_level-'.$data['no_urut'][$i]];
            }

            $arr_data = [
                'id_internal_rule' => decrypt($data['id_set']),
                'id_dept' => $id_dept[0],
                'id_level' => $id_level
            ];

            for($a = 0; $a < count($arr_data['id_level']); $a++){
                $insert[] = [
                    'id_internal_rule' => $arr_data['id_internal_rule'],
                    'id_dept' => $arr_data['id_dept'],
                    'id_level' => $arr_data['id_level'][$a]
                ];
            }
        }

        $delete = Permissioninternalrule::where('id_internal_rule', decrypt($data['id_set']))->delete();
        $post = Permissioninternalrule::insert($insert);        

        $user = auth()->user();
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'Setting Internal Rule '.'"'.$rule->nama.'"';
        $insert->save();

        return redirect()->route('internal-rule.index')->with('success', 'Set Internal Rule Successfully.');
    }

    //start benefit
    public function index_benefit(Request $request){
        $query = Permissioninternalrule::with('internalrule','area')->get()->unique('benefit');        
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->benefit;
                $data[$index] = array();
                $data[$index]['id'] = $qry->benefit;
                $data[$index]['benefit'] = $qry->benefit;
                if(!empty($qry->id_internal_rule)){
                    $data[$index]['id_internal_rule'] = $qry->internalrule->nama;
                }else{
                    $data[$index]['id_internal_rule'] = '-';
                }
                // if(!empty($qry->value_nominal)){
                //     $data[$index]['value'] = number_format($qry->value_nominal,0) ?? '-';
                // }else{
                //     $data[$index]['value'] = $qry->value_textual ?? '-';
                // }
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.benefit.setting')){
                        $button = '<button data-id="' . $data['id'] . '" data-bs-toggle="modal" data-bs-target="#modal-setting-benefit" title="Setting" class="btn btn-primary btn-sm setting-benefit-btn"><i class="ri-file-settings-line"></i></button>';
                    }else{
                        $button = '';
                    }
                    $button .= ' ';
                    if(\Auth::user()->can('hrd.benefit.delete')){
                        $button .= '<button data-id="' . $data['id'] . '" data-bs-toggle="modal" data-bs-target="#modal-delete-benefit" title="Delete" class="btn btn-danger btn-sm delete-benefit-btn"><i class="ri-delete-bin-line"></i></button>';
                    }else{
                        $button.= '';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.internal-rules.index');
    }

    public function edit_setting_benefit(Request $request){
        $id = $request->input('id');
        $query = Permissioninternalrule::with('internalrule','area')->where('benefit', $id)->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $arr_level[] = $qry->id_level;
                $uniq_level = array_unique($arr_level);
                $arr_area[] = $qry->id_area;
                $uniq_area = array_unique($arr_area);
                foreach($uniq_level as $key =>$val){
                    if($qry->id_level == $val){
                        $arr_data[$val]['level_id'] = $qry->id_level;
                        $arr_data[$val]['rule_id'] = $qry->id_internal_rule;
                        $arr_data[$val]['benefit'] = $qry->benefit;
                        $arr_data[$val]['nominal'] = $qry->value_nominal;
                        $arr_data[$val]['textual'] = $qry->value_textual;
                        $arr_data[$val]['area_id'][] =  $qry->id_area;
                        $arr_data[$val]['employee_id'][] =  $qry->id_employee;
                    }
                }
            }
            $data['rule'] = $query->unique('id_internal_rule')->value('id_internal_rule');
            $data['area'] = $query->unique('id_area')->value('id_area');
            $data['permission'] = $arr_data;
        }else{
            $data['permission'] = '';
        }
        return response()->json($data);
    }

    public function store_benefit(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            if($request->id_rule == 'none'){
                $id_rule = null;
            }else{
                $id_rule = $request->id_rule;
            }
            //multiple insert
            // for($i = 0; $i < count($request->id_area); $i++){
            //     $insert[] = [
            //         'id_internal_rule' => $id_rule,
            //         'id_area' => $request->id_area[$i],
            //         'benefit' => $request->benefit
            //         // 'value_nominal' => $request->value_nominal,
            //         // 'value_textual' => $request->value_textual,
            //         // 'id_level' => $request->id_level[$i]
            //     ];
            // }
            // $post = Permissioninternalrule::insert($insert);

            $cek_benefit = Permissioninternalrule::where('benefit', $request->benefit)->first();
            if(empty($cek_benefit)){
                //insert benefit
                $insert_b = new Permissioninternalrule;
                $insert_b->id_internal_rule = $id_rule;
                // $insert_b->id_area = $request->id_area;
                $insert_b->benefit = $request->benefit;
                $insert_b->save();
                
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'insert benefit '.'"'.$request->benefit.'"';
                $insert->save();
    
                DB::commit();
    
                return response()->json(['message' => "$request->benefit has been saved"], 200);
            }else{
                DB::rollback();
                return response()->json(['message' => "$request->benefit sudah ada silahkan masukkan benefit berbeda !"], 500);
            }
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function setting_benefit(Request $request){        
        DB::beginTransaction();
        try{

            $user = auth()->user();
            if(!empty($request->no_urut)){
                $data = $request->input();
                $nama_benefit = $data['benefit'];
                if($data['id_rule'] == 'none'){
                    $id_rule = null;
                }else{
                    $id_rule = $data['id_rule'];
                }
                for($i = 0; $i < count($data['no_urut']); $i++){
                    if($data['id_level-'.$data['no_urut'][$i]] && $data['id_employee-'.$data['no_urut'][$i]] && $data['id_area-'.$data['no_urut'][$i]] && $data['value_nominal-'.$data['no_urut'][$i]] && $data['value_textual-'.$data['no_urut'][$i]]){
                        $id_level = $data['id_level-'.$data['no_urut'][$i]];
                        $id_area = $data['id_area-'.$data['no_urut'][$i]];
                        $id_employee = $data['id_employee-'.$data['no_urut'][$i]];
                        $value_nominal = $data['value_nominal-'.$data['no_urut'][$i]];
                        $value_textual = $data['value_textual-'.$data['no_urut'][$i]];
                    }
                    $arr_data = [
                        'id_internal_rule' => $id_rule,
                        'benefit' => $data['benefit'],
                        'value_nominal' => $value_nominal,
                        'value_textual' => $value_textual,
                        'id_level' => $id_level,
                        'id_employee' => $id_employee,
                        'id_area' => $id_area
                    ];

                    for($n = 0; $n < count($arr_data['id_area']); $n++){
                        $str_nominal = str_replace('.','', $arr_data['value_nominal'][0]);
                        $int_nominal = (int)$str_nominal;
                        $arr_insert = [
                        'id_internal_rule' => $arr_data['id_internal_rule'],
                        'id_area' => $arr_data['id_area'][$n],
                        'benefit' => $arr_data['benefit'],
                        'value_nominal' => $int_nominal,
                        'value_textual' => $arr_data['value_textual'][0],
                        'id_level' => $arr_data['id_level'][0],
                        'id_employee' => $arr_data['id_employee']
                        ];
                        
                        for($a = 0; $a < count($arr_insert['id_employee']); $a++){
                            $insert[] = [
                                'id_internal_rule' => $arr_insert['id_internal_rule'],
                                'id_area' => $arr_insert['id_area'],
                                'benefit' => $arr_insert['benefit'],
                                'value_nominal' => $arr_insert['value_nominal'],
                                'value_textual' => $arr_insert['value_textual'],
                                'id_level' => $arr_insert['id_level'],
                                'id_employee' => $arr_insert['id_employee'][$a]
                            ];
                        }
                    }
                } 
                $delete = Permissioninternalrule::where('benefit', $data['benefit'])->delete();
                $post = Permissioninternalrule::insert($insert);
                
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Setting Internal Rule Benefit '.'"'.$nama_benefit.'"';
                $insert->save();

                DB::commit();
    
                return response()->json(['message' => "Update $nama_benefit has been saved"], 200);
            }else{
                $delete = Permissioninternalrule::where('benefit', $request->benefit)->delete();
                $post_insert = new Permissioninternalrule;
                $post_insert->benefit = $request->benefit;
                if($request->id_rule == 'none'){
                    $post_insert->id_internal_rule = null;
                }else{
                    $post_insert->id_internal_rule = $request->id_rule;
                }
                $post_insert->save();

                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Setting Internal Rule Benefit '.'"'.$request->benefit.'"';
                $insert->save();

                DB::commit();
    
                return response()->json(['message' => "Update $request->benefit has been saved"], 200);
            }
    

        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function destroy_benefit(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'delete';
            $insert->description = 'Internal Rule Benefit '.'"'.$request->id_delete_benefit.'"';
            $insert->save();
            
            $delete = Permissioninternalrule::where('benefit', $request->id_delete_benefit)->delete();

            DB::commit();

            return response()->json(['message' => "Data has been deleted"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    //end benefit

    //start emp
    public function emp_index(Request $request){
        $user = auth()->user();
        // $permission_rule = Permissioninternalrule::whereIn('id_dept', [$user->employee->department_id,'all'])
        //     ->whereIn('id_level', [$user->employee->level_id,'all'])->get();
        // $arr_rule = $permission_rule->pluck('id_internal_rule');
        // $query = Internalrule::whereIn('id', $arr_rule)->get();
        $query = Internalrule::all();
        if($query->isNotEmpty()){
            foreach ($query as $qry) {
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['nama'] = $qry->nama;
                $data[$index]['tgl_berlaku'] = date('d F Y', strtotime($qry->tgl_berlaku));
                $data[$index]['isi'] = $qry->isi;
                $data[$index]['file'] = $qry->file;
                // if(!empty($qry->file)){
                //     // $url = URL::asset('storage/rules/'.$qry->file);
                //     // $data[$index]['url'] = 'https://docs.google.com/viewer?url='.$url.'&embedded=true';
                //     $data[$index]['url'] = route('emp.lampiran.rule', encrypt($qry->id));
                // }else{
                //     $data[$index]['url'] = '#';
                // }
            }
        }else{
            $data = array();
        }
        if($request->ajax()){
            return DataTables::of($data)
            ->addColumn('action', function ($data) {
                // $button = '<button data-id="'. $data['url'] .'" data-bs-toggle="modal" data-bs-target="#modal-preview" title="Preview" class="btn btn-danger btn-sm preview-btn"><i class="ri-file-pdf-line"></i> Show</button>';               
                if(!empty($data['file'])){
                    if(\Auth::user()->can('emp.internal-rule.pdf')){
                    $button = '<a href="'.route('emp.lampiran.rule', encrypt($data['id'])).'" target="_blank" class="btn btn-danger btn-sm"><i class="ri-file-pdf-line me-1 align-bottom"></i> Show</a>';
                    }else{
                        $button = '';
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('emp.internal-rule.download')){
                        $button .= '<a href="'.route('emp.download.rule', encrypt($data['id'])).'" class="btn btn-success btn-sm"><i class="ri-file-pdf-line me-1 align-bottom"></i> Download</a>';
                    }else{
                        $button .= '&nbsp;';
                    }
                }else{
                    $button = '';
                }
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }

        return view('pages.employee.internal-rule.index', compact('user'));
    }

    public function emp_lampiran_rule($id){
        $query = Internalrule::find(decrypt($id));
        $lampiran_rule = public_path('storage/rules/'.$query->file);

        return response()->file($lampiran_rule);
    }

    public function emp_download_rule($id){
        $query = Internalrule::find(decrypt($id));
        $unduh_rule = public_path('storage/rules/'.$query->file);
        return response()->download($unduh_rule);
    }

    public function emp_benefit(Request $request){
        $user = auth()->user();
        $emp_id = $user->employee->id;
        $emp_area = $user->employee->area_id;
        $emp_level = $user->employee->level_id;
        $benefits = Permissioninternalrule::with('internalrule','area','employee','level')->whereIn('id_employee', [$emp_id,'all'])->whereIn('id_area', [$emp_area,'all'])->whereIn('id_level', [$emp_level,'all'])->get();
        return view('pages.employee.benefit.index', compact('user','benefits'));
    }

    public function emp_benefit_rule($id){
        $query = Internalrule::find(decrypt($id));
        $lampiran_rule = public_path('storage/rules/'.$query->file);
        // $url_rule = URL::asset('storage/rules/'.$query->file);
        return response()->file($lampiran_rule);
    }
    //end emp
}
