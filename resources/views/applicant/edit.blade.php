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
                                <!-- <img src="{{ asset($applicant->photo) }}" style="max-width: 100px; max-height: 100px;" class="img img-responsive"> -->
                                <img src="{{ asset($applicant->cropped_image) }}" style="max-width: 100px; max-height: 100px;" class="img img-responsive">
                            @endif
                        </div>
                    </div>



                    <!-- Update Button -->
                    <button type="submit" class="btn btn-primary rounded-pill mb-2" id="update-btn">Update</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        let croppie;
        let errors = @json($errors->getMessages(), JSON_PRETTY_PRINT);

        // Custom validation method for minimum age
        $.validator.addMethod('minAge', function(value, element) {
            // Parse the entered date of birth
            var dob = new Date(value);
            // Calculate today's date
            var today = new Date();
            // Calculate the age difference in milliseconds
            var ageDiff = today - dob;
            // Convert milliseconds to years
            var years = ageDiff / (1000 * 60 * 60 * 24 * 365);
            // Return true if age is 18 or older, false otherwise
            return years >= 18;
        }, 'You must be at least 18 years old');


        // Initialize jQuery Validation for the registration form
        $('#registration-form').validate({
            // Specify validation rules
            rules: {
                first_name: 'required',
                last_name: 'required',
                phone: 'required',
                email: {
                    required: true,
                    email: true
                },
                address: 'required',
                dob: {
                    required: true,
                    date: true,
                    minAge: 18 // Minimum age is 18
                },
                gender: 'required',
                resume: {
                    extension: 'pdf|docx' // Validate file extension
                },
                photo: {
                    extension: 'jpg|png|jpeg' // Validate file extension
                }
            },
            // Specify validation error messages
            messages: errors,
            // Specify the class to be added to the error message elements
            errorClass: 'invalid',
            // Specify where to place the error messages
            errorPlacement: function(error, element) {
                // Append error message after the parent div of the input element
                error.appendTo(element.closest('div'));
            },
            // Handle form submission
            submitHandler: function(form) {
                // Submit the form via AJAX
                $.ajax({
                    url: $(form).attr('action'), // Get the form action URL
                    type: $(form).attr('method'), // Get the form method (POST)
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle successful form submission
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                            });
                            // Optionally, redirect or perform any other action
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle form submission error
                        if (xhr.responseText) {
                            var errors = JSON.parse(xhr.responseText);
                            if (errors.errors) {
                                // Iterate over the errors object and handle each field error
                                $.each(errors.errors, function(field, messages) {
                                    // Construct the error message from the array of messages
                                    var errorMessage = messages.join('<br>');
                                    // Update the HTML content of the corresponding error element
                                    $('#' + field + '-error').html(errorMessage);
                                });
                            }
                        }
                    }
                });
            }
        });

        // Create Croppie instance
        croppie = new Croppie(document.getElementById('cropper-container'), {
            viewport: { width: 200, height: 200 },
            boundary: { width: 300, height: 300 },
            showZoomer: true,
            enableOrientation: true
        });

        // Handle photo upload
        $('#photo').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    croppie.bind({
                        url: reader.result
                    });
                    // Show the cropper container
                    $('#cropper-container').show();
                }
                reader.readAsDataURL(file);
            } else {
                // Hide the cropper container if no file is selected
                $('#cropper-container').hide();
            }
        });
    });
</script>
@endsection

@section('css')
    <style>
        /* Custom styles for the form */
        #registration-form {
            max-width: 80%;
            margin: 0 auto;
        }

        #registration-form div {
            margin-bottom: 20px;
        }

        /* Hide the cropped image by default */
        #cropped-image {
            display: none;
            max-width: 100%;
            height: auto;
        }

        /* Style for error messages */
        .invalid {
            color: red;
        }

        #cropper-container {
            display: none;
        }
    </style>
@endsection
