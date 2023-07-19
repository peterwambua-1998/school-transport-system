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
      <li class="breadcrumb-item"><a href="{{route('grades_page')}}">Streams</a></li>
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
                <h4 class="card-title">Add Stream</h4>
                <hr>
                <form action="{{ route('stream_store') }}"  method="POST" id="my-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            @if (count($grades) <= 0)
                                <p class="text-danger">Please add {{ucfirst($tr->grade_class) ?? 'Grades'}}</p>
                            @else
                                <label class="form-label" for="grade">{{ucfirst($tr->grade_class) ?? 'Grades'}}</label>
                                <select name="grade" id="grade" class="form-select">
                                    <option>select...</option>
                                    @foreach ($grades as $grade)
                                        <option value="{{$grade->id}}">{{$grade->name}}</option>
                                    @endforeach
                                </select>
                                <span class="issue" id="grade_error"></span>

                            @endif
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Number of strams</label>
                            <input type="number" name="num_of_streams" class="form-control" id="num_of_streams" placeholder="0" required>
                            <span class="issue" id="num_of_streams_error"></span>
                        </div>
                        
                    </div>

                    <div id="more-inputs">

                    </div>

                    <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Streams</button>
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
    let trs2 = {};
    let trsLookup = {};
    let num;

    $(document).ready( function () {
        $('#num_of_streams').on('input', function(e) {
            num = $('#num_of_streams').val();
            en = false;

            getStreamTeachers(num);
        });
    });
    
    function getStreamTeachers(num) {
        trs = {};
        trsLookup = {};
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
                for (let t = 0; t < response.length; t++) {
                    trs[response[t].id] = response[t].id;
                    trs2[response[t].id] = response[t].id;
                    trsLookup[response[t].id] = response[t].name;
                }
                
                createInputs(num);
            }
        });
    }

    function createInputs(num) {
        $('#more-inputs').children().remove();
        let x = 1;
        for (let i = 0; i < num; i++) {
            let template = `
            <div class="row"> 
                <div class="col-md-6 mb-3"> 
                    <label class="form-label">Teacher</label>
                    <select name="teacher[]" id="${x}" class="form-select teacher-select" required>
                        <option id="first-select">select...</option>
                        
                    </select>
                    <span class="issue" id="teacher-select-error"></span>
                </div>
                <div class="col-md-6 mb-3"> 
                    <label class="form-label">Stream</label>
                    <input class="form-control stream" name="streams[]" placeholder="stream" required>
                    <span class="issue" id="stream-error"></span>

                </div>
            </div>
            `;  
            $('#more-inputs').append(template);

            x++;
        }

        addOptions();
    }

   

    function addOptions() {
        $('.teacher-select').each(function(i) {
            for (const key in trs) {
               let opt = `<option value='${trs[key]}'>${trsLookup[key]}</option>`;
               $(opt).insertAfter($(this).children(':first'));
            }
        });

        getNodelist();
    }

    let en = false;

    function getNodelist() {
        en = false;
        let allDontHaveValues = false;

        $('.teacher-select').each(function(i) {
            $(this).on('change', function() {
                let allDontHaveValues = false;

                $('.teacher-select').each(function(es) {
                    if ($(this).val() == "select...") {
                        allDontHaveValues = true;
                        console.log($(this).val());
                        return;
                    }
                });

                let id = $(this).attr('id');
                let value = $(this).find(":selected").val();
                
                delete trs[value];
                
                console.log(allDontHaveValues);
                
                if (en) {
                    for (const keys in trs2) {
                        trs[keys] = trs2[keys];
                    }
                    $('.teacher-select').each(function(e) {
                        $('#more-inputs').children().remove();
                        console.log(trs);
                        console.log(trs2);
                        createInputs(num);
                        en = false;
                        allDontHaveValues = true;
                        //$(this).empty();
                        
                        //$(this).append('<option>select...</option>');
                    });

                }

                if (!allDontHaveValues) {
                    en =true;
                }

               

                $('.teacher-select').each(function() {
                    if ($(this).attr('id') == id && !allDontHaveValues) {
                        return true;
                    } else {
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

        if(!$('#num_of_streams').val()){
            ev.preventDefault();
            $('#num_of_streams').focus();
            $('#num_of_streams_error').text('field required');
            return;
        } else {
            $('#num_of_streams_error').text('');
        }

        let no_value;
        $('.teacher-select').each((i, e) => {
            
            if ($(e).find(':selected').text() == 'select...') {
                ev.preventDefault();
                $(e).focus();
                $(e).next().text('field required');
                no_value = true;
            } else {
                $(e).next().text('');
            }

            
        })

        if (no_value) {
            return;
        }
        let no_value_two;
        $('.stream').each((i, e) => {
            console.log($(e).find(':selected').text());
            if (!$(e).val()) {
                ev.preventDefault();
                $(e).focus();
                no_value_two = true;
                $(e).next().text('field required');
            } else {
                $(e).next().text('');
            }
        });
        if (no_value_two) {
            return;
        }

        $('#my-form').submit();
    })


    

</script>
@endpush