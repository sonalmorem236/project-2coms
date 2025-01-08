<?php

namespace App\Http\Controllers;

use App\Imports\LeadImport;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allLeads = Lead::orderBy('id','DESC');
        if(auth()->user()->type == User::USER){
            $allLeads->where('user_id', auth()->id());
        }
        $allLeads = $allLeads->get();
        return view('leads.index', compact(['allLeads']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // dd(Lead::get());
        return view('leads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the file
        $validator = Validator::make($request->all(), [
            'lead_excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $import = new LeadImport;

        // Run the import
        Excel::import($import, $request->file('lead_excel_file'));
    
        // Check if there were any validation errors
        $errors = $import->getErrors();
    
        if (!empty($errors) && count($errors['errors']) > 0) {
            return response()->json(['errors' => $errors], 422);
        } else {
            return response()->json(['message' => 'Import successful!']);
        }
        

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $lead = Lead::find($request->id);

        if ($lead) {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:leads,email,' . $lead->id,
                'phone' => 'required|regex:/^\+?[0-9]{10,15}$/',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->first()], 422);
            }

            // Update the lead
            $lead->name = $request->name;
            $lead->email = $request->email;
            $lead->phone = $request->phone;
            $lead->save();

            return response()->json(['success' => true,'message' => 'Updated successfully']);
        }

       
        return response()->json(['success' => false, 'message' => 'Something went wrong'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request){
        $lead = Lead::find($request->id);
        if ($lead) {
            $lead->delete();
        }
        return response()->json(['success' => true]);
    }

    public function statusChange(Request $request){
        $lead = Lead::find($request->id);
        if ($lead) {

            LeadStatusHistory::create([
                'user_id' => auth()->user()->id,
                'lead_id' => $lead->id,
                'from' => $lead->status,
                'to' => $request->status
            ]);

            $lead->status = $request->status;
            $lead->update();
        }
        return response()->json(['success' => true]);
    }
}
