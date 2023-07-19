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
      <li class="breadcrumb-item"><a href="{{route('school-fees.index')}}">School Fees Structure</a></li>
      <li class="breadcrumb-item active" aria-current="page">View</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('school-fees.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon> Back</a>
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
                <h4 class="card-title">School Fee Structure</h4>
                <hr>
                <div class="row">
                    
                    <div class="col-md-12">
                        <p class="card-title">Fee Details</p>

                        <table class="table table-bordered mt-3">
                            <thead class="text-center">
                            <tr>
                                <th>Grade</th>
                                <th>Year</th>
                                <th>Term</th>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>{{$grade->name}}</td>
                                    <td>{{$fees->year}}</td>
                                    <td>{{$term->name}}</td>
                                </tr>
                            </tbody>
                            
                      </table>

                        <p class="card-title mt-3">Fee Breakdown</p>
                        <table class="table table-bordered mt-3">
                            <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php $num = 1; ?>
                                @foreach ($entries as $entry)
                                    <tr>
                                        <td>{{$num}}</td>
                                        <?php $num++; ?>
                                        <td>{{$entry->entry}}</td>
                                        <td>{{number_format($entry->amount, 2)}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th class="text-success" style="font-weight: 900; font-size: 16px;">Total</th>
                                    <th class="text-success" style="font-weight: 900; font-size: 16px;">{{number_format($fees->amount, 2)}}</th>
                                </tr>
                            </tbody>
                            
                      </table>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush