@extends('layouts.app')
@push('plugin-styles')
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
      <li class="breadcrumb-item"><a href="{{route('term_holiday.index')}}">Holiday</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
        <a href="{{route('term_holiday.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Edit Holiday</h4>
                <hr>
                <form id="my-form" action="{{ route('term_holiday.update', $term->id) }}"  method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Name" required value="{{ $term->name }}">
                            <span class="issue" id="name_error"></span>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Year</label>
                            <input readonly type="text" name="year" class="form-control" id="year" placeholder="Year" required value="{{ $term->year }}" required>
                            <span class="issue" id="year_error"></span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="mb-3 col-md-4 col-sm-12">
                            <label class="form-label" for="platenum">Start Date</label>
                            <input type="date" name="start" class="form-control" id="date_start" required value="{{ $term->start }}">
                            <span class="issue" id="date_start_error"></span>
                        </div>
                        <div class="mb-3 col-md-4 col-sm-12">
                            <label class="form-label" for="platenum">End Date</label>
                            <input type="date" name="end" class="form-control" id="date_end" required value="{{ $term->ends }}">
                            <span class="issue" id="date_end_error"></span>
                        </div>
                        <div class="mb-3 col-md-4 col-sm-12">
                            <label class="form-label" for="">School Term</label>
                            <select name="term_id" id="" class="form-control">
                                <option selected value="{{$schoolterm->id}}">{{ $schoolterm->name }} {{ $schoolterm->year }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-success mt-3" type="button" id="submit-btn"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Changes</button>
                    </div>
                        
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        // Get the current year
        var currentYear = new Date().getFullYear();

        // Set the minimum attribute of the date input
        var dateInput = document.getElementById('date_start');
        dateInput.min = currentYear + "-01-01";

        var dateInputEnd = document.getElementById('date_end');
        dateInputEnd.min = currentYear + "-01-01";

        $('#submit-btn').on('click',(e) => {
            if (!$('#name').val()) {
                $('#name').focus();
                $('#name_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#name_error').text('');
            }

            if (!$('#year').val()) {
                $('#year').focus();
                $('#year_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#year_error').text('');
            }

            if (!$('#date_start').val()) {
                $('#date_start').focus();
                $('#date_start_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#date_start_error').text('');
            }


            if (!$('#date_end').val()) {
                $('#date_end').focus();
                $('#date_end_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#date_end_error').text('');
            }

            $('#my-form').submit();
        })
    })
    

    
</script>
@endpush