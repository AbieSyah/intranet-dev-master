<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseMedia;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class KnowledgeBaseController extends Controller
{
    public function getData(Request $request) {
        $knowledgeBases = KnowledgeBase::with('author')->latest()->get();

        return DataTables::of($knowledgeBases)
            ->addColumn('encrypted_id', function($kb) {
                return encrypt($kb->id);
            })
            ->addColumn('formated_created_at', function($kb) {
                return $kb->created_at->format('d M Y H:i');
            })
            ->addColumn('author_name', function($kb) {
                return $kb->author ? $kb->author->fullname : '-';
            })
            ->addColumn('edit_url', function($kb) {
                $editUrl = route('knowledge-base.edit', ['id' => encrypt($kb->id)]);
                return $editUrl;
            })
            ->addColumn('delete_url', function($kb) {
                $deleteUrl = route('knowledge-base.destroy', ['id' => encrypt($kb->id)]);
                return $deleteUrl;
            })
            ->addColumn('view_url', function($kb) {
                $viewUrl = route('knowledge-base.show', ['id' => encrypt($kb->id)]);
                return $viewUrl;
            })
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.administrator.knowledge-base.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::get()->load('department', 'position');
        $employees->each(function($employee) {
            $employee->encrypted_id = encrypt($employee->id);
        });
        return view('pages.administrator.knowledge-base.form', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function upsert(Request $request, $id = null)
    {
        if($id) {
            $decryptedId = decrypt($id);
            $knowledgeBase = KnowledgeBase::findOrFail($decryptedId);
        } else {
            $knowledgeBase = new KnowledgeBase();
        }

        $processedRequest = $request->except('attachments');
        if ($request->action == KnowledgeBase::STATUS_DRAFT) {
            $processedRequest['status'] = KnowledgeBase::STATUS_DRAFT;
        } else {
            $processedRequest['status'] = KnowledgeBase::STATUS_PUBLISHED;
            $processedRequest['published_at'] = now();
        }

        $processedRequest['selected_employees'] = collect($request->selected_employees)->map(function($employeeId) {
            return decrypt($employeeId);
        })->toArray();

        $validated = Validator::make($processedRequest, [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:'.implode(',', [KnowledgeBase::STATUS_DRAFT, KnowledgeBase::STATUS_PUBLISHED, KnowledgeBase::STATUS_ARCHIVED]),
            'level' => 'nullable|in:'.implode(',', [KnowledgeBase::LEVEL_PRIVATE, KnowledgeBase::LEVEL_SOME_EMPLOYEES, KnowledgeBase::LEVEL_ALL_EMPLOYEES]),
            'selected_employees' => 'required_if:level,'.KnowledgeBase::LEVEL_SOME_EMPLOYEES,
            'selected_employees.*' => 'exists:employees,id',
        ])->validate();

        $user = auth()->user();

        $uploadedAttachments = [];

        try {
            if ($request->attachments) {
                foreach ($request->attachments as $attachment) {
                    $path = $attachment->store('knowledge_base_attachments', 'public');
                    $uploadedAttachments[] = [
                        'name' => $attachment->getClientOriginalName(),
                        'path' => $path,
                        'type' => Str::before($attachment->getClientMimeType(), '/') == 'image' ? 'image' : 'file',
                    ];
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload attachments',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            DB::beginTransaction();

            $knowledgeBase->title = $validated['title'];
            $knowledgeBase->content = $validated['content'];
            $knowledgeBase->status = $validated['status'];
            $knowledgeBase->level = $validated['level'];
            if (isset($processedRequest['published_at'])) {
                $knowledgeBase->published_at = $processedRequest['published_at'];
            }
            $knowledgeBase->author_id = $user->employee->id;
            $knowledgeBase->save();

            if($uploadedAttachments) {
                $knowledgeBase->media()->createMany($uploadedAttachments);
            }

            if ($validated['level'] == KnowledgeBase::LEVEL_SOME_EMPLOYEES) {
                $knowledgeBase->employees()->sync($validated['selected_employees']);
            }

            // user_id','ip_address','action','description'
            if ($id) {
                Log::create([
                    'ip_address' => $request->ip(),
                    'user_id' => $user->id,
                    'action' => 'update',
                    'description' => "User {$user->employee->name} - {$user->employee->position->nama}({$user->employee->department->name}) edit a Knowledge Base title: {$knowledgeBase->title} at ".now()->format('d M Y, H:i'),
                ]);
            } else {
                Log::create([
                    'ip_address' => $request->ip(),
                    'user_id' => $user->id,
                    'action' => 'insert',
                    'description' => 'Created knowledge base with title: ' . $knowledgeBase->title
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Knowledge base created successfully',
                'data' => $knowledgeBase
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create knowledge base',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id) {
        $decryptedId = decrypt($id);
        $knowledgeBase = KnowledgeBase::findOrFail($decryptedId);

        $newStatus = $knowledgeBase->status == KnowledgeBase::STATUS_DRAFT ? KnowledgeBase::STATUS_PUBLISHED : KnowledgeBase::STATUS_DRAFT;
        $knowledgeBase->status = $newStatus;
        if ($newStatus == KnowledgeBase::STATUS_PUBLISHED) {
            $knowledgeBase->published_at = now();
        } else {
            $knowledgeBase->published_at = null;
        }
        $knowledgeBase->save();


        Log::create([
            'ip_address' => $request->ip(),
            'user_id' => Auth::user()->id,
            'action' => 'update',
            'description' => "User ".Auth::user()->employee->name." - ".Auth::user()->employee->position->nama."(".Auth::user()->employee->department->name.") change status of Knowledge Base title: {$knowledgeBase->title} to {$knowledgeBase->status} at ".now()->format('d M Y, H:i'),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => encrypt($knowledgeBase->id),
                'status' => $knowledgeBase->status,
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $decryptedId = decrypt($id);
        $knowledgeBase = KnowledgeBase::with('author', 'media', 'employees')->findOrFail($decryptedId);
        $is_preview = false;
        if (Auth::user()->hasRole('super_admin') && $knowledgeBase->status == 'draft' && $request->has('preview')) {
            $is_preview = true;
        }
        
        $user = Auth::user()->load('employee.department', 'employee.position');

        return view('pages.administrator.knowledge-base.show', compact('knowledgeBase', 'is_preview', 'user', ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $decryptedId = decrypt($id);
        $knowledgeBase = KnowledgeBase::with('media', 'employees')->findOrFail($decryptedId);
        $employees = Employee::get()->load('department', 'position');
        $employees->each(function($employee) {
            $employee->encrypted_id = encrypt($employee->id);
        });
        return view('pages.administrator.knowledge-base.form', compact('knowledgeBase', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $decryptedId = decrypt($id);
        $knowledgeBase = KnowledgeBase::findOrFail($decryptedId);
        $knowledgeBase->delete();

        return response()->json([
            'message' => 'Knowledge base deleted successfully'
        ], 200);
    }

    public function deleteMedia($id)
    {
        $decryptedId = decrypt($id);
        $media = KnowledgeBaseMedia::findOrFail($decryptedId);

        try {
            // Delete the file from storage
            if (file_exists(public_path('storage/' . $media->path))) {
                unlink(public_path('storage/' . $media->path));
            }

            // Delete the database record
            $media->delete();

            return response()->json([
                'message' => 'Attachment deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete attachment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
