<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pkb;
use App\Models\Log;
use Carbon\Carbon;
use Response;
use Auth;
use Yajra\DataTables\Facades\DataTables;

class PKBController extends Controller
{
    public function index(Request $request){
        $query = Pkb::get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['nama'] = $qry->nama;
                $data[$index]['periode'] = date('Y', strtotime($qry->tgl_berlaku)).' Sampai '.date('Y', strtotime($qry->tgl_berakhir));
                $data[$index]['isi'] = $qry->isi;
                $data[$index]['file_pkb'] = $qry->file_pkb;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.pkb.update')){
                        $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-quill-pen-line"></i></button>';
                    }else{
                        $button = '';
                    }
                    $button .= '&nbsp;';
                    // $button .= '<button data-id="' . route('lampiran.pkb', encrypt($data['id'])) . '" data-bs-target="#modalbacaPKB" title="Preview" class="btn btn-danger btn-sm view-btn><i class="ri-book-read-line"></i>test</button>';
                    if(\Auth::user()->can('hrd.pkb.pdf')){
                        $button .= '<button data-id="'. route('lampiran.pkb',encrypt($data['id'])) .'" data-bs-toggle="modal" data-bs-target="#modalbacaPKB" title="Preview" class="btn btn-danger btn-sm view-btn"><i class="ri-file-pdf-line"></i></button>';
                    }else{
                        $button = '';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.internal-rules.index');
    }

    public function edit(request $request){
        $id = decrypt($request->input('id'));
        $pkb = Pkb::find($id);

        return response()->json($pkb);
    }

    public function store(Request $request){
        $periode = $request->input('periode_pkb');
        $explode = explode(' to ', $periode);
        $d_file = $request->file('file_pkb');        
        if(!empty($d_file)){
            $nama_file = time().'.'.$d_file->getClientOriginalExtension();

            $data = [
                'id' => $request->input('id_pkb'),
                'nama' => $request->input('nama_pkb'),
                'tgl_berlaku' => $explode[0],
                'tgl_berakhir' => $explode[1],
                'isi' => $request->input('isi_pkb'),
                'file_pkb' => $nama_file
            ];
        }else{
            $data = [
                'id' => $request->input('id_pkb'),
                'nama' => $request->input('nama_pkb'),
                'tgl_berlaku' => $explode[0],
                'tgl_berakhir' => $explode[1],
                'isi' => $request->input('isi_pkb')
            ];
        }
        $post = Pkb::updateOrCreate(['id' => $data['id']], $data);

        if ($post->wasRecentlyCreated) {
            //upload file
            $pkb_file = $request->file('file_pkb');
            $pkb_name = time().'.'.$pkb_file->getClientOriginalExtension();
            $request->file_pkb->storeAs('public/pkb', $pkb_name);

            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create New PKB '.'"'.$data['nama'].'"';
            $insert->save();        
        }else{
            //upload file
            $query = Pkb::find($data['id']); 
            if(!empty($query->file_pkb)){
                if(!empty($request->file('file_pkb'))){
                    $cek_file = storage_path('app/public/pkb/'.$query->file_pkb);
                    if (File::exists($cek_file)) {
                        File::delete($cek_file);
                    }
                    $pkb_file = $request->file('file_pkb');
                    $pkb_name = time().'.'.$pkb_file->getClientOriginalExtension();
                    $request->file_pkb->storeAs('public/pkb', $pkb_name);

                    $query->update(['file_pkb' => $pkb_name]);
                }
            }else{
                if(!empty($request->file('file_pkb'))){
                    $pkb_file = $request->file('file_pkb');
                    $pkb_name = time().'.'.$pkb_file->getClientOriginalExtension();
                    $request->file_pkb->storeAs('public/pkb', $pkb_name);

                    $query->update(['file_pkb' => $pkb_name]);
                }
            }

            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify PKB '.'"'.$data['nama'].'"';
            $insert->save();
        }
        return redirect(route('internal-rule.index'))->with('pkb','open tab')->with('success', 'Add PKB Successfully.');
    }

    public function lampiran_pkb($id){
        $query = Pkb::find(decrypt($id));
        $lampiran_pkb = public_path('storage/pkb/'.$query->file_pkb);

        $file = File::get($lampiran_pkb);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'filename=' . '"'.$query->nama.'.pdf"');
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }

    //start emp
    public function emp_index(Request $request){
        $user = auth()->user();
        $pkb = Pkb::where('status','active')->first();
        $url_pkb = URL::asset('storage/pkb/'.$pkb->file_pkb);
        return view('pages.employee.pkb.index', compact('user','url_pkb'));
    }
    //end emp
}
