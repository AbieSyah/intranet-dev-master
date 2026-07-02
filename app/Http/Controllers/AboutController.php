<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AboutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $abouts = About::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('version', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%")
                        ->orWhereHas('user.employee', function ($q) use ($search) {
                            $q->where('fullname', 'ilike', "%{$search}%");
                        });
                });
            })
            ->with(['user.employee'])
            ->orderBy('release_date', 'desc')
            ->paginate(10);

        return view('pages.about.index', compact('abouts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'release_date' => 'required|date',
            'description' => 'required|string',
            'id' => 'sometimes|nullable|exists:abouts,id' // Add this for update scenario
        ]);

        // Determine if this is an update or create operation
        if (!empty($validated['id'])) {
            // Update existing record
            $about = About::findOrFail($validated['id']);
            $message = 'About entry updated successfully!';
        } else {
            // Create new record
            $about = new About();
            $message = 'About entry created successfully!';
        }

        // Set common fields
        $about->version = $validated['version'];
        $about->release_date = $validated['release_date'];
        $about->description = $validated['description'];
        $about->user_id = Auth::id();

        $about->save();


        $user = auth()->user();
        $log = new \App\Models\Log;
        $log->user_id = $user->id;
        $log->ip_address = $request->ip();
        if ($about->wasRecentlyCreated) {
            $log->action = 'insert';
            $log->description = 'Create New About "' . $about->version . '"';
        } else {
            $log->action = 'update';
            $log->description = 'Modify About "' . $about->version . '"';
        }
        $log->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $about
        ]);
    }

    public function destroy($id)
    {
        try {
            $about = About::findOrFail($id);
            $about->delete();

            return response()->json([
                'success' => true,
                'message' => 'About entry deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete about entry: ' . $e->getMessage()
            ], 500);
        }
    }
}
