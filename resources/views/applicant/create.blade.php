@extends('layouts.main')

@section('title', 'Auction-Create')

@section('content')
<style>
    /* Custom styles for the form */
    #registration-form {
        max-width: 500px;
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css" />

<div class="container mt-5">
    <h1 class="my-4 text-center">Applicant Registration</h1>

    <form id="registration-form" class="bg-light p-4 rounded" method="POST" action=" {{ route('applicants.store')}} ">
        @csrf
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name">
            <div class="text-danger error-message" id="first_name-error"></div>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
            <div class="text-danger error-message" id="last_name-error"></div>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone">
            <div class="text-danger error-message" id="phone-error"></div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
            <div class="text-danger error-message" id="email-error"></div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address"></textarea>
            <div class="text-danger error-message" id="address-error"></div>
        </div>
        <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob">
            <div class="text-danger error-message" id="dob-error"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Gender</label>
            <div class="text-danger error-message" id="gender-error"></div>
            <div>
                <input type="radio" id="male" name="gender" value="male" class="form-check-input">
                <label for="male" class="form-check-label">Male</label>
            </div>
            <div>
                <input type="radio" id="female" name="gender" value="female" class="form-check-input">
                <label for="female" class="form-check-label">Female</label>
            </div>
        </div>
        <div class="mb-3">
            <label for="resume" class="form-label">Resume</label>
            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.docx">
            <div class="text-danger error-message" id="resume-error"></div>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.png">
            <div id="cropper-container"></div>
            <div class="text-danger error-message" id="photo-error"></div>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        let croppie;
        let errors = @json($errors->getMessages(), JSON_PRETTY_PRINT);

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
                dob: 'required',
                gender: 'required',
                resume: {
                    required: true,
                    extension: 'pdf|docx' // Validate file extension
                },
                photo: {
                    required: true,
                    extension: 'jpg|png' // Validate file extension
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
                croppie.result('base64').then(function(base64) {
                    // Add the cropped image data to the form data
                    $(form).append($('<input>').attr({
                        type: 'hidden',
                        name: 'cropped_image',
                        value: base64
                    }));

                    // Submit the form via AJAX
                    $.ajax({
                        url: $(form).attr('action'), // Get the form action URL
                        type: $(form).attr('method'), // Get the form method (POST)
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Handle successful form submission
                            console.log(response);
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                });
                                // Reset the form and clear error messages
                                $(form)[0].reset();
                                $('.text-danger').html('');
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle form submission error
                            console.error(xhr);
                            if (xhr.responseText) {
                                var errors = xhr.responseJSON.message;
                                $('#general-error').html(errors);
                            }
                        }
                    });
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