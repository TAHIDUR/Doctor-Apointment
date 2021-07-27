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

                <div class="form-group mb-2">
                    <label for="exampleFormControlFile1">Select Department</label>
                    <select name="departmentId" id="department-select" class="form-control form-control-sm mt-1">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="doctor-warning"></div>

                <div id="doctor-select" class="form-group mt-2"></div>

                <div id="date-select" class="form-group mt-2">
                    <label for="exampleFormControlFile1">Select Date</label>
                    <div name="date" class='input-group date' id='datetimepicker'>
                        <input id="date" type='text' class="form-control" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>

                <div id="schedule-warning"></div>

                <div id="schedule-select" class="form-group mt-2"></div>

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

            $('#date-select').css("visibility", "hidden");

            //Check Doctor Under Selected Deparment
            $('#department-select').on('change',function(){
                // getting doctors data

                let departmentId = $('#department-select').val();

                $.get('/get-doctor', { id: departmentId})
                    .done(function( data ) {
                    if(data)
                    {
                        $('#doctor-select').empty();
                        let doctorId    = data.id;
                        let doctorName  = data.name;

                        //Doctor Selection 
                        let doctorSelect = `
                        <label for="exampleFormControlFile1">Select Doctor</label>
                        <select name="doctorId" class="doctor-select form-control form-control-sm">
                                <option value="${doctorId}">${doctorName}</option>
                        </select>
                        `;

                        $('#doctor-select').append(doctorSelect);

                    }else
                    {
                        $('#doctor-select').empty();
                        $('#date-select').css("visibility", "hidden");
                        let doctorWarning = `<div class="text-danger alert m-0">No Doctor is Found under this department</div>`
                        $('#doctor-warning').append(doctorWarning);
                        timeOut();
                    }
                });
            });


        });

        // warning timeout function
        function timeOut()
        {
            setTimeout(function () {
                $('.alert').alert('close');
            }, 3000);
        }
        
        //Date Selection 
        $('#doctor-select').on('click', function(){

            $('#date-select').css("visibility", "");

            $(function() {
                $('#datetimepicker').datetimepicker({
                        format: 'DD-MM-YYYY'
                });
            });
        });

        // console.log();
        $('#date').click(function(){
            let date        = $('#date').val();
            let doctorId    = $('.doctor-select').val();
            if(date && doctorId)
            {

                $.get('/get-schedule', { id: doctorId, date: date}) 
                    .done (function (data){

                    if(typeof(data) == 'string')
                    {
                        let scheduleWarning = `<div class="text-danger alert m-0 p-0">Schedule Not Found for ${data}</div>`
                        $('#schedule-warning').append(scheduleWarning);
                        timeOut();
                    }else if(data.date)
                    {
                        let scheduleWarning = `<div class="text-danger alert m-0 p-0">Doctor is unavailable on that particular day</div>`
                        $('#schedule-warning').append(scheduleWarning);
                        timeOut();
                    }else
                    {
                        let scheduleId  = data.id;
                        let startTime   = data.start_time;
                        let endTime     = data.end_time;
                        let maxPatient  = data.maximum_patient;

                        //Doctor Selection 
                        let scheduleSelect = `
                        <label for="exampleFormControlFile1">Select Doctor</label>
                        <select name="schedule" class="schedule-select form-control form-control-sm">
                                <option value="${scheduleId}">${startTime} to ${endTime}</option>
                        </select>
                        `;

                        $('#schedule-select').append(scheduleSelect);
                    }
                    
                });
            }
        });

        
    </script>
@endpush