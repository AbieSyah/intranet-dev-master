<?php

namespace App\Http\Controllers;

use App\Models\RiskRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiskRegisterController extends Controller
{
    public function getData(Request $request)
    {
        $risks = RiskRegister::latest()->get();

        return datatables()->of($risks)
            ->addColumn('view_url', function($row) {
                return route('risk-register.edit', encrypt($row->id));
            })
            ->addColumn('edit_url', function($row) {
                return route('risk-register.edit', encrypt($row->id));
            })
            ->addColumn('delete_url', function($row) {
                return route('risk-register.destroy', encrypt($row->id));
            })
            ->editColumn('id', function($row) {
                return encrypt($row->id);
            })
            ->make(true);
    }

    public function index()
    {
        return view('pages.administrator.service-management.risk-register.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'risk_id' => 'required|unique:risk_registers,risk_id,' . ($request->id ? decrypt($request->id) : 'NULL'),
            'name' => 'required|string|max:255',
            'impact' => 'required|integer|min:1|max:5',
            'probability' => 'required|integer|min:1|max:5',
            'description' => 'required|string',
            'mitigation' => 'required|string',
            'contingency_plan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['risk_id', 'name', 'description', 'impact', 'probability', 'mitigation', 'contingency_plan']);
        $data['score'] = $request->impact * $request->probability;

        if ($request->id) {
            $id = decrypt($request->id);
            RiskRegister::find($id)->update($data);
            $msg = "Risk updated successfully!";
        } else {
            RiskRegister::create($data);
            $msg = "Risk created successfully!";
        }

        return response()->json(['message' => $msg]);
    }

    public function edit($encryptedId)
    {
        $id = decrypt($encryptedId);
        $risk = RiskRegister::findOrFail($id);
        $risk->encrypted_id = $encryptedId;
        return response()->json($risk);
    }

    public function destroy($encryptedId)
    {
        $id = decrypt($encryptedId);
        RiskRegister::destroy($id);
        return response()->json(['message' => 'Risk deleted successfully.']);
    }
}
