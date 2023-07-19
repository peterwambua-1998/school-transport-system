@extends('layouts.app')
@push('plugin-styles')
  <style>
    .my-nav {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
  </style>
@endpush
@section('content')

<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('term.index')}}">School Term</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
        <a href="{{route('term.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Edit School Term</h4>
                <hr>
                <form action="{{ route('term.update', $term->id) }}"  method="POST">
                    @csrf
                    @method('PATCH')
                    
                    
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Name</label>
                            <input type="text" name="name" class="form-control" id="title" placeholder="Name" required value="{{ $term->name }}">
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Year</label>
                            <input type="text" name="year" class="form-control" id="title" placeholder="Year" required value="{{ $term->year }}" required>
                        </div>
                        
                        </div>


                        <div class="row">
                            <div class="mb-3 col-md-6 col-sm-12">
                                <label class="form-label" for="platenum">Start Date</label>
                                <input type="date" name="start" class="form-control" id="desc" required value="{{ $term->start }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="platenum">End Date</label>
                                <input type="date" name="end" class="form-control" id="desc" required value="{{ $term->ends }}">
                            </div>
                            
                        </div>
                        
                        <div class="text-center">
                            <button class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Changes</button>
                        </div>
                        
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush