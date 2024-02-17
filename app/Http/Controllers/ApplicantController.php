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
                    <button class="btn btn-danger btn-sm" onclick="deleteApplicant(' . $applicants->id . ')"><i class="fas fa-trash-alt" title="Delete"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('applicants.index');

}
public function create()
{
    return view('applicant.create');
}
public function store(Request $request)
    { echo '<pre>'; print_r($request->all()); die;
        $validator = Validator::make($request->all(), Applicant::$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $applicant = Applicant::create($request->all());

        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes');
            $applicant->resume = $resumePath;
            $applicant->save();
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos');
            $applicant->photo = $photoPath;
            $applicant->save();
        }

        return redirect()->route('applicants.index')->with('success', 'Applicant registered successfully.');
    }

    public function show(Applicant $applicant)
    {
        return view('applicants.show', compact('applicant'));
    }

    public function edit(Applicant $applicant)
    {
        return view('applicants.edit', compact('applicant'));
    }

}
