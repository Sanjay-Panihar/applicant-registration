@extends('layouts.main')

@section('title', 'Index')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary rounded-pill mb-2" id="addApplicant" data-toggle="modal"
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

    $('#addApplicant').on('click', function() {
        window.location.href = "{{ route('applicants.create')}}";
    });
</script>
@endsection
