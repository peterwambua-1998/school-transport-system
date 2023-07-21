@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">Students</a></li>
      <li class="breadcrumb-item active" aria-current="page">Absent</li>
    </ol>
</nav>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

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



<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Students Absent Today</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped"  id="dataTableExample" data-ordering='false'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Reason</th>
                                <th>Time</th>
                                <th>date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($flagoffs as $flagoff)
                            <td>{{ $number }}</td>
                            <?php $number++; ?>
                            <td>{{ $flagoff->student->first_name }}</td>
                            <td>{{ $flagoff->student->last_name }}</td>
                            <td>{{ $flagoff->reason }}</td>
                            <td>{{ $flagoff->time }}</td>
                            <td>{{ $flagoff->date }}</td>
                            
                                
                            @endforeach
                            

                        </tbody>
                        
                        
                    </table>
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
        $( function() {
            $( document ).tooltip();
        });

      
    </script>
@endpush