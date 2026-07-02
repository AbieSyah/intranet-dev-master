<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\LogService;
use Illuminate\Http\Request;
use App\Models\Security\Guest;
use DB;
use Exception;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class GuestController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }
    public function index()
    {
        return view('pages.security.guest.index');
    }

    public function data(Request $request)
    {
        $guests = Guest::with('employee:id,fullname')
            ->latest('security_guest.created_at');

        if ($request->has('from') && $request->from && $request->has('to') && $request->to) {
            $guests->whereBetween('security_guest.created_at', [$request->from, $request->to]);
        }

        return DataTables::of($guests)
            ->addColumn('encrypted_id', fn($data) => encrypt($data->id))
            ->addIndexColumn()
            ->make(true);
    }

    public function detail($id)
    {
        $guestForm = Guest::findOrFail(decrypt($id));
        return view('pages.security.guest.detail', compact('guestForm'));
    }

    public function status($id)
    {
        $guest = Guest::findOrFail($id);

        return response()->json($guest);
    }

    public function print(string $id = null)
    {
        $guest = Guest::findOrFail(decrypt($id));

        $data = [
            'form' => $guest,
            'logohisamitsu' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/logo.png'))),
            'logok3' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/k3.png'))),
            'tamu1' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu1.png'))),
            'tamu2' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu2.jpg'))),
            'tamu3' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu3.jpg'))),
            'tamu4' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu4.png'))),
            'tamu5' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu5.png'))),
            'tamu6' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu6.png'))),
            'tamu7' => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('/assets/images/security/tamu7.png'))),
            'qrcode1' => $qrcode = base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')
                ->generate("Signed By {$guest->nama} at {$guest->created_at}")),
            'qrcode2' => $qrcode = base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')
                ->generate(
                    "Signed By " . ($guest->waktu_bertemu ? $guest->nama_pic : 'security') .
                    " at " . ($guest->waktu_bertemu ?? $guest->waktu_keluar)
                )),
        ];


        $pdf = PDF::loadView('pages.security.guest.print', $data)
            ->setPaper('a6', 'landscape');

        return $pdf->stream('Form_Tamu_' . $guest->nama . '.pdf');
    }

    public function security_form(string $id = null)
    {
        $guestForm = null;

        if ($id) {
            $guestForm = Guest::findOrFail(decrypt($id));
        }

        $employees = Employee::with('Department')->whereNot('status', 'TERMINATED')->get();
        return view('pages.security.guest.security-form', compact('employees', 'guestForm'));
    }

    public function security_form_save(Request $request, string $id = null)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'tujuan_kunjungan' => 'required|string|max:255',
            'alamat_pribadi' => 'required|string|max:255',
            'id_employee' => 'required|exists:employees,id',
            'suhu' => 'nullable|numeric|min:34|max:42',
            'lama_kunjungan' => 'required|string|max:255',
            'resiko_kesehatan' => 'nullable|in:rendah,sedang,tinggi',
            'nomor_kartu_identitas' => 'required|string|max:255',
            'nomor_polisi' => 'nullable|string|max:255',
            'muatan' => 'nullable|string|max:255',
            'photo64' => 'nullable|string',
            'nomor_visitor' => 'nullable|string|max:255',
            'jenis_kendaraan' => 'nullable|string|max:255',
            'muatan_kendaraan' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'waktu_keluar' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $guest = $id ? Guest::findOrFail(decrypt($id)) : new Guest;

            $guest->fill($validatedData);

            if ($request->has('photo64') && $request->input('photo64')) {
                $photoData = $request->input('photo64');
                $image_parts = explode(";base64,", $photoData);
                $image_base64 = base64_decode($image_parts[1]);

                $fileName = $guest->id . '.jpg';
                $filePath = 'tamu/' . $fileName;
                Storage::disk('public')->put($filePath, $image_base64);
            }

            $guest->save();

            $this->logService->createLog(
                auth()->user()->id,
                $request->ip(),
                'verificate',
                'Verificate Guest Form "' . $guest->nama . '"'
            );

            DB::commit();
            return response()->json([
                'message' => 'Success.',
                'redirect' => route('guest.index'),
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function set_waktu_keluar(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $guest = Guest::findOrFail($id);

            if (auth()->user()->hasrole('Security')) {
                $guest->waktu_keluar = now();
                $this->logService->createLog(
                    auth()->user()->id,
                    $request->ip(),
                    'Update',
                    'Security Set Out Time Guest Form "' . $guest->nama . '"'
                );
            } else if ((auth()->user()->employee->id == $guest->id_employee) && !$guest->waktu_bertemu) {
                $guest->waktu_bertemu = now();
                $this->logService->createLog(
                    auth()->user()->id,
                    $request->ip(),
                    'Update',
                    'Set Meeting time Guest Form "' . $guest->nama . '"'
                );
            } else {
                $guest->waktu_keluar = now();
                $this->logService->createLog(
                    auth()->user()->id,
                    $request->ip(),
                    'Update',
                    'Set Out Time Guest Form "' . $guest->nama . '"'
                );
            }

            $guest->save();

            DB::commit();
            return response()->json([
                'message' => 'Success.',
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            dd($e);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function guest_form()
    {
        return view('pages.security.guest.guest-form');
    }

    public function guest_form_save(Request $request)
    {

        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'nama'    => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{N}\s\.\-,&@()+\/\'"]+$/u'],
                'company' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{N}\s\.\-,&@()+\/\'"]+$/u'],
                'purpose' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{N}\s\.\-,&@()+\/\'"]+$/u'],
                'emp'     => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{N}\s\.\-,&@()+\/\'"]+$/u'],
                'est'     => ['required', 'string', 'max:100'],
            ]);

            $form = Guest::create([
                'nama' => strtoupper($validatedData['nama']),
                'perusahaan' => strtoupper($validatedData['company']),
                'tujuan_kunjungan' => strtoupper($validatedData['purpose']),
                'nama_pic' => $validatedData['emp'],
                'lama_kunjungan' => $validatedData['est'],
                'tanggal' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Form submitted successfully.']);
        } catch (Exception $e) {
            DB::rollback();
            // dd($e);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $guest = Guest::findOrFail($id);

            $this->logService->createLog(
                auth()->user()->id,
                $request->ip(),
                'delete',
                'Delete Guest Form "' . $guest->nama . '"'
            );

            $guest->delete();

            DB::commit();
            return response()->json([
                'message' => 'Success.',
            ], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
