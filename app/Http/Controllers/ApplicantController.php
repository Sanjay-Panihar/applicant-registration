<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|unique:applicants,email',
            'address' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'photo' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);
        $data = $request->all();
        // Handle file uploads for resume
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension; // Use a unique filename to prevent overwriting
            $path = $file->storeAs('resumes', $filename); // Save the image to the 'photos' directory with the custom filename
            $data['resume'] = $path;
        }

        // Handle file uploads for photo
        if ($request->hasFile('photo')) {
            // Get the uploaded file
            $uploadedFile = $request->file('photo');

            // Generate a unique filename
            $fileName = time() . '_' . $uploadedFile->getClientOriginalName();

            // Store the file in the public/images directory
            $path = $uploadedFile->storeAs('images', $fileName, 'public');

            // Update the validated data to include the path to the uploaded image
            $data['photo'] = '/storage/' . $path;
        }

        // Handle cropped image
        if ($request->has('cropped_image')) {
            $croppedImageData = $request->input('cropped_image');
            $imageData = str_replace('data:image/png;base64,', '', $croppedImageData);
            $imageData = str_replace(' ', '+', $imageData);
            $decodedImage = base64_decode($imageData);
            $imageName = time() . '_' . Str::random(10) . '.png';
            $storagePath = 'public/images';
            $path = Storage::put($storagePath . '/' . $imageName, $decodedImage);
            $data['cropped_image'] = '/storage/images/' . $imageName; // Assuming images are stored in storage/app/public/images directory
        }

        $applicant = Applicant::create($data);

        return response()->json(['status' => true, 'message' => 'Applicant created successfully'], 201);
    }


    public function show(Applicant $applicant)
    {
        if ($applicant) {
            return response()->json([
                'status' => true,
                'message' => 'Applicant found',
                'applicant' => $applicant
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Applicant not found'
            ], 404); // Assuming 404 status code for "not found" response
    }
}

    public function edit($id)
    {
        $applicant = Applicant::find($id);

        return view('applicant.edit', compact('applicant'));
    }
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|unique:applicants,email,'.$id,
            'address' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'photo' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Handle file uploads for resume
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension; // Use a unique filename to prevent overwriting
            $path = $file->storeAs('resumes', $filename); // Save the image to the 'photos' directory with the custom filename
            $validatedData['resume'] = $path;
        }

        if ($request->hasFile('photo')) {
            $uploadedFile = $request->file('photo');

            $fileName = time() . '_' . $uploadedFile->getClientOriginalName();

            $path = $uploadedFile->storeAs('images', $fileName, 'public');

            $validatedData['photo'] = '/storage/' . $path;
        }

        if ($request->has('cropped_image')) {
            $croppedImageData = $request->input('cropped_image');
            $imageData = str_replace('data:image/png;base64,', '', $croppedImageData);
            $imageData = str_replace(' ', '+', $imageData);
            $decodedImage = base64_decode($imageData);

            $imageName = time() . '_' . Str::random(10) . '.png';

            $storagePath = 'public/images';

            $path = Storage::put($storagePath . '/' . $imageName, $decodedImage);

            $data['cropped_image'] = '/storage/images/' . $imageName; // Assuming images are stored in storage/app/public/images directory
        }

        $applicant = Applicant::where('id', $id)->update($validatedData);

        return response()->json(['status' => true, 'message' => 'Applicant updated successfully'], 201);
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
