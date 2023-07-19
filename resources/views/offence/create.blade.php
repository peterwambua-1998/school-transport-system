@extends('layouts.app')
@push('plugin-styles')
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
      <li class="breadcrumb-item"><a href="{{route('offence.index')}}">Offence</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('offence.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Add Offence</h4>
                <hr>
                <form action="{{ route('offence.store') }}" id="my-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label"for="title">Type</label>
                            <select class="form-select" name="type" id="type">
                                <option>select...</option>
                                <option value="driver">Driver</option>
                                <option value="attendant">Attendant</option>
                            </select>
                            <span class="issue" id="type_error"></span>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label"for="title">User</label>
                            <select class="form-select" name="user" id="user">
                                <option>select...</option>
                            </select>
                            <span class="issue" id="user_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12 col-sm-12">
                            <label class="form-label"for="offence_type">Offence Type</label>
                            <input type="text" name="offence_type" class="form-control" id="offence_type" required>
                            <span class="issue" id="offence_type_error"></span>
                        
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label"for="description">Description</label>
                            <textarea type="text" name="description" class="form-control" id="description" required></textarea>
                            <span class="issue" id="description_error"></span>
                        
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label"for="disciplinary_action">Disciplinary Action</label>
                            <input type="text" name="disciplinary_action" class="form-control" id="disciplinary_action" required>
                            <span class="issue" id="disciplinary_action_error"></span>
                        </div>
                    </div>


                    <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Offence</button>
                    </div>
        
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer>
    $(function() {
        $('#type').on('change',(e) => {
            let d = new FormData;
            d.append('_token','{{csrf_token()}}');
            d.append('type',e.target.value);
            $.ajax({
                type: 'POST',
                url: "{{route('offence_getuser')}}",
                processData: false,
                cache: false,
                contentType: false,
                data:d,
                error: function(err) {
                    console.log(err);
                },
                success: function(response) {
                    console.log(response);
                    $('#user').empty();
                    $('#user').append('<option>select...</option>');
                    for (let i = 0; i < response.length; i++) {
                        let template = ` 
                            <option value="${response[i].id}">${response[i].name}</option>
                        `;
                       
                        $('#user').append(template);
                    }
                    
                }
            });
        })

        $('#submit-btn').on('click',(e) => {
           

            if($('#type').find(':selected').text() == 'select...'){
                $('#type_error').text('field required');
                e.preventDefault();
                $('#type').focus();
                return;
            } else {
                $('#type_error').text('');
            }

            if($('#user').find(':selected').text() == 'select...'){
                $('#user_error').text('field required');
                e.preventDefault();
                $('#user').focus();
                return;
            } else {
                $('#user_error').text('');
            }

            if(!$('#offence_type').val()){
                $('#offence_type_error').text('field required');
                e.preventDefault();
                $('#offence_type').focus();
                return;
            } else {
                $('#offence_type_error').text('');
            }

            if(!$('#description').val()){
                $('#description_error').text('field required');
                e.preventDefault();
                $('#description').focus();
                return;
            } else {
                $('#description_error').text('');
            }

            if(!$('#disciplinary_action').val()){
                $('#disciplinary_action_error').text('field required');
                e.preventDefault();
                $('#disciplinary_action').focus();
                return;
            } else {
                $('#disciplinary_action_error').text('');
            }

            $('#my-form').submit();
        });
    });
</script>
@endpush