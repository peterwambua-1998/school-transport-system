@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
  <style>
    .my-nav {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .issue {
        color: #ff3366;
    }
  </style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('term_events.index')}}">Event</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('term_events.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>
    
@if (Session::has('success'))
    <div class="alert alert-success" role="alert" id="success">
        {{Session::get('success')}}
    </div>
@endif

@if (Session::has('unsuccess'))
    <div class="alert alert-danger" role="alert" id="danger">
        {{Session::get('unsuccess')}}
    </div> 
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Event</h4>
                <hr>
                <form action="{{ route('term_events.store') }}" id="my-form" method="POST">
                    @csrf

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Name</label>
                            <input type="text" name="name" class="form-control" id="title" placeholder="Name" required>
                            <span class="issue" id="title_error"></span>
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="">Within School Days?</label>
                            <select name="within" id="within" class="form-select">
                                <option selected>select...</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="issue" id="within_error"></span>

                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="platenum">Start Date</label>
                            <input type="date" name="start" class="form-control" id="date_start" required>
                            <span class="issue" id="date_start_error"></span>

                            </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">End Date</label>
                            <input type="date" name="ends" class="form-control" id="date_end" required>
                            <span class="issue" id="date_end_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="platenum">Start Time</label>
                            <input type="time" name="start_time" class="form-control" id="start_time" required>
                            <span class="issue" id="start_time_error"></span>

                            </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">End Time</label>
                            <input type="time" name="end_time" class="form-control" id="end_time" required>
                            <span class="issue" id="end_time_error"></span>

                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="">School Term</label>
                            <select name="term_id" id="term_id" class="form-control">
                                <option selected value="{{$schoolterm->id}}">{{ $schoolterm->name }} {{ $schoolterm->year }}</option>
                            </select>
                            <span class="issue" id="term_id_error"></span>
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="platenum">Year</label>
                            <input type="text" name="year" class="form-control" id="year" required value="{{ date('Y') }}">
                            <span class="issue" id="year_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3" >
                            <label class="form-label" for="title">Location</label>
                            <input placeholder="location" type="text" name="location" class="form-control" id="location" required>
                            <span class="issue" id="location_error"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="inputState">@if ($tr)
                                {{ucfirst($tr->grade_class) ?? 'Grades'}}
                                @else
                                Grades
                                @endif</label>
                            @if (count($grades) == 0)
                                <p class="text-danger">Add grades</p>
                            @else 
                            
                            <select id="grades" class="js-example-basic form-select" multiple="mutiple" name="grades[]" required>
                               <option>select...</option> 
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <span class="issue" id="grades_error"></span>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3" id="transport">
                            <label class="form-label" for="title">Transport</label>
                            <select name="transport" id="transport" class="form-control">
                                <option>select...</option>
                                <option value="school">School</option>
                                <option value="parent">Parent</option>
                            </select>
                            <span class="issue" id="transport_error"></span>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Event</button>
                    </div>

                    
                </form>
            </div>
        </div>
    </div>
</div>
    



@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>
    $(document).ready( function () {
        $('#transport').hide('slow');
        $('#within').on('change', (e) => {
            let value = e.target.value;

            if (value == "no") {
                $('#transport').show('slow');
            } else {
                $('#transport').hide('slow');
            }
        });

        


        $('#submit-btn').on('click', function(e) {
            if (!$('#title').val()) {
                $('#title_error').text('field is required');
                e.preventDefault();
                $('html, body').animate({
                        scrollTop: '0px'
                    }, 800);
                $('#title').focus();
                return;
            }else {
                $('#title_error').text('');
            }

            if ($('#within').find(':selected').text() == 'select...') {
                $('#within_error').text('field is required');
                e.preventDefault();
                $('html, body').animate({
                        scrollTop: '0px'
                    }, 800);
                $('#within').focus();
                return;
            }else {
                $('#within_error').text('');
            }


            if (!$('#date_start').val()) {
                $('#date_start_error').text('field is required');
                e.preventDefault();
                $('html, body').animate({
                        scrollTop: '0px'
                    }, 800);
                $('#date_start').focus();
                return;
            }else {
                $('#date_start_error').text('');
            }


            if (!$('#date_end').val()) {
                $('#date_end_error').text('field is required');
                e.preventDefault();
                $('html, body').animate({
                        scrollTop: '0px'
                    }, 800);
                $('#date_end').focus();
                return;
            }else {
                $('#date_end_error').text('');
            }

            if (!$('#start_time').val()) {
                $('#start_time_error').text('field is required');
                e.preventDefault();
                $('#start_time').focus();
                return;
            }else {
                $('#start_time_error').text('');
            }


            if (!$('#end_time').val()) {
                $('#end_time_error').text('field is required');
                e.preventDefault();
                $('#end_time').focus();
                return;
            }else {
                $('#end_time_error').text('');
            }

            if ($('#term_id').find(':selected').val() == 'select...') {
                $('#term_id_error').text('field is required');
                $('#term_id').focus();
                e.preventDefault();
                return;
            }else {
                $('#term_id_error').text('');
            }


            if (!$('#year').val()) {
                $('#year_error').text('field is required');
                e.preventDefault();
                $('#year').focus();
                return;
            }else {
                $('#year_error').text('');
            }


            if (!$('#location').val()) {
                $('#location_error').text('field is required');
                e.preventDefault();
                $('#location').focus();
                return;
            }else {
                $('#location_error').text('');
            }

            if ($('#grades').val().length <= 0) {
                $('#grades_error').text('field is required');
                $('#grades').focus();
                e.preventDefault();
                return;
            }else {
                $('#grades_error').text('');
            }

            console.log($('#within').find(':selected').text());
            if ($('#within').find(':selected').val() == 'no') {
                if ($('#transport').find(':selected').val() == 'select...') {
                    $('#transport_error').text('field is required');
                    $('#transport').focus();
                    e.preventDefault();
                    return;
                }else {
                    $('#transport_error').text('');
                }
            }

            $('#my-form').submit();

        });

        // Get the current year
        var currentYear = new Date().getFullYear();

        // Set the minimum attribute of the date input
        var dateInput = document.getElementById('date_start');
        dateInput.min = currentYear + "-01-01";

        var dateInputEnd = document.getElementById('date_end');
        dateInputEnd.min = currentYear + "-01-01";
    });
</script>
@endpush