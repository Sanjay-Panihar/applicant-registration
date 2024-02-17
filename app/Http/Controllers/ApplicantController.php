<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $applicants = Applicant::latest()->get();
            return DataTables::of($applicants)
                ->addColumn('action', function ($applicants) {
                    return '<button class="btn btn-primary btn-sm" onclick="editApplicant(' . $applicants->id . ')"><i class="fas fa-edit" title="Edit"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $applicants->id . ')"><i class="fas fa-trash-alt" title="Delete"></i></button>
                    <button class="btn btn-info btn-sm" onclick="viewApplicant(' . $applicants->id . ')"><i class="fas fa-eye" title="Delete"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('applicant.index');

    }
    public function create()
    {
        return view('applicant.create');
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:applicants,email', // Unique validation rule added
            'address' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048', // Example validation for resume file
            'photo' => 'required|image|mimes:jpg,png|max:2048', // Example validation for photo file
        ]);

        // Handle file uploads
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes');
            $validatedData['resume_path'] = $resumePath;
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos');
            $validatedData['photo_path'] = $photoPath;
        }

        // Create a new Applicant instance with the validated data
        $applicant = Applicant::create($validatedData);

        // Optionally, you can return a response or redirect the user
        return response()->json(['status' => true, 'message' => 'Applicant created successfully', 'applicant' => $applicant], 201);
    }

    public function show(Applicant $applicant)
    {
        return view('applicants.show', compact('applicant'));
    }

    public function edit(Applicant $applicant)
    {
        return view('applicants.edit', compact('applicant'));
    }

    public function destroy(Applicant $applicant)
    {
        try {
            // Delete the applicant
            $applicant->delete();

            // Return success response
            return response()->json(['status' => true, 'message' => 'Applicant deleted successfully']);

        } catch (\Exception $e) {
            // Return error response if deletion fails
            return response()->json(['status' => false, 'message' => 'Failed to delete applicant'], 500);
        }
    }

}
