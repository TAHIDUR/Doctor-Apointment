@extends('welcome')

@section('content')
    <div class="row mt-2">
        <div class="col-md-4">
            <h5>Create Appointment</h5>

            {{-- Department Select --}}

            <form   class="form-horizontal" method="post"
                    action="{{route('appointment.store')}}"
                    role="form"
                    enctype="multipart/form-data">
            @csrf

                <div class="form-group mb-3">
                    <label for="exampleFormControlFile1">Select Department</label>
                    <select id="department-select" class="form-control form-control-sm mt-1">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="doctor-warning"></div>

                <div id="doctor-select" class="form-group mb-3"></div>

            </form>

        </div>
        
        <div class="col-md-8">
            <h5>Added Appointment</h5>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $('#department-select').on('change',function(){
                let departmentId = $('#department-select').val();

                $.get('/get-doctor', { id: departmentId})
                    .done(function( data ) {
                    if(data)
                    {
                        $('#doctor-select').empty();
                        let doctorId    = data.id;
                        let doctorName  = data.name;

                        let doctorSelect = `
                        <label for="exampleFormControlFile1">Select Doctor</label>
                        <select class="doctor-select form-control form-control-sm mt-1">
                                <option value="${doctorId}">${doctorName}</option>
                        </select>
                        `;

                        $('#doctor-select').append(doctorSelect);
                    }else
                    {
                        $('#doctor-select').empty();
                        let doctorWarning = `<div class="text-danger alert">No Doctor is Found under this department</div>`
                        $('#doctor-warning').append(doctorWarning);
                        timeOut();
                    }
                });
            });
        });

        function timeOut()
        {
            setTimeout(function () {
                $('.alert').alert('close');
            }, 2000);
        }
    </script>
@endpush