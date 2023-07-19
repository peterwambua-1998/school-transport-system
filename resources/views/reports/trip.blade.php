@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .my-success {
            display: none;
        }
        .click-active:hover {
            cursor: pointer;
        }
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Trips Report</a></li>
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
                <h6 class="card-title">General Trips Report</h6>
                    
                <div class="accordion" id="accordionExample">
                @foreach ($vehicles as $vehicle)
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Vehicle {{$vehicle->plate_num}}
                        </button>
                      </h2>
                      <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-4">
                                          <div class="mt-3">
                                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Bus Reg. No:</label>
                                            <p class="text-muted">{{$vehicle->plate_num}}</p>
                                          </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mt-3">
                                              <label class="tx-11 fw-bolder mb-0 text-uppercase">Driver:</label>
                                              <p class="text-muted">{{$vehicle->driver->name}}</p>
                                            </div>
                                          </div>
                                          <div class="col-md-4">
                                            <div class="mt-3">
                                              <label class="tx-11 fw-bolder mb-0 text-uppercase">Attendant:</label>
                                              <p class="text-muted">{{$vehicle->attendant->name}}</p>
                                            </div>
                                          </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('reports.trips_includes.general')
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                @endforeach
                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
        
        
        function activate(activated, term_id) {
           
            
            var data = new FormData;
            data.append('_token', '{{csrf_token()}}');
            data.append('status', activated);
            data.append('term_id', term_id);

            $.ajax({
                type: "POST",
                url: "{{route('activate_term')}}",
                processData: false,
                contentType: false,
                cache: false,
                data: data,
                        
                error: function(data){
                    console.log(data);
                },
                success: function (message) {
                    console.log(message);
                    $('.my-success').show('slow');
                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                    
                }
            });
            
        }

    </script>
@endpush