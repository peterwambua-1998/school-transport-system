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
      <li class="breadcrumb-item"><a href="#">School Terms</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>

    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('term.create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Term</a>
    </div>
    @endif
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


<div class="alert alert-success my-success" role="alert" id="success">
    Change saved
</div>


<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">School Term Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($terms as $term)
                                
                            @php
                                
                                $year = date('Y');
                            @endphp
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>{{$term->name}}</td>
                                <td>{{date_format(date_create($term->start), 'd-M-Y')}}</td>
                                <td>{{date_format(date_create($term->ends), 'd-M-Y')}}</td>
                                <td>{{ $year }}</td>
                                <td>
                                    @if ($term->status)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                    @if ($term->status)
                                    <i class="fa-solid fa-toggle-on text-success click-active" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Change term to be active"  style="color: #facc15; margin-right: 15px; font-size: 20px;" @if (Auth::user()->user_type != 'parent' && Auth::user()->user_type != 'driver') onclick="activate(0, {{$term->id}})" @endif></i>
                                    @else
                                    <i class="fa-solid fa-toggle-off text-danger click-active mr-5" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Change term to be inactive" style="margin-right: 15px; font-size: 20px;" onclick="activate(1, {{ $term->id }})"></i>  
                                    @endif

                                    @if (!$term->status)
                                    <a href="{{ route('term.edit', Crypt::encrypt($term->id)) }}" class="span-delete" style="margin-right: 15px;"  title="Edit Term Details">
                                        <span><i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" title="Edit term details" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit term details"></i></span>
                                    </a>
                                    @endif
                                @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        
                        
                    </table>
                </div>
                <div class="text-center"></div>     
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