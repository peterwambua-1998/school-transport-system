@extends('layouts.app')

@push('plugin-styles')
<style>
    .submit {
     background: #0071f3;
     color: #fff;
    }
 
    .submit:hover {
     background: #014fa8;
    }
 
    .table-responsive {
     overflow: hidden;
    }
    .map-wrapper {
         height: 60vh;
         margin-bottom: 30px;
    }
 
    #map {
     height: 100%;
    }
 
    #pac-input {
     background-color: #fff;
     font-family: "Roboto", Helvetica, sans-serif;
     font-size: 15px;
     font-weight: 400;
     margin-left: 12px;
     padding: 0 11px 0 13px;
     text-overflow: ellipsis;
     width: 400px;
     }
 
     #pac-input:focus {
     border-color: #4d90fe;
     }
 
     .label-marker {
         position: absolute;
         top: 0;
         left: -40px;
         background: #FEDB00;
         padding: 3px;
         border-radius: 0.125rem;
       }
 
       .controls {
             position: absolute;
             margin-top: 10px;
             left: 35vw;
             background-color: #fff;
             border-radius: 2px;
             border: 1px solid transparent;
             box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
             box-sizing: border-box;
             font-family: Roboto;
             font-size: 15px;
             font-weight: 300;
             height: 40px;
             margin-left: 17px;
             
             outline: none;
             padding: 0 11px 0 13px;
             z-index: 10;
             width: 400px;
             
         }
 
         .controls:focus {
             border-color: #4d90fe;
         }

         .issues {
        color: #ff3366;
    }
 </style>
@endpush

@section('content')
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">  
      <div class="card">
          <div class="card-body">
            <h6 class="card-title">Sms Settings</h6>
            

            <hr>

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
              
            <form action="{{route('sms_settings_save')}}" method="post" id="my-form">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="" class="form-label">Username</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" placeholder="Username" value="{{$sms->user_name ?? ''}}">
                        <span class="text-danger" id="user_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="" class="form-label">Api key</label>
                        <input type="text" name="api_key" id="api_key" class="form-control" placeholder="api key" value="{{$sms->api_key ?? ''}}">
                        <span class="text-danger" id="api_key_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="" class="form-label">Short Code or Sender ID</label>
                        <input type="text" name="short_code" id="short_code" class="form-control" placeholder="Short Code or Sender ID" value="{{$sms->short_code ?? ''}}">
                        <span class="text-danger" id="short_code_error"></span>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
                </div>
            </form>
           
          </div>
      </div>
  </div>
</div>


@endsection

@push('custom-scripts')
    <script defer>
        $(function() {
            $('#submit-btn').on('click', (e) => {
                if(!$('#user_name').val()){
                    $('#user_name').focus();
                    $('#user_name_error').text('field required');
                    e.preventDefault();
                    return;
                } else {
                    $('#user_name_error').text('');
                }

                if(!$('#api_key').val()){
                    $('#api_key').focus();
                    $('#api_key_error').text('field required');
                    e.preventDefault();
                    return;
                } else {
                    $('#api_key_error').text('');
                }

                if(!$('#short_code').val()){
                    $('#short_code').focus();
                    $('#short_code_error').text('field required');
                    e.preventDefault();
                    return;
                } else {
                    $('#short_code_error').text('');
                }

                $('#my-form').submit();
            });
        })
    </script>
@endpush
