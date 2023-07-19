@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Garages</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('garage.create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Garage</a>
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

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Garage Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($garages as $garage)
                                
                            @php
                                
                                $year = date('Y');
                            @endphp
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>{{$garage->name}}</td>
                                <td>{{$garage->location}}</td>
                                <td>{{$garage->contact_person}}</td>
                                <td>{{$garage->contact_phone}}</td>
                                <td>
                                    @if ($garage->active)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td style="display:grid;grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                        @if ($garage->active)
                                        <a href="#" class="mywish" data-lat="{{$garage->lat}}" data-lng="{{$garage->lng}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details">
                                            <i class="fa-solid fa-eye text-info" data-bs-toggle="modal" data-bs-target="#garage{{$garage->id}}"></i>
                                        </a>
                                        @endif

                                        <a href="{{ route('garage.edit', Crypt::encrypt($garage->id)) }}" >
                                            <span><i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit garage details"></i></span>
                                        </a>
                                    @endif

                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')  
                                    @if ($garage->active)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#del{{$garage->id}}">
                                        <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate garage"></i></span>
                                    </a>
                                    @else
                                    <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$garage->id}}">
                                        <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate garage" style="font-size: 16px;"></i>
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


<!-- Modal -->
@foreach ($garages as $key => $garage)
<div class="modal fade" id="garage{{$garage->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">{{$garage->name}} Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div id="map{{$key}}" style="width: 100%; height:60vh"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
        
    </div>
</div>
@endforeach

<!-- Modal -->
@foreach ($garages as $key => $garage)
<div class="modal fade" id="del{{$garage->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('garage.destroy', $garage->id) }}" method="post" style="display: inline-block">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="exampleModalCenterTitle">
                        <i class="far fa-lightbulb text-danger" style="margin-right: 10px;"></i>
                        Deactivate {{$garage->name}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="garage_id" value="{{ $garage->id }}">
                    <p>Garage will be inactive. Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Deactivate</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>

                </div>
                </form>
            </div>
    </div>
</div>
@endforeach


@foreach ($garages as $key => $garage)
<div class="modal fade" id="activate{{$garage->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('activate_garage') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Activate {{$garage->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Garage will be active. Are you sure?</p>
                
                @csrf
                <input type="hidden" name="garage_id" value="{{$garage->id}}">
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-success">Activate</button>
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
</div>
@endforeach


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"></script>
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
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: message,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                    
                }
            });
            
        }

        var nodeList = $('.mywish');
        nodeList.each(function(index) {
            $(this).on('click', () => {
                var lat = $(this).attr('data-lat');
                var lng = $(this).attr('data-lng');

                let myLatlng = { lat: '{{ $settings->lat }}' - 0, lng: '{{ $settings->lng }}' - 0 };
                let map = new google.maps.Map(document.getElementById("map" + index), {
                    zoom: 10,
                    center: myLatlng,
                });
                let makers = [{lat: lat - 0, lng: lng - 0}];
                const image = "https://cdn-icons-png.flaticon.com/512/1183/1183390.png";
                for (let t = 0; t < makers.length; t++) {
                    new google.maps.Marker({
                        position: makers[t],
                        label: {text: "Garage location", color: "#1e293b", fontSize: "15px", className: "label-marker"},
                        icon: {
                            url: image,
                            scaledSize: new google.maps.Size(50, 50), // scaled size
                            
                        },
                        map: map
                    });
                    map.panTo(makers[t]);
                }
            });
        });
                
    </script>
@endpush