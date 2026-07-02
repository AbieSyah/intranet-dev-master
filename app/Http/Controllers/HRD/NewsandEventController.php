<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Calendar;
use App\Models\News;
use App\Models\Log;
use Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class NewsandEventController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $query = News::orderBy('tanggal_news','desc')->get();

            return DataTables::of($query)
                ->addColumn('tumbnail', function($data){
                    if(!empty($data->tumbnail)){
                        $tumbnail = asset('storage/tumbnail/'.$data->tumbnail);
                        return '<img src="'.$tumbnail.'" width="100px"/>';                   
                    }else{
                        return '-';                   
                    }
                })
                ->addColumn('tanggal', function($data){
                    if(!empty($data->tanggal_news)){
                        return date('d M Y', strtotime($data->tanggal_news));
                    }else{
                        return '-';
                    }
                })
                ->addColumn('status', function($data){
                    if($data->status == 'release'){
                        return '<span class="badge text-bg-success">RELEASED</span>';
                    }else{
                        return '<span class="badge text-bg-warning">DRAFT</span>';
                    }
                })
                ->addColumn('action', function ($data) {
                    if($data->status == 'draft'){
                        $preview = '<li><a href="'.route('news-and-event.preview', encrypt($data->id)).'" class="dropdown-item"><i class="ri-eye-line align-bottom me-2 text-muted"></i> Preview</a></li>';
                    }else{
                        $preview = '';
                    }
                    $edit = '<li><a href="'.route('news-and-event.form', encrypt($data->id)).'" class="dropdown-item"><i class="ri-edit-box-line align-bottom me-2 text-muted"></i> Edit</a></li>';
                    $hapus = '<li><a href="#" data-id="' . encrypt($data->id) . '" class="dropdown-item delete-btn"><i class="ri-delete-bin-line align-bottom me-2 text-muted"></i> Delete</a></li>';
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$preview.$edit.$hapus.'</ul></div>';
                    return $button;
                })
                ->rawColumns(['tumbnail','tanggal','status','action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.news-event.index');
    }

    public function preview(Request $request, $id){
        $year = date('Y');
        $date_now = date('Y-m-d');
        $news = News::where('id', decrypt($id))->first();
        $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $end = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
                    $data['dateup'] = $start.' to '.$end;
                }else{
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = null;
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $data['dateup'] = $start;
                }
                $data_all[] = $data;
            }
        }else{
            $data_all = array();
        }
        return view('pages.hrd.news-event.preview', compact('news','data_all','date_now'));
    }

    public function preview_detail(Request $request, $id){
        $news = News::find(decrypt($id));
        if(!empty($news->gambar)){
            $arr_konten = explode(',', $news->gambar);
        }else{
            $arr_konten = null;
        }
        $lampiran = route('news-and-event.lampiran', encrypt($news->id));
        return view('pages.hrd.news-event.preview-detail', compact('news','arr_konten','lampiran'));
    }

    public function form(Request $request, string $id = null){
        if ($id) $id = decrypt($id);
        $news = News::find($id);
        if(!empty($news->gambar)){
            $arr_gambar = explode(',', $news->gambar); 
        }else{
            $arr_gambar = null;
        }

        return view('pages.hrd.news-event.form', compact('news','arr_gambar'));
    }

    public function store(Request $request){
        if($request->action == 'release'){            
            //insert or update
            $news = News::where('id',$request->id)->first();
            if(!empty($news)){
                if($request->input('images', [])){
                    $images = implode(',', $request->input('images', []));
                }else{
                    $images = $news->gambar;
                }
                if($request->file('tumbnail')){
                    $cek_file = storage_path('app/public/tumbnail/'.$news->tumbnail);
                    if (File::exists($cek_file)) {
                        File::delete($cek_file);
                    }
                    $tumbnail = $request->file('tumbnail');
                    // dd($tumbnail);
                    $nama_tumbnail = time().'.'.$tumbnail->getClientOriginalExtension();
                    $request->tumbnail->storeAs('public/tumbnail', $nama_tumbnail);
                }else{
                    $nama_tumbnail = $news->tumbnail;
                }
                if($request->file('lampiran')){
                    $cek_lampiran = storage_path('app/public/lampiran_konten/'.$news->lampiran);
                    if (File::exists($cek_lampiran)) {
                        File::delete($cek_lampiran);
                    }
                    $lampiran = $request->file('lampiran');
                    $nama_lampiran = time().'.'.$lampiran->getClientOriginalExtension();
                    $request->lampiran->storeAs('public/lampiran_konten', $nama_lampiran);
                }else{
                    $nama_lampiran = $news->lampiran;
                }

                if(!empty($request->judul)){
                    $judul = $request->judul;
                }else{
                    $judul = $news->judul;
                }
                if(!empty($request->detail)){
                    $detail = $request->detail;
                }else{
                    $detail = $news->detail;
                }
                if(!empty($request->link_video)){
                    $link_video = $request->link_video;
                }else{
                    $link_video = $news->link_video;
                }

                $update = News::where('id', $request->id)->update([
                    'judul' => $judul,
                    'tanggal_news' => $request->tanggal_news,
                    'detail' => $detail,
                    'tumbnail' => $nama_tumbnail,
                    'gambar' => $images,
                    'link_video' => $link_video,
                    'lampiran' => $nama_lampiran,
                    'status' => 'release'
                ]);

                //cek storage konten
                $files = Storage::allFiles('public/konten');
                $fileNames = array_map(function($file){
                    return basename($file); // remove the folder name
                }, $files);
                foreach($fileNames as $key => $value){
                    $query = News::where('gambar','like','%'.$value.'%')->first();
                    if(!empty($query)){
                        $gambar_ada = $value;
                    }else{
                        $gambar_kosong = storage_path('app/public/konten/'.$value); 
                        if (File::exists($gambar_kosong)) {
                            File::delete($gambar_kosong);
                        }
                    }
                }

                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify News and Event '.'"'.$news->judul.'"';
                $insert->save();

                return redirect()->route('news-and-event.index')->with('status','Modify News and Event Successfully');
            }else{
                if($request->input('images', [])){
                    $images = implode(',', $request->input('images', []));
                }else{
                    $images = null;
                }
                $tumbnail = $request->file('tumbnail');
                // dd($tumbnail);
                $nama_tumbnail = time().'.'.$tumbnail->getClientOriginalExtension();
                $request->tumbnail->storeAs('public/tumbnail', $nama_tumbnail);
                if($request->file('lampiran')){
                    $lampiran = $request->file('lampiran');
                    $nama_lampiran = time().'.'.$lampiran->getClientOriginalExtension();
                    $request->lampiran->storeAs('public/lampiran_konten', $nama_lampiran);
                }else{
                    $nama_lampiran = null;
                }

                $insert = new News;
                $insert->judul = $request->judul;
                $insert->tanggal_news = $request->tanggal_news;
                $insert->detail = $request->detail;
                $insert->tumbnail = $nama_tumbnail;
                $insert->gambar = $images;
                $insert->link_video = $request->link_video;
                $insert->lampiran = $nama_lampiran;
                $insert->status = 'release';
                $insert->save();
    
                //cek storage konten
                $files = Storage::allFiles('public/konten');
                $fileNames = array_map(function($file){
                    return basename($file); // remove the folder name
                }, $files);
                foreach($fileNames as $key => $value){
                    $query = News::where('gambar','like','%'.$value.'%')->first();
                    if(!empty($query)){
                        $gambar_ada = $value;
                    }else{
                        $gambar_kosong = storage_path('app/public/konten/'.$value); 
                        if (File::exists($gambar_kosong)) {
                            File::delete($gambar_kosong);
                        }
                    }
                }

                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Create News and Event '.'"'.$request->judul.'"';
                $insert->save();

                return redirect()->route('news-and-event.index')->with('status','Create News and Event Successfully');
            }
        }

        if($request->action == 'draft'){
            $news = News::where('id',$request->id)->first();
            if(!empty($news)){
                if($request->input('images', [])){
                    $images = implode(',', $request->input('images', []));
                }else{
                    $images = $news->gambar;
                }
                if($request->file('tumbnail')){
                    $cek_file = storage_path('app/public/tumbnail/'.$news->tumbnail);
                    if (File::exists($cek_file)) {
                        File::delete($cek_file);
                    }
                    $tumbnail = $request->file('tumbnail');
                    $nama_tumbnail = time().'.'.$tumbnail->getClientOriginalExtension();
                    $request->tumbnail->storeAs('public/tumbnail', $nama_tumbnail);
                }else{
                    $nama_tumbnail = $news->tumbnail;
                }
                if($request->file('lampiran')){
                    $cek_lampiran = storage_path('app/public/lampiran_konten/'.$news->lampiran);
                    if (File::exists($cek_lampiran)) {
                        File::delete($cek_lampiran);
                    }
                    $lampiran = $request->file('lampiran');
                    $nama_lampiran = time().'.'.$lampiran->getClientOriginalExtension();
                    $request->lampiran->storeAs('public/lampiran_konten', $nama_lampiran);
                }else{
                    $nama_lampiran = $news->lampiran;
                }

                if(!empty($request->judul)){
                    $judul = $request->judul;
                }else{
                    $judul = $news->judul;
                }
                if(!empty($request->detail)){
                    $detail = $request->detail;
                }else{
                    $detail = $news->detail;
                }
                if(!empty($request->link_video)){
                    $link_video = $request->link_video;
                }else{
                    $link_video = $news->link_video;
                }

                $update = News::where('id', $request->id)->update([
                    'judul' => $judul,
                    'tanggal_news' => $request->tanggal_news,
                    'detail' => $detail,
                    'tumbnail' => $nama_tumbnail,
                    'gambar' => $images,
                    'link_video' => $link_video,
                    'lampiran' => $nama_lampiran,
                    'status' => 'draft'
                ]);

                //cek storage konten
                $files = Storage::allFiles('public/konten');
                $fileNames = array_map(function($file){
                    return basename($file); // remove the folder name
                }, $files);
                foreach($fileNames as $key => $value){
                    $query = News::where('gambar','like','%'.$value.'%')->first();
                    if(!empty($query)){
                        $gambar_ada = $value;
                    }else{
                        $gambar_kosong = storage_path('app/public/konten/'.$value); 
                        if (File::exists($gambar_kosong)) {
                            File::delete($gambar_kosong);
                        }
                    }
                }

                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify Draft News and Event '.'"'.$news->judul.'"';
                $insert->save();

                return redirect()->route('news-and-event.index')->with('status','Modify Draft News and Event Successfully');
            }else{
                if($request->input('images', [])){
                    $images = implode(',', $request->input('images', []));
                }else{
                    $images = null;
                }
                $tumbnail = $request->file('tumbnail');
                $nama_tumbnail = time().'.'.$tumbnail->getClientOriginalExtension();
                $request->tumbnail->storeAs('public/tumbnail', $nama_tumbnail);
                if($request->file('lampiran')){
                    $lampiran = $request->file('lampiran');
                    $nama_lampiran = time().'.'.$lampiran->getClientOriginalExtension();
                    $request->lampiran->storeAs('public/lampiran_konten', $nama_lampiran);
                }else{
                    $nama_lampiran = null;
                }
                //insert
                $insert = new News;
                $insert->judul = $request->judul;
                $insert->tanggal_news = $request->tanggal_news;
                $insert->detail = $request->detail;
                $insert->tumbnail = $nama_tumbnail;
                $insert->gambar = $images;
                $insert->link_video = $request->link_video;
                $insert->lampiran = $nama_lampiran;
                $insert->status = 'draft';
                $insert->save();
    
                //cek storage konten
                $files = Storage::allFiles('public/konten');
                $fileNames = array_map(function($file){
                    return basename($file); // remove the folder name
                }, $files);
                foreach($fileNames as $key => $value){
                    $query = News::where('gambar','like','%'.$value.'%')->first();
                    if(!empty($query)){
                        $gambar_ada = $value;
                    }else{
                        $gambar_kosong = storage_path('app/public/konten/'.$value); 
                        if (File::exists($gambar_kosong)) {
                            File::delete($gambar_kosong);
                        }
                    }
                }

                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Draft News and Event '.'"'.$request->judul.'"';
                $insert->save();
    
                return redirect()->route('news-and-event.index')->with('status','Draft News and Event Successfully');
            }
        }
    }

    public function uploads(Request $request){
        $path = storage_path('app/public/konten');

        !file_exists($path) && mkdir($path, 0777, true);

        $file = $request->file('file');
        $name = uniqid() . '.' . trim($file->getClientOriginalExtension());
        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function destroy(Request $request){
        $query = News::where('id', decrypt($request->id))->first();
        if(!empty($query->tumbnail)){
            $tumbnail = storage_path('app/public/tumbnail/'.$query->tumbnail); 
            if (File::exists($tumbnail)) {
                File::delete($tumbnail);
            }
        }
        if(!empty($query->lampiran)){
            $lampiran = storage_path('app/public/lampiran_konten/'.$query->lampiran); 
            if (File::exists($lampiran)) {
                File::delete($lampiran);
            }
        }
        $post = News::where('id', decrypt($request->id))->delete();
        return redirect()->route('news-and-event.index')->with('error','Delete News and Event Successfully');
    }

    public function lampiran($id){
        $query = News::find(decrypt($id));
        $lampiran = public_path('storage/lampiran_konten/'.$query->lampiran);
        
        // return response()->file($lampiran);
        $file = File::get($lampiran);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'filename=' . '"'.$query->judul.'.pdf"');
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }
}
