@extends('layouts.app')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/js/photoswipe.umd.min.js') }}"></script>
<script src="{{ asset('assets/js/photoswipe-lightbox.umd.min.js') }}"></script>
<link href="{{ asset('css/photoswipe.css') }}" rel="stylesheet" />
<style>
.custom-html-slide {
  font-size: 40px;
  line-height: 45px;
  max-width: 400px;
  width: 100%;
  padding: 0 20px;
  margin: 50px auto 0;
  color: #fff;
}
.custom-html-slide a {
  color: #fff;
  text-decoration: underline;
}
</style>
@endpush

@section('content')

<nav class="page-breadcrumb" style="display:grid; grid-template-columns: 1fr 1fr;">
  <ol class="breadcrumb" style="width: 75%">
    <li class="breadcrumb-item"><a href="#">Maintenance</a></li>
    <li class="breadcrumb-item active" aria-current="page">List</li>
  </ol>

  <div style="display: flex; flex-direction: row-reverse;">
    <a href="{{url('/maintenance')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon>Back</a>
  </div>
</nav>
     

<h6 class="card-title"></h6>

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
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      
      <div class="card-body">
        <h6 class="card-title">{{$vehicle->plate_num}} Maintenance Records</h6>
      <hr>
        <ul  class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
          
          <li class="nav-item text-center" style="width: 33.33%;">
            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">Daily Condition</a>
          </li>
          <li class="nav-item text-center" style="width: 33.33%;">
            <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">Routine</a>
          </li>
          <li class="nav-item text-center" style="width: 33.33%;">
            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">Off Routine</a>
          </li>
          
        </ul>
        <div class="tab-content mt-3" id="lineTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            @include('maintenance.includes.daily')
          </div>
          <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            @include('maintenance.includes.routine')
          </div>
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            @include('maintenance.includes.off-routine')
          </div>
          
        </div>
      </div>
  </div>
</div>
</div>

 


@endsection
@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
       
    </script>
@endpush