@extends('layouts.app')

@push('plugin-styles')
<style>
    .submit {
     background: #0071f3;
     color: #fff;
    }

    .issue {
        color: #ff3366;
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
 </style>
@endpush

@section('content')
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">  
      <div class="card">
          <div class="card-body">
            <h6 class="card-title">Payment Settings</h6>
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
            <br>

            <div class="">
                <ul  class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                
                  <li class="nav-item text-center" style="width: 50%;">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">Mpesa</a>
                  </li>
                  <li class="nav-item text-center" style="width: 50%;">
                    <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">Stripe</a>
                  </li>
                  
                  
                </ul>
                <div class="tab-content mt-3" id="lineTabContent">
                  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @include('payments.includes.mpesa')
                  </div>
                  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    @include('payments.includes.stripe')
                  </div>
                  
                  
                </div>
            </div>
          </div>
      </div>
      

    </div>
</div>


@endsection
