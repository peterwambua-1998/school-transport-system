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
      <li class="breadcrumb-item"><a href="{{route('grades_page')}}">Stream</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <h4 class="card-title">Add Stream</h4>
                <hr>
                <form action="{{ route('streamUpdateStore') }}" id="my-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label" for="grade">{{ucfirst($tr->grade_class) ?? 'Grades'}}</label>
                            <select name="grade" id="grade" class="form-select">
                                <option>select...</option>
                                @foreach ($grades as $grade)
                                    <option @if($stream->student_classes_id == $grade->id) selected @endif value="{{$grade->id}}">{{$grade->name}}</option>
                                @endforeach
                            </select>
                            <span class="issue" id="grade_error"></span>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="teacher">Teacher</label>
                            <select name="teacher" id="teacher" class="form-select">
                                <option>select...</option>
                                @foreach ($teachers as $teacher)
                                    <option @if($teacher->id == $stream->class_teacher) selected @endif value="{{$teacher->id}}">{{$teacher->name}}</option>
                                @endforeach
                            </select>
                            <span class="issue" id="teacher_error"></span>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="">Stream</label>
                            <input class="form-control" type="text" name="streams" id="streams" value="{{$stream->name}}">
                            <span class="issue" id="streams_error"></span>
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{$stream->id}}">

                    <div class="text-center">
                        <button type="submit" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update Streams</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script defer>
    let trs = {};
    let trsLookup = {};
    let num;
    $(document).ready( function () {
        $('#num_of_streams').on('input', function(e) {
            num = $('#num_of_streams').val();
            
            createInputs(num);
        });
    });

    

    $.ajax({
        type: "get",
        url: "/stream-teachers",
        contentType: false,
        cache: false,
        processData: false,
        error: function(err) {
            console.log(err);
        },
        success: function(response) {
            console.log(response);
            for (let t = 0; t < response.length; t++) {
                trs[response[t].id] = response[t].id;
                trsLookup[response[t].id] = response[t].name;
            }
            console.log(trs);
            console.log(trsLookup);
        }
    })



    function createInputs(num) {
        let x = 1;
        for (let i = 0; i < num; i++) {
            let template = `
            <div class="row"> 
                <div class="col-md-6 mb-3"> 
                    <label class="form-label">Teacher</label>
                    <select name="teacher[]" id="${x}" class="form-select teacher-select" required>
                        <option id="first-select">select...</option>
                        
                    </select>
                </div>
                <div class="col-md-6 mb-3"> 
                    <label class="form-label">Stream</label>
                    <input class="form-control" name="streams[]" placeholder="stream" required>
                </div>
            </div>
            `;  
            $('#more-inputs').append(template);

            x++;
        }

        addOptions();

    }

    function addOptions() {
        console.log($('.teacher-select').length);
        $('.teacher-select').each(function(i) {
            
            for (const key in trs) {
               let opt = `<option value='${trs[key]}'>${trsLookup[key]}</option>`;
               $(opt).insertAfter($(this).children(':first'));
            }
        });

        getNodelist();
    }

    

   

    function getNodelist() {
        $('.teacher-select').each(function(i) {
            
            $(this).on('change', function() {
                let id = $(this).attr('id');
                let value = $(this).find(":selected").val();

                if (! state[value]) {
                    state[value] = value;
                    delete trs[value];

                } else {
                    if (condition) {
                        trs[value] = value;
                        delete state[value];
                    }
                } 

                $('.teacher-select').each(function() {
                    if ($(this).attr('id') == id) {
                        return true;
                    } else {
                        console.log($(this).find(":selected").val());
                        if ($(this).find(":selected").val() == 'select...') {
                            $(this).empty();
                        
                            $(this).append('<option>select...</option>')
                            for (const key in trs) {
                                let opt = `<option value='${trs[key]}'>${trsLookup[key]}</option>`;
                                //$(opt).insertAfter($(this).children(':first'))
                                $(this).append(opt);
                            }
                        }
                    }
                });

               

                
                console.log(state, trs);

            });
        });
    }

    let state = {};


    $('#submit-btn').on('click',(ev)=> {
        if($('#grade').find(':selected').text() == 'select...'){
            ev.preventDefault();
            $('#grade').focus();
            $('#grade_error').text('field required');
            return;
        } else {
            $('#grade_error').text('');
        }

        if($('#teacher').find(':selected').text() == 'select...'){
            ev.preventDefault();
            $('#teacher').focus();
            $('#teacher_error').text('field required');
            return;
        } else {
            $('#teacher_error').text('');
        }

        if(!$('#streams').val()){
            ev.preventDefault();
            $('#streams').focus();
            $('#streams_error').text('field required');
            return;
        } else {
            $('#streams_error').text('');
        }


        $('#my-form').submit();
    })
</script>
@endpush