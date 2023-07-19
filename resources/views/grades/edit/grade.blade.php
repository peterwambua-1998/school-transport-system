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
      <li class="breadcrumb-item"><a href="{{route('grades_page')}}">{{ucfirst($tr->grade_class) ?? 'Grades'}}</a></li>
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
                <h4 class="card-title">Edit {{ucfirst($tr->grade_class) ?? 'Grades'}}</h4>
                <hr>
                <form action="{{ route('gradeUpdateStore') }}" id="my-form" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label" for="group">Group</label>
                            <select name="group" id="group" class="form-select">
                                <option>select...</option>
                                @foreach ($groups as $group)
                                    <option @if($group->id == $grade->group) selected @endif value="{{$group->id}}">{{$group->name}}</option>
                                @endforeach
                            </select>
                            <span class="issue" id="group_error"></span>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Name" required value="{{$grade->name}}">
                            <span class="issue" id="name_error"></span>
                        </div>

                        <input type="hidden" name="id" value="{{$grade->id}}">
                        
                    </div>

                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update {{ucfirst($tr->grade_class) ?? 'Grades'}}</button>
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
    $('#submit-btn').on('click',(e) => {
            if ($('#group').find(':selected').text() == 'select...') {
                $('#group').focus();
                e.preventDefault();
                $('#group_error').text('field required');
                return;
            } else {
                $('#group_error').text('');
            }

            if (!$('#name').val()) {
                $('#name').focus();
                e.preventDefault();
                $('#name_error').text('field required');
                return;
            } else {
                $('#name_error').text('');
            }

            $('#my-form').submit();
        })

</script>
@endpush