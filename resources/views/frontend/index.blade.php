@extends('welcome')

@section('content')
    <div class="row mt-2">
        <div class="col-md-4">
            <h3>Create Appointment</h3>

            {{-- Department Select --}}

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

                <div id="schedule-select" class="form-group mt-2">
                    <label for="exampleFormControlFile1">Select Doctor</label>
                    <select name="schedule" class="schedule-select form-control form-control-sm">
                    </select>
                </div>

                <div id="appointment-message"></div>

        </div>
        
        <div class="col-md-8">
            <h3>Added Appointment</h3>

            @php
                $appointments = request()->session()->get('appointments');
            @endphp 

            <table  class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Department</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Schedule</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="appointments">
                    @foreach ($appointments as $key => $appointment)
                        <tr>
                            <td class="item-serial-counter">{{ ++$key }}</td>
                            <td>
                                <input type="hidden" id="appointmentId" value="{{ $appointment->id }}">                                
                                {{ $appointment->doctor->department->name }}
                            </td>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>{{ $appointment->appoint_date }}</td>
                            <td>{{ $appointment->schedule->start_time .'-'.$appointment->schedule->end_time }}</td>
                            <td><div onclick="deleteAppointment(this)" class="btn btn-danger btn-sm">delete</div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form   class="form-horizontal" method="post"
                    role="form"
                    enctype="multipart/form-data">
                    @csrf

                <div id="patient-info"></div>

            </form>
        </div>
    </div>
@endsection

@push('js')


    <script>
        $(document).ready(function () {

            $('#date-select').css("visibility", "hidden");
            $('#schedule-select').css("visibility", "hidden");

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
                        $('#schedule-select').css("visibility", "");           
                        let schedule_options = '';
                        $.each(data, function(key,value){

                            schedule_options = `<option value="${value.id}">${value.start_time} to ${value.end_time}</option>`;
                            $('.schedule-select').append(schedule_options);
                        });


                    }
                    
                });
            }
        });

        $('#schedule-select').click(function(){
            let schedule    = $('.schedule-select').val();
            let date        = $('#date').val();
            
            $.get('/check-appointment', { scheduleId: schedule, date: date})
                .done (function (data){
                
                let check = parseInt(data);

                if(data == 'available')
                {
                    $('#appointment-message').empty();
                    
                    appointmentMessage = `<div class="text-success alert m-0 p-0">Available</div>
                    <button onclick="saveAppointment()" class="btn btn-primary btn-sm col-md-12" id="add" type="button">Add This</button>
                    `
                    $('#appointment-message').append(appointmentMessage);
                }
                else
                {       
                    $('#appointment-message').empty();

                    appointmentMessage = `<div class="text-danger alert m-0 p-0">Maximum Quota (${data}) is Filled</div>`
                    
                    $('#appointment-message').append(appointmentMessage);
                }
            });

        });

        function saveAppointment(){
            let schedule    = $('.schedule-select').val();
            let date        = $('#date').val();

            $.get('/set-appointment', {scheduleId:schedule, date:date}, function(response){ 

                let serial      = response.sl;
                let id          = response.id;
                let department  = response.department;
                let date        = response.date;
                let doctor      = response.doctor;
                let schedule    = response.schedule;
                let appointmentData = $('#appointments');

                let savedAppointment = `
                <tr>
                    <td>${serial}</td>
                    <td>
                        <input type="hidden" id="appointmentId" value="${id}">                                
                        ${department}
                    </td>
                    <td>${doctor}</td>
                    <td>${date}</td>
                    <td>${schedule}</td><td><div onclick="deleteAppointment(this)" class="btn btn-danger btn-sm">delete</div></td>
                </tr>
                `
                appointmentData.append(savedAppointment);

                $('#doctor-select').empty();
                $('#date-select').css("visibility", "hidden");
                $('#schedule-select').css("visibility", "hidden");
                $('#appointment-message').empty();
                $('#patient-info').empty();

                let patientInfo = `
                    <h3>Patient Information</h3>
                    
                    <div style="margin-top: 20px;" class="form-group mt-2">
                        <input id="patientName" name="patientName" type='text' class="form-control" placeholder="Patient Name"/>
                        <input style="margin-top: 20px;" id="patientNumber" name="patientNumber" type='text' class="form-control" placeholder="Patient Number" />
                    </div>
                    <button style="margin-top: 20px;" class="btn btn-primary btn-sm col-md-12" id="add" type="submit">Pay with Paypal</button>
                `;

                $('#patient-info').append(patientInfo);
            });
        }

        function deleteAppointment(obj){

            let deletingRow = $(obj).closest('tr');
            let appointmentId = deletingRow.find('#appointmentId').val();

            deletingRow.remove();

            setItemSerial();

            $.get('/delete-appointment', {  scheduleId:appointmentId });
        }

        function setItemSerial() {
            $('.item-serial-counter').each(function(counter) {
                $(this).text(counter + 1)
            })
        }
        
    </script>
@endpush