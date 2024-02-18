@extends('layouts.main')

@section('title', 'Index')

@section('content')
<div class="row g-4">
    <div class="col-md-10 offset-md-1">
        <div class="bg-light rounded h-100 p-4">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary rounded-pill" id="addApplicant" data-toggle="modal"
                    data-target="#addCardModel">Add Applicant</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="applicant-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('modal.view_modal')
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#applicant-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('applicants.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'first_name', name: 'first_name' },
                { data: 'last_name', name: 'last_name' },
                { data: 'email', name: 'email' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            lengthMenu: [10, 25, 50, 75, 100],
            pageLength: 10,
            responsive: true,
            pagingType: 'simple',
        });
    });

    function editApplicant(applicant_id) {
        window.location.href = "/applicants/" + applicant_id + "/edit";
    }
    function viewApplicant(applicant_id) {
    // Send AJAX request to retrieve applicant data
    $.ajax({
        url: '/show/' + applicant_id,
        type: 'GET',
        success: function(response) {
            // Populate modal with applicant data
            $('#first_name').val(response.applicant.first_name);
            $('#last_name').val(response.applicant.last_name);
            $('#phone').val(response.applicant.phone);
            $('#email').val(response.applicant.email);
            $('#address').val(response.applicant.address);
            $('#dob').val(response.applicant.dob);
            $('#gender').val(response.applicant.gender);

            // Set the image path for the applicant photo
            var applicantPhotoUrl = response.applicant.cropped_image;
            $('#applicant-photo').attr('src', applicantPhotoUrl);

            // Display modal
            $('#applicantDetails').modal('show');
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(xhr.responseText);
        }
    });
}

    $('#addApplicant').on('click', function() {
        window.location.href = "{{ route('applicants.create')}}";
    });

    function confirmDelete(applicantId) {
    Swal.fire({
        title: 'Confirm Deletion',
        text: 'Are you sure you want to delete this applicant?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteApplicant(applicantId);
        }
    });
}

function deleteApplicant(applicantId) {
    $.ajax({
        url: '/delete/' + applicantId,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
                $('#applicant-table').DataTable().row('#applicant_' + applicantId).remove().draw();


            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete applicant'
            });
        }
    });
}

</script>
@endsection

@section('css')
    <style>
        .table {
    width: 100%;
}

.table th,
.table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
    </style>
@endsection

