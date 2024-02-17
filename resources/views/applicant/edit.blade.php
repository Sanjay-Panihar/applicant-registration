@extends('layouts.main')

@section('title', 'Update')

@section('content')
<x-app-layout>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <a href="{{ url('/dashboard') }}" class="btn btn-secondary rounded-pill mb-2">Back</a>
            </div>
        </div>

        <h1 class="my-4 text-center">Update Applicant</h1>

        <form id="registration-form" class="bg-light p-4 rounded" method="POST" action="{{ route('applicants.update', $applicant->id) }}">
            @csrf
            @method('PUT') <!-- Add this line to specify the HTTP method as PUT for updating -->

            <div class="row justify-content-center"> <!-- Adjusted to center the content -->
                <div class="col-md-10"> <!-- Reduced by one column -->
                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $applicant->first_name }}">
                        <div class="text-danger error-message" id="first_name-error"></div>
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $applicant->last_name }}">
                        <div class="text-danger error-message" id="last_name-error"></div>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $applicant->phone }}">
                        <div class="text-danger error-message" id="phone-error"></div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $applicant->email }}">
                        <div class="text-danger error-message" id="email-error"></div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address">{{ $applicant->address }}</textarea>
                        <div class="text-danger error-message" id="address-error"></div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ $applicant->dob }}">
                        <div class="text-danger error-message" id="dob-error"></div>
                    </div>

                    <!-- Gender -->
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <div class="text-danger error-message" id="gender-error"></div>
                        <div>
                            <input type="radio" id="male" name="gender" value="male" class="form-check-input" {{ $applicant->gender == 'male' ? 'checked' : '' }}>
                            <label for="male" class="form-check-label">Male</label>
                        </div>
                        <div>
                            <input type="radio" id="female" name="gender" value="female" class="form-check-input" {{ $applicant->gender == 'female' ? 'checked' : '' }}>
                            <label for="female" class="form-check-label">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center"> <!-- Adjusted to center the content -->
                <div class="col-md-10"> <!-- Reduced by one column -->
                    <!-- Resume -->
                    <div class="mb-3">
                        <label for="resume" class="form-label">Resume</label>
                        <input type="file" class="form-control" id="resume" name="resume">
                        <div class="text-danger error-message" id="resume-error"></div>
                    </div>

                    <!-- Photo -->
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo">
                        <div id="cropper-container"></div>
                        <div class="text-danger error-message" id="photo-error"></div>
                        <!-- Div to display uploaded photo -->
                        <div id="uploaded-photo" style="margin-top: 10px;">
                            <!-- Display the last uploaded photo -->
                            @if($applicant->photo)
                                <img src="{{ asset($applicant->photo) }}" style="max-width: 100px; max-height: 100px;">
                            @endif
                        </div>
                    </div>

                    <!-- Update Button -->
                    <button type="submit" class="btn btn-primary rounded-pill mb-2">Update</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
@endsection
