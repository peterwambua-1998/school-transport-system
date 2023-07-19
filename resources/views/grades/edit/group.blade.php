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
      <li class="breadcrumb-item"><a href="{{route('grades_page')}}">Group</a></li>
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
                <h4 class="card-title">Edit Group</h4>
                <hr>
                <form action="{{ route('groupUpdateStore') }}" id="my-form" method="POST">
                    @csrf

                    <div class="row">
                        <div class="mb-3 col-md-12 col-sm-12">
                            <label class="form-label" for="title">Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Name" required value="{{$group->name}}">
                            <span class="issue" id="group_error"></span>
                        </div>
                        
                    </div>

                    <input type="hidden" name="id" value="{{$group->id}}">

                    <div class="text-center">
                        <button class="btn btn-success mt-3"  type="button" id="submit-btn"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update Group</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer>
    $(function() {
        $('#submit-btn').on('click', function() {
            if(!$('#name').val()){
                $('#group_error').text('field required');
                $('#name').focus();
                return;
            }else{
                $('#group_error').text('');

            }

            $('#my-form').submit();
        })
    })
</script>
@endpush