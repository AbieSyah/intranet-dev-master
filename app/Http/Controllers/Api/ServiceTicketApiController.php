<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ServiceTicket;
use App\Models\ServiceStatusHistory;
use App\Models\ServiceTicketMessage;
use App\Models\ServiceTicketMedia;
use App\Models\ServiceTicketCC;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceTicketAsset;
use App\Models\ServiceCatalog;

class ServiceTicketApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;

        $tickets = ServiceTicket::with([
            'priority',
            'submitter.department',
            'submitter.position',
            'ccs.employee',
            // 'pic',
            'deptHead',
            'supervisor',
            'riskRegister',
            'messages.sender',
            'messages.media',
        ])
        ->where(function ($query) use ($employeeId) {
            $query->where('submitter_id', $employeeId)
                ->orWhere('dept_head_id', $employeeId)
                ->orWhere('supervisor_id', $employeeId)
                ->orWhereHas('ccs', function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
                });
        })
        ->latest()
        ->get();

        // $catalogs = ServiceCatalog::get()->keyBy('id');


        $tickets->each(function ($ticket) use ($employeeId) {
            // $ticket->service_catalog = $catalogs[$ticket->catalog] ?? null;

            if ($ticket->submitter_id == $employeeId) {
                $ticket->user_role = 'submitter';
            } elseif ($ticket->supervisor_id == $employeeId) {
                $ticket->user_role = 'supervisor';
            } elseif ($ticket->dept_head_id == $employeeId) {
                $ticket->user_role = 'dept_head';
            } elseif ($ticket->ccs->contains('employee_id', $employeeId)) {
                $ticket->user_role = 'cc';
            } else {
                $ticket->user_role = 'other';
            }

            $ticket->messages->each(function ($message) {
                $message->media->each(function ($media) {
                    $media->url = Storage::url($media->path);
                });
            });
            $ticket->assets = DB::table('service_ticket_assets as sta')
                ->leftJoin('employees as e', 'sta.employee_id', '=', 'e.id')
                ->leftJoin('it_assets as a', 'sta.it_asset_id', '=', 'a.id')
                ->where('sta.service_ticket_id', $ticket->id)
                ->select(
                    'sta.id',
                    'e.fullname as employee_name',
                    'a.brand as asset_name',
                    // 'sta.service_ticket_id',
                    // 'sta.employee_id',
                    'sta.it_asset_id',
                    // pastikan ini juga benar
                )
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ]);
    }

    public function getapprovalTickets(Request $request)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;

        $tickets = ServiceTicket::with([
            'priority',
            'submitter.department',
            'submitter.position',
            'ccs.employee',
            'deptHead',
            'supervisor',
            'riskRegister',
            'messages.sender',
            'messages.media',
        ])
        ->where(function ($query) use ($employeeId) {
            $query->where(function ($q) use ($employeeId) {
                // Supervisor: langsung bisa lihat
                $q->where('supervisor_id', $employeeId);
            })
            ->orWhere(function ($q) use ($employeeId) {
                // Dept Head: hanya jika supervisor sudah approve
                $q->where('dept_head_id', $employeeId)
                ->where('supervisor_approval', 'approved'); // <- sesuaikan nama field
            });
        })
        ->latest()
        ->get();

        // $catalogs = ServiceCatalog::get()->keyBy('id');

        $tickets->each(function ($ticket) {
            $assets = ServiceTicketAsset::where('service_ticket_id', $ticket->id)->get();

            $ticket->assets = $assets;
        });

        $tickets->each(function ($ticket) {
            $ticket->service_catalog = $catalogs[$ticket->catalog] ?? null;

            $ticket->messages->each(function ($message) {
                $message->media->each(function ($media) {
                    $media->url = Storage::url($media->path);
                });
            });
            $ticket->assets = DB::table('service_ticket_assets as sta')
                ->leftJoin('employees as e', 'sta.employee_id', '=', 'e.id')
                ->leftJoin('it_assets as a', 'sta.it_asset_id', '=', 'a.id')
                ->where('sta.service_ticket_id', $ticket->id)
                ->select(
                    'sta.id',
                    'e.fullname as employee_name',
                    'a.brand as asset_name',
                    // 'sta.service_ticket_id',
                    // 'sta.employee_id',
                    'sta.it_asset_id',
                )
                ->get();
        });
        $tickets->each(function ($ticket) use ($employeeId) {
            if ($ticket->supervisor_id == $employeeId) {
                $ticket->approval_role = 'supervisor';
            } elseif ($ticket->dept_head_id == $employeeId) {
                $ticket->approval_role = 'dept_head';
            }
        });

        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ]);
    }

    public function getCatalogs()
    {
        $catalogs = ServiceCatalog::all();

        return response()->json([
            'status' => 'success',
            'data' => $catalogs
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'subject'       => 'required|min:5',
                'description'   => 'required',
                // 'asset_id_self' => 'required_if:report_type,self',
                // 'pics'          => 'required_if:report_type,other',
                // 'reported_dept' => 'required_if:pics_method,manual|nullable',
                // 'reported_area' => 'required_if:pics_method,manual|nullable',

                // Validasi File Multi-upload
                'attachments'   => 'nullable|array|max:5', // Batas 5 file per tiket
                'attachments.*.extension' => 'in:jpg,jpeg,png,pdf,zip', // Max 5MB per file
            ], [
                'asset_id_self.required_if' => 'Please select your IT asset.',
                'pics.required_if'           => 'Please select which employee has the issue.',
                'reported_dept.required_if' => 'Department is required for manual reporting.',
                'attachments.*.mimes'       => 'Only Images, PDF, and ZIP files are allowed.',
                'attachments.*.max'         => 'Each file must not exceed 5MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 422);
            }

            $employee = auth()->user()->employee;

            $date = date('ymd');
            $latestTicket = ServiceTicket::where('no_ticket', 'like', 'TK' . $date . '-%')->latest()->first();
            $sequence = $latestTicket ? intval(substr($latestTicket->no_ticket, -3)) + 1 : 1;
            $noTicket = 'TK' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $employee = auth()->user()->employee;

            $ticket = ServiceTicket::create([
                'no_ticket'      => $noTicket,
                'subject'        => $request->subject,
                'description'    => $request->description,
                'submitter_id'   => $employee->id, // Sesuaikan dengan login user
                'category'       => $request->category,
                'catalog'       => $request->catalog,
                'current_status' => ServiceStatusHistory::STATUS_OPEN, // Status awal
            ]);

            if ($request->has('ccs')) {
                foreach ($request->ccs as $employeeId) {
                    $ticket->ccs()->create([
                        'employee_id' => $employeeId
                    ]);
                }
            }

            $history = new ServiceStatusHistory();
            $history->service_ticket_id = $ticket->id;
            $history->from_status = ServiceStatusHistory::STATUS_OPEN;
            $history->to_status = ServiceStatusHistory::STATUS_OPEN;
            $history->employee_id = $employee->id;
            $history->note = 'Initial process';
            $history->started_at = now();
            $history->save();

            $message = $ticket->messages()->create([
                'sender_id' => $employee->id,
                'role' => ServiceTicketMessage::ROLE_USER,
                'message' => $request->description,
                'is_internal' => false,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    $fileName = time() . '_' . uniqid() . '.' . $extension;

                    $path = $file->storeAs('tickets', $fileName, 'public');

                    // 🔥 SIMPAN KE MESSAGE (BUKAN TICKET)
                    $message->media()->create([
                        'path' => $path,
                        'name' => $originalName,
                        'extension' => $extension,
                    ]);
                }
            }

            \App\Models\Log::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => "User " . auth()->user()->employee->fullname . " created a service ticket with Ticket Number: {$ticket->no_ticket} and subject '{$ticket->subject}'"
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $ticket
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveTicket(Request $request, $id)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;

        $validator = Validator::make($request->all(), [
            'approval' => 'required|in:approved,rejected',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ticket = ServiceTicket::findOrFail($id);

            $oldStatus = $ticket->current_status;
            $role = null;

            if ($ticket->supervisor_id == $employeeId) {

                if (in_array($ticket->supervisor_approval, ['approved', 'rejected'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Supervisor already approved/rejected this ticket'
                    ], 400);
                }

                $ticket->supervisor_approval = $request->approval;
                // $ticket->supervisor_note = $request->note;
                $ticket->supervisor_approval_at = now();
                $role = ServiceTicket::ROLE_SUPERVISOR;
            } elseif ($ticket->dept_head_id == $employeeId) {

                if (in_array($ticket->dept_head_approval, ['approved', 'rejected'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Department Head already approved/rejected this ticket'
                    ], 400);
                }

                $ticket->dept_head_approval = $request->approval;
                // $ticket->dept_head_note = $request->note;
                $ticket->dept_head_approval_at = now();
                $role = ServiceTicket::ROLE_DEPT_HEAD;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            if (
                $ticket->supervisor_approval === ServiceTicket::APPROVAL_STATUS_REJECTED ||
                $ticket->dept_head_approval === ServiceTicket::APPROVAL_STATUS_REJECTED
            ) {
                $ticket->current_status = 'rejected';
            } elseif (
                $ticket->supervisor_approval === ServiceTicket::APPROVAL_STATUS_APPROVED &&
                $ticket->dept_head_approval === ServiceTicket::APPROVAL_STATUS_APPROVED
            ) {
                $ticket->current_status = 'process';
            }

            $ticket->save();

            $ticket->histories()->create([
                'from_status' => $oldStatus,
                'to_status' => $ticket->current_status,
                'employee_id' => $employeeId,
                'note' => strtoupper($role) . " {$request->approval}",
                'started_at' => now(),
            ]);

            $roleName = ($role == ServiceTicket::ROLE_SUPERVISOR) ? 'Direct Supervisor' : 'Department Head';
            $messageText = "{$roleName} {$request->approval} this ticket.";

           $ticket->messages()->create([
                'sender_id'   => $employeeId,
                'role'        => \App\Models\ServiceTicketMessage::ROLE_SYSTEM,
                'message'     => $messageText,
                'is_internal' => false,
            ]);

            // 3. Catat ke log user
            \App\Models\Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'approve_ticket',
                'description' => "{$user->employee->fullname} {$request->approval} ticket {$ticket->no_ticket} as {$role}"
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Ticket {$request->approval} successfully",
                'data' => $ticket
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'message'       => 'required_without:attachments|string',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_internal'   => 'nullable|boolean'
        ], [
            'message.required_without' => 'Message tidak boleh kosong jika tidak ada attachment.',
            'attachments.*.mimes'      => 'Hanya file JPG, PNG, PDF yang diperbolehkan.',
            'attachments.*.max'        => 'Ukuran maksimal file 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $ticket = ServiceTicket::findOrFail($ticketId);

            $user = Auth::user();
            $employeeId = $user->employee->id;

            $message = $ticket->messages()->create([
                'sender_id'   => $employeeId,
                'role'        => ServiceTicketMessage::ROLE_USER,
                'message'     => $request->message,
                'is_internal' => $request->is_internal ? true : false,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if (!$file->isValid()) continue;

                    $path = $file->store('tickets', 'public');

                    $message->media()->create([
                        'path'      => $path,
                        'name'      => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                    ]);
                }
            }

            $message->load('media');

            foreach ($message->media as $media) {
                $media->url = Storage::url($media->path);
            }

            DB::commit();

            \App\Models\Log::create([
                'user_id'     => $user->id,
                'ip_address'  => $request->ip(),
                'action'      => 'send_message',
                'description' => "User {$user->employee->fullname} mengirim pesan pada ticket {$ticket->no_ticket}",
            ]);

            $recipients = User::whereIn('employee_id', array_filter([
                $ticket->submitter_id,
                optional($ticket->deptHead)->id,
                optional($ticket->supervisor)->id,
            ]))->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new TicketNotification(
                    $ticket,
                    "Pesan baru pada tiket {$ticket->no_ticket}",
                    ServiceTicket::ROLE_USER
                ));
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Message berhasil dikirim',
                'data'    => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }
}
