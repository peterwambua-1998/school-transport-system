@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('term_events.create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Event</a>
    </div>
</nav>

<div id="append-alter"></div>

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
                <h6 class="card-title">Events Table</h6>
                <p class="text-muted mb-3"></p>   
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Location</th>
                                <th>Transport</th>
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
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                
                                <td>{{$term->name}}</td>
                                <td>
                                    {{date_format(date_create($term->start), 'd-M-Y')}}
                                </td>
                                <td>{{date_format(date_create($term->ends), 'd-M-Y')}}</td>
                                <td>{{date_format(date_create($term->start_time), 'h:i A')}}</td>
                                <td>{{date_format(date_create($term->end_time), 'h:i A')}}</td>
                                <td>{{$term->location}}</td>
                                <td>{{$term->transport}}</td>
                                <td style="display: grid;grid-template-columns:1fr 1fr 1fr; gap: 15px;">
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
                                        @if ($term->status)
                                        <a href="#" class="share" data-events="{{$term->id}}" onclick="sendMessage(this)">
                                            <i class="fa-brands fa-whatsapp" style="color:#25D366" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Share event details with parents"></i>
                                        </a> 
                                        @endif
                                    @endif

                                    

                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')

                                        <a href="{{ route('term_events.edit', Crypt::encrypt($term->id)) }}" class="span-delete">
                                            <span><i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit event details"></i></span>
                                        </a>
                                    
                                        @if ($term->status)
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#del{{$term->id}}" class="span-delete" style="background: none; border: none" >
                                                <span ><i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate event"></i></span>
                                            </button>
                                        @else
                                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$term->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate event" title="Activate event"></i>
                                            </a>
                                        @endif

                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Modal -->
 @foreach ($terms as $term)
 <div class="modal fade" id="del{{$term->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('term_events.destroy', $term->id) }}" method="post" style="display: inline-block">
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="fa-solid fa-trash" style="margin-right: 20px;"></i>
                     Deactivate event {{$term->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Are you sure?</p>
                 
                 @csrf
                 @method('DELETE')
             </div>
             <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Deactivate</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
             </div>
         </form>
       </div>
     </div>
 </div>
 @endforeach

  <!-- Modal -->
  @foreach ($terms as $term)
  <div class="modal fade" id="activate{{$term->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
         <form action="{{ route('activate_event') }}" method="post" style="display: inline-block">
              <div class="modal-header">
                  <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                      Activate event {{$term->name}}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
              </div>
              <div class="modal-body">
                  <p>Are you sure?</p>
                  @csrf
                  <input type="hidden" name="event_id" value="{{$term->id}}">
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
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
        $('#append-alter').hide();
        function sendMessage(ele)  {
            var data = ele.getAttribute('data-events') - 0;

            Swal.fire({
                title: 'Send Whatsapp Message',
                text: "All parents will receive news about the event.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#25D366',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: '/sms-whatsapp/' + data,
                        processData: false,
                        contentType: false,
                        cache: false,
                    
                        error: function (err) {
                            console.log(err)
                        },
                        success: function (response) {
                            $('#append-alter').children().remove();
                            if (response == 0) {
                               let template = `
                               <div class="alert alert-danger" role="alert" id="dangers">
                                    <p>Please add whatsapp settings, under settings, system settings</p>
                                </div>
                               `; 

                               $('#append-alter').append(template);
                               $('#append-alter').show('slow');
                               return;
                            }

                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: `${response}`,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            
                        }
                    });     
                }
            });
        };

        
    </script>
@endpush