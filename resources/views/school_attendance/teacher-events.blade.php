@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Events Table</h6>
                <p class="text-muted mb-3"></p>   
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Year</th>
                                <th>Location</th>
                                <th>Transport</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($terms as $term)
                            @php
                                $year = date('Y');
                            @endphp
                            <tr>
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                
                                <td>{{$term->name}}</td>
                                <td>{{$term->start}}</td>
                                <td>{{$term->ends}}</td>
                                <td>{{$term->start_time }}</td>
                                <td>{{$term->end_time }}</td>
                                <td>{{ $term->year }}</td>
                                <td>{{$term->location}}</td>
                                <td>{{$term->transport}}</td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection



