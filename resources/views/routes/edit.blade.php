@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
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
      <li class="breadcrumb-item"><a href="{{route('routes.index')}}">Route</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('routes.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>

<!-- form to store coordinates -->
<form action="{{ route('routes.update', $route->id) }}" method="post" id="pathForm">
    @csrf
    @method('PATCH')
   
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add Route</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label  class="form-label" for="title">Name</label>
                                <input type="text" name="title" class="form-control" id="title" placeholder="Name" required value="{{ old('title', $route->title) }}">
                                <span class="issue" id="name-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label  class="form-label" for="platenum">Description</label>
                                <input type="text" name="description" class="form-control" id="desc" placeholder="Description" required value="{{ old('description', $route->description) }}">
                                <span class="issue" id="desc-error"></span>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        
                        <div class="mb-3">
                            <div class="col-md-6">
                                <label  class="form-label" for="inputState">Zone</label>
                                @if(count($zones) === 0)
                                <p class="text-danger">Add active zone</p>
                                
                                @else
                                <select id="zones" class="js-example-basic form-select" multiple="mutiple" name="zone[]" required>
                                    @foreach ($zones as $zone)
                                        @php
                                            $zone_route = DB::table('route_zones')->where('route_id','=',$route->id)->where('zone_id','=', $zone->id)->first();
                                        @endphp
                                        <option @if($zone_route) @if($zone_route->zone_id == $zone->id) selected @endif @endif value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <span class="issue" id="zones-error"></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update Route Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
 
@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>
    $(function() {
        $('#submit-btn').on('click',(e) => {
            if (!$('#title').val()) {
                $('#name-error').text('field required');
                e.preventDefault();
                $('#title').focus();
                return;
            } else {
                $('#name-error').text('');
            }
            if (!$('#desc').val()) {
                $('#desc-error').text('field required');
                $('#desc').focus()
                e.preventDefault();
                return;
            } else {
                $('#desc-error').text('');
            }

            if ($('#zones').val().length <= 0) {
                $('#zones-error').text('field required');
                $('#zones').focus();
                e.preventDefault();
                return;
            } else {
                $('#zones-error').text('');
            }

            $('#pathForm').submit();
        });
    })
</script>
@endpush
