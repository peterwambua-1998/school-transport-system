@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
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
      <li class="breadcrumb-item"><a href="{{route('grades_page')}}">{{ucfirst($tr->plural) ?? 'Grades'}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('grades_page')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Add {{ucfirst($tr->plural) ?? 'Grades'}}</h4>
                <hr>
                <form action="{{ route('grade_store') }}" id="my-form"  method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label" for="group">Group</label>
                            @if (count($groups) <= 0)
                                <p class="text-danger">Please add group</p>
                            @else
                                <select name="group" id="group" class="form-select">
                                    <option>select...</option>
                                    @foreach ($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                            <span class="issue" id="group_error"></span>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Name</label>
                            <span class="text-success" style="margin-left: 10px;">(Press enter after each {{ucfirst($tr->grade_class) ?? 'Grade'}}.)</span>
                            <input  name="name" id="tagsss" placeholder="Press enter after each {{ucfirst($tr->grade_class) ?? 'Grade'}}"  required>
                            <span class="issue" id="tagsss_error"></span>
                        
                        </div>
                        
                    </div>

                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add {{ucfirst($tr->plural) ?? 'Grades'}}</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
    
<div style="display: none">

</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/tags-input.js') }}"></script>
<script>
    $(function() {
        'use strict';

        let text = "add {{$tr->plural ?? 'grades'}}"

        $('#tagsss').tagsInput({
            'width': '100%',
            'height': '65%',
            'interactive': true,
            'defaultText': text,
            'removeWithBackspace': true,
            'minChars': 0,
            'maxChars': 100,
            'placeholderColor': '#666666'
        });

        $('#submit-btn').on('click',(e) => {
            if ($('#group').find(':selected').text() == 'select...') {
                $('#group').focus();
                e.preventDefault();
                $('#group_error').text('field required');
                return;
            } else {
                $('#group_error').text('');
            }

            if (!$('#tagsss').val()) {
                $('#tagsss').focus();
                e.preventDefault();
                $('#tagsss_error').text('field required');
                return;
            } else {
                $('#tagsss_error').text('');
            }

            $('#my-form').submit();
        })

    });
</script>
@endpush