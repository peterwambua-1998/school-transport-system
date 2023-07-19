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
            <h6 class="card-title">Notification Settings</h6>
            

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

            <form action="{{ route('notification-settings.store') }}" id="my-form" method="post">
              @csrf
              <div class="table-responsive">
                  <table class="table table-striped" id="dataTableExample2">
                      <thead>
                          <tr>   
                              <th>Notification Type</th>
                              <th>1st Notification</th>
                              <th>2nd Notification</th>
                              <th>Unit Of Measure</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td>Insurance</td>
                              <td><input required type="number" name="insurance_send_at" class="form-control inputs"  placeholder="0" value="{{$notificationSetting->insurance_send_at ?? ''}}"><p class="issues"></p></td>
                              <td><input required type="number" name="insurance_send_at_two" class="form-control inputs"  placeholder="0" value="{{$notificationSetting->insurance_send_at_two ?? ''}}"><p class="issues"></p></td>
                              <td><input required type="text" name="insurance_unit" class="form-control inputs"  placeholder="Years" value="{{$notificationSetting->insurance_unit ?? ''}}"><p class="issues"></p></td>
                          </tr>
                          <tr>
                              <td>Driver License</td>
                              <td><input required type="number" name="dl_send_at" class="form-control inputs"  placeholder="0" value="{{$notificationSetting->dl_send_at ?? ''}}"><p class="issues"></p></td>
                              <td><input required type="number" name="dl_send_at_two" class="form-control inputs"  placeholder="0" value="{{$notificationSetting->dl_send_at_two ?? ''}}"><p class="issues"></p></td>
                              <td><input required type="text" name="license_unit" class="form-control inputs"  placeholder="Years" value="{{$notificationSetting->license_unit ?? ''}}"><p class="issues"></p></td>
                          </tr>
                          <tr>
                              <td>Inspection</td>
                              <td><input required type="number" name="inspection_send_at" class="form-control inputs"  placeholder="0" value="{{$notificationSetting->inspection_send_at ?? ''}}"><p class="issues"></p></td>
                              <td><input required type="number" name="inspection_send_at_two" class="form-control inputs"  placeholder="0" value="{{$notificationSetting->inspection_send_at_two ?? ''}}"><p class="issues"></p></td>
                              <td><input required type="text" name="inspection_unit" class="form-control inputs"  placeholder="Years" value="{{$notificationSetting->inspection_unit ?? ''}}"><p class="issues"></p></td>
                          </tr>
                      </tbody>
                  </table>
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
            $('#submit-btn').on('click', (ev) => {
                let is_empty;

                $('.inputs').each((i, e) => {
                    if (!$(e).val()) {
                        ev.preventDefault();
                        $(e).focus();
                        $(e).next().text('field required');
                        is_empty = true;
                    } else {
                        $(e).next().text('');
                    }
                });
                if (is_empty) {
                    return;
                }

                $('#my-form').submit();
            });

           
            
        })
    </script>
@endpush
