@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <script src="{{ asset('assets/js/photoswipe.umd.min.js') }}"></script>
  <script src="{{ asset('assets/js/photoswipe-lightbox.umd.min.js') }}"></script>
  <link href="{{ asset('css/photoswipe.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:grid; grid-template-columns: 1fr 1fr;">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Incidents</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    
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
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Incidents Table</h6>
                <div class="">
                    <ul  class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                     
                      <li class="nav-item text-center" style="width: 25%;">
                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">Student</a>
                      </li>
                      <li class="nav-item text-center" style="width: 25%;">
                        <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">Parent</a>
                      </li>
                      <li class="nav-item text-center" style="width: 25%;">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">Driver</a>
                      </li>
                      <li class="nav-item text-center" style="width: 25%;">
                        <a class="nav-link" id="attendant-tab" data-bs-toggle="tab" data-bs-target="#attendant" role="tab" aria-controls="attendant" aria-selected="false">Attendant</a>
                      </li>
                    </ul>
                    <div class="tab-content mt-3" id="lineTabContent">
                      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @include('incidents.includes.student')
                      </div>
                      <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        @include('incidents.includes.parent')
                      </div>
                      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        @include('incidents.includes.driver')
                      </div>
                      <div class="tab-pane fade" id="attendant" role="tabpanel" aria-labelledby="attendant-tab">
                        @include('incidents.includes.attendant')
                      </div>
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
    
@endpush