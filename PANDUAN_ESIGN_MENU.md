# 📋 PANDUAN LENGKAP: Menambah Menu E-Sign Management

## ✅ Langkah yang Sudah Selesai:
1. ✔️ Menu ditambahkan ke navbar (`resources/views/partials/navbar2.blade.php`)
2. ✔️ Folder views dibuat (`resources/views/pages/profile/ESign/`)
3. ✔️ File blade dibuat:
   - `resources/views/pages/profile/ESign/index.blade.php` (halaman daftar)
   - `resources/views/pages/profile/ESign/form.blade.php` (halaman form tambah/edit)

---

## 📝 Langkah Berikutnya: Controller & Routes

### **1. Buat Model ESign**

Buat file: `app/Models/ESign.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESign extends Model
{
    use HasFactory;

    protected $table = 'e_signs';

    protected $fillable = [
        'employee_id',
        'document_name',
        'document_type',
        'description',
        'document_path',
        'file_name',
        'file_size',
        'status',
        'upload_date',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'upload_date',
        'created_at',
        'updated_at',
    ];

    // Relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static $statuses = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Menunggu Persetujuan',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        $colors = [
            self::STATUS_DRAFT => 'warning',
            self::STATUS_PENDING => 'info',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        $label = self::$statuses[$this->status] ?? 'Unknown';

        return "<span class='badge bg-{$color}'>{$label}</span>";
    }
}
```

---

### **2. Buat Controller ESign**

Buat folder: `app/Http/Controllers/ESign/` (jika belum ada)

Buat file: `app/Http/Controllers/ESign/ProfileController.php`

```php
<?php

namespace App\Http\Controllers\ESign;

use App\Http\Controllers\Controller;
use App\Models\ESign;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($request->ajax()) {
            $query = ESign::where('employee_id', $user->employee_id);
            
            return DataTables::of($query)
                ->addColumn('DT_RowIndex', function ($row) {
                    static $count = 0;
                    return ++$count;
                })
                ->editColumn('document_type', function ($row) {
                    $types = [
                        'contract' => 'Kontrak',
                        'approval' => 'Persetujuan',
                        'agreement' => 'Perjanjian',
                        'other' => 'Lainnya',
                    ];
                    return $types[$row->document_type] ?? $row->document_type;
                })
                ->editColumn('upload_date', function ($row) {
                    return $row->upload_date?->format('d M Y H:i') ?? '-';
                })
                ->editColumn('status', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    
                    if ($row->status === ESign::STATUS_DRAFT) {
                        $actions .= '<a href="'.route('e-sign.profile-edit', encrypt($row->id)).'" class="btn btn-sm btn-warning">
                            <i class="ri-pencil-line"></i> Edit
                        </a> ';
                        
                        $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteESign('.encrypt($row->id).')">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>';
                    } else {
                        $actions .= '<a href="'.route('e-sign.profile-show', encrypt($row->id)).'" class="btn btn-sm btn-info">
                            <i class="ri-eye-line"></i> Lihat
                        </a>';
                    }
                    
                    return $actions;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('pages.profile.ESign.index', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        return view('pages.profile.ESign.form', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:contract,approval,agreement,other',
            'description' => 'nullable|string',
            'document_file' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        $user = Auth::user();
        $file = $request->file('document_file');
        
        // Generate unique filename
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('e-signs', $fileName, 'public');

        // Create record
        $eSign = ESign::create([
            'employee_id' => $user->employee_id,
            'document_name' => $validated['document_name'],
            'document_type' => $validated['document_type'],
            'description' => $validated['description'] ?? null,
            'document_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'status' => ESign::STATUS_DRAFT,
            'upload_date' => now(),
        ]);

        return redirect()->route('e-sign.profile-index')
            ->with('success', 'Dokumen E-Sign berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $eSign = ESign::findOrFail(decrypt($id));

        // Authorization check
        if ($eSign->employee_id !== $user->employee_id) {
            abort(403);
        }

        return view('pages.profile.ESign.form', [
            'user' => $user,
            'eSign' => $eSign,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $eSign = ESign::findOrFail(decrypt($id));

        // Authorization check
        if ($eSign->employee_id !== $user->employee_id) {
            abort(403);
        }

        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:contract,approval,agreement,other',
            'description' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Update basic data
        $eSign->update([
            'document_name' => $validated['document_name'],
            'document_type' => $validated['document_type'],
            'description' => $validated['description'] ?? null,
        ]);

        // Update file if provided
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('e-signs', $fileName, 'public');

            $eSign->update([
                'document_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()->route('e-sign.profile-index')
            ->with('success', 'Dokumen E-Sign berhasil diperbarui!');
    }

    /**
     * Delete the specified resource.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $eSign = ESign::findOrFail(decrypt($id));

        // Authorization check
        if ($eSign->employee_id !== $user->employee_id) {
            abort(403);
        }

        // Delete file
        if (file_exists(storage_path('app/public/' . $eSign->document_path))) {
            unlink(storage_path('app/public/' . $eSign->document_path));
        }

        $eSign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen E-Sign berhasil dihapus!',
        ]);
    }
}
```

---

### **3. Tambahkan Route**

Edit file: `routes/web.php`

Cari bagian `// Route protected (perlu login)` atau bagian akhir route, tambahkan:

```php
// E-SIGN Management Routes
Route::controller(\App\Http\Controllers\ESign\ProfileController::class)
    ->middleware('auth')
    ->prefix('employee/e-sign')
    ->name('e-sign.')
    ->group(function(){
        Route::get('/index', 'index')->name('profile-index');
        Route::get('/create', 'create')->name('profile-create');
        Route::post('/store', 'store')->name('profile-store');
        Route::get('/edit/{id}', 'edit')->name('profile-edit');
        Route::put('/update/{id}', 'update')->name('profile-update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });
```

---

### **4. Buat Migration Database**

Jalankan command:
```bash
php artisan make:migration create_e_signs_table
```

Edit file migration yang dibuat di `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('e_signs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('document_name');
            $table->enum('document_type', ['contract', 'approval', 'agreement', 'other']);
            $table->text('description')->nullable();
            $table->string('document_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->timestamp('upload_date')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_signs');
    }
};
```

Jalankan migration:
```bash
php artisan migrate
```

---

## 📋 Checklist Final:

- [ ] Model dibuat (`app/Models/ESign.php`)
- [ ] Controller dibuat (`app/Http/Controllers/ESign/ProfileController.php`)
- [ ] Routes ditambahkan (`routes/web.php`)
- [ ] Migration dibuat dan dijalankan
- [ ] Cek menu muncul di navbar
- [ ] Test create dokumen baru
- [ ] Test edit dokumen
- [ ] Test delete dokumen

---

## 🎯 Struktur Folder Final:

```
app/
├── Models/
│   └── ESign.php (NEW)
├── Http/Controllers/
│   └── ESign/
│       └── ProfileController.php (NEW)

resources/views/pages/profile/
└── ESign/
    ├── index.blade.php (NEW)
    └── form.blade.php (NEW)

resources/views/partials/
└── navbar2.blade.php (MODIFIED)

database/migrations/
└── YYYY_MM_DD_HHmmss_create_e_signs_table.php (NEW)
```

---

## 🚀 Langkah Testing:

1. Jalankan `php artisan migrate`
2. Kunjungi aplikasi dan login
3. Ke menu "E-Sign Management"
4. Coba tambah dokumen baru
5. Coba edit dokumen
6. Coba hapus dokumen

---

**Semua view blade dan navbar sudah ready! Tinggal implement backend saja! 🎉**
