@extends('layouts.app')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
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
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">Students</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('students.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
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
                <h4 class="card-title">Add Student</h4>
                <hr>
                <form action="{{ route('students.store') }}"  method="POST" id="my-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">First Name</label>
                            <input type="text" name="fname" class="form-control" id="fname" placeholder="First Name" required>
                            <span class="issue" id="fname_error"></span>
                        
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Last Name</label>
                            <input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name" required>
                            <span class="issue" id="lname_error"></span>
                        
                        </div>
                    </div>
                    
                    <div class="row">
                        
                        <div class="col-md-6">
                            <label class="form-label" for="">Gender</label>
                            <select class="form-select" name="gender" id="gender" required>
                                <option>select...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <span class="issue" id="gender_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Admission Number</label>
                            <input type="number" name="add_num" class="form-control" id="add_num" placeholder="Admission Number" required>
                            <span class="issue" id="add_num_error"></span>
                        </div>

                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">{{ucfirst($tr->grade_class) ?? 'Grades'}}</label>
                            @if (count($classes) <= 0)
                                <p class="text-danger">Please add {{ucfirst($tr->plural) ?? 'Grades'}}</p>
                            @else
                            <select id="classes" class="form-select" name="grade" required>
                                <option>select...</option>
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->name}}</option>
                                @endforeach
                            </select>
                            @endif
                            <span id="classes_error" class="issue"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Stream</label>
                            @if (count($classes) <= 0)
                                <p class="text-danger">Please add stream</p>
                            @else
                            <select id="streams" class="form-select streams" name="stream" required>
                               <option value="">select...</option>
                                
                            </select>
                            @endif
                            <span class="issue" id="streams_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState">Parent</label>
                            @if (count($parents) <= 0)
                                <p class="text-danger">Please add parent</p>
                            @else
                            <select id="parent_id" class="form-control parent_id" name="parent_id" required>
                                <option>select...</option>
                                @foreach ($parents as $parent)
                                <option value="{{$parent->id}}">{{ $parent->name }} (ID {{ $parent->id_num }}) </option>
                                @endforeach
                                
                            </select>
                            @endif
                            <span class="issue" id="parent_id_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="">Relationship</label>
                            <select class="form-select" name="relationship" id="relationship" required>
                                <option>select...</option>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                            </select>
                            <span id="relationship_error" class="issue"></span>
                        </div>
                    </div>

                    

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="exampleFormControlFile1">Photo</label>
                            <br>
                            <input type="file" class="form-control-file" id="myDropify" name="image">
                        </div>
                        
                    </div>
                  
                    <div class="text-center">
                        <button type="button" id="my_submit" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Student</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script defer>
    $(document).ready(function() {
        $('.parent_id').select2();
    });

    $(window).keydown(function(event){
        if(event.keyCode == 13) {
        event.preventDefault();
        return false;
        }
    });
    
     $('#my_submit').on('click', (e) => {
        if(!$('#fname').val()){
            $('#fname_error').text('field required');
            e.preventDefault();
            $('#fname').focus();
            return;
        } else {
            $('#fname_error').text('');
        }

        if(!$('#lname').val()){
            $('#lname_error').text('field required');
            e.preventDefault();
            $('#lname').focus();
            return;
        } else {
            $('#lname_error').text('');
        }

        if($('#gender').find(':selected').val() == 'select...'){
            $('#gender_error').text('field required');
            e.preventDefault();
            $('#gender').focus();
            return;
        } else {
            $('#gender_error').text('');
        }



        if(!$('#add_num').val()){
            e.preventDefault();
            $('#add_num').focus();
            $('#add_num_error').text('field required');
            return;
        } else {
            $('#add_num_error').text('');
        }
    
    
        if($('#classes').find(':selected').val() == 'select...'){
            $('#classes_error').text('field required');
            e.preventDefault();
            $('html, body').animate({
                scrollTop: '0px' 
            }, 800);
            $('#classes').focus();
            return;
        } else {
            $('#classes_error').text('');
        }
    
        if($('.streams').find(':selected').text() == 'select...'){
            e.preventDefault();
            $('html, body').animate({
                scrollTop: '0px' 
            }, 800);
            $('#streams').focus();
            $('#streams_error').text('field required');
            return;
        } else {
            $('#streams_error').text('');
        }

        if($(".parent_id option:selected").text() === 'select...'){
            e.preventDefault();
            $('#parent_id').focus();
            $('#parent_id_error').text('field required');
            return;
        } else {
            $('#parent_id_error').remove();

        }

        if($('#relationship').find(':selected').text() == 'select...'){
            $('#relationship_error').text('field required');
            e.preventDefault();
            $('#relationship').focus();
            return;
        } else {
            $('#relationship_error').text('');
        }

        $('#my-form').submit();
    });
    

    $('#classes').on('change', function(e) {
        $('#streams option:gt(0)').remove();

        let class_id = e.target.value;

        $.ajax({
            type: "GET",
            url: `/get-streams/${class_id}` ,
            processData: false,
            contentType: false,
            cache: false,
            error: function(data){
                console.log(data);
            },
            success: function (response) {
                for (let v = 0; v < response.length; v++) {
                    let option = `<option value="${response[v].id}">${response[v].name}</option>`;
                    $('#streams').append(option);
                }
                
            }
        });
    });


  

</script>
@endpush