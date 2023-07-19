@extends('layouts.app')

@push('plugin-styles')
    <script src="{{ asset('js/intlTelInput.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
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
      <li class="breadcrumb-item"><a href="{{route('zones.index')}}">Zone</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('zones.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
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
                <h4 class="card-title">Edit Zone</h4>
                <hr>
                <form action="{{ route('zones.update', $zone->id) }}" id="my-form" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Title" value="{{ old('name', $zone->name) }}" required>
                            <span class="issue" id="title-error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Description</label>
                            <input type="text" name="description" class="form-control" id="desc" placeholder="Description" value="{{ old('description', $zone->description) }}" required>
                            <span class="issue" id="desc-error"></span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Two-Way Price {{ $settings->currency ?? 'USD' }}</label>
                            <input type="number" name="price" class="form-control" id="two-way" placeholder="Enter Price" value="{{ old('price', $zone->price) }}" required>
                            <span class="issue" id="two-way-error"></span>
                        
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="oneway_price">One-Way Price {{ $settings->currency ?? "USD" }}</label>
                            <input type="number" name="oneway_price" class="form-control" id="oneway_price" placeholder="0" required value="{{$zone->oneway_price}}">
                            <span class="issue" id="one-way-error"></span>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-success mt-3" id="my-submit" type="button"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Changes</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom-scripts')
    <script defer>
        $(function () {
            $('#my-submit').on('click',(e) => {

                if (!$('#title').val()) {
                    $('#title-error').text('field required');
                    e.preventDefault();
                    $('#title').focus();
                        return;
                }else {
                    $('#title-error').text('');
                }


                if (!$('#desc').val()) {
                    $('#desc-error').text('field required');
                    $('#desc').focus()
                        return;
                }else {
                    $('#desc-error').text('');
                }

                if (!$('#two-way').val()) {
                    $('#two-way-error').text('field required');
                    $('#two-way').focus();
                        return;
                }else {
                    $('#two-way-error').text('');
                }

                if (!$('#oneway_price').val()) {
                    $('#one-way-error').text('field required');
                    e.preventDefault();
                    $('#oneway_price').focus()
                        return;
                }else {
                    $('#one-way-error').text('');
                }

                

                $('#my-form').submit();
            });
        })
    </script>
@endpush
