@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/photoswipe.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/photoswipe-lightbox.umd.min.js') }}"></script>
    <link href="{{ asset('css/photoswipe.css') }}" rel="stylesheet" />

    <style>
        .pick-up:hover {
            cursor: pointer;
        }
        .define-transport {
            color:crimson;
        }
        .span-delete {
            margin-left: 5px;
            margin-right: 10px;
        }
    </style>
  @endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">Students</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
</nav>

<div class="page-breadcrumb" style="display:grid;grid-template-columns:1fr 1fr;">
    <div style="display:flex; gap:5px;">
        
        <div class="p-2 " style="display:flex; gap: 10px; background: #fff;border-radius: 0.375rem; border:1px solid #e2e8f0;">
            <span>Students</span> <span class="bg-success px-2" style="border-radius: 0.175rem;">{{count($students)}}</span>
        </div>
        
        <div class="p-2 " style="display:flex; gap: 10px; background: #fff;border-radius: 0.375rem;border:1px solid #e2e8f0;">
            <span>Unallocated </span> <span class="px-2 @if($unallocatedCount > 0) bg-warning @else bg-success @endif" style="border-radius: 0.175rem;">{{$unallocatedCount}}</span>
        </div>
        
    </div>
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="width: 100%;display:flex;flex-direction:row-reverse;">
        <div>
            <a class="btn btn-primary" style="width: 100%;" href="{{route('students.create')}}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Student</a>
        </div>
    </div>
    @endif
</div>

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
            <h6 class="card-title">Student Table</h6>
            <p class="text-muted mb-3"></p> 
    
            <div class="table-responsive">
                
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Admission</th>
                            <th>{{$tr->grade_class ?? 'Grade'}}</th>
                            <th>Transport</th>
                            <th>Pick-up</th>
                            <th>Actions</th>
                            <th style="display: none">Allocated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($students as $student)
                        <tr>
                            <td>{{ $number }}</td>
                            <?php $number++; ?>
                            <td>
                                @if ($student->image)
                                <div class="test-gallery">
                                    <a href="{{ asset('store/'.$student->image) }}" data-pswp-width="600" data-pswp-height="600">
                                        <img class="wd-80 ht-80 rounded-circle" src="{{ asset('store/'.$student->image) }}" alt="">
                                    </a> 
                                </div>
                                @else
                                    @if ($student->gender == "male")
                                        <img class="wd-80 ht-80 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/3135/3135755.png')}}" alt="staff">
                                    @else
                                    <img class="wd-80 ht-80 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9676/9676572.png')}}" alt="staff">
                                    @endif
                                @endif
                            </td>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="text-center">{{ $student->add_num }}</td>
                            <td>
                                <?php $grade = DB::table('student_classes')->where('id','=', $student->grade)->first(); ?>
                                {{$grade->name}}
                            </td>
                            <th>
                                @if ($student->transport !== null)
                                    @if ($student->transport == 0)
                                        <span class="badge bg-success">Own</span>
                                    @endif
                                    @if ($student->transport == 1)
                                        <span class="badge bg-success">School</span>
                                    @endif
                                @else
                                <span class="badge bg-danger">Pending</span>
                                @endif
                            </th>
                            
                            <td class="text-center">
                                @if ($student->pick_up)
                                <span onclick="pickUp({{ $student->id }}, 0)"><i class="fa fa-check-circle pick-up" aria-hidden="true" style="color: green; font-size: 15px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Student is picked up by bus"></i></span>
                                @else
                                <span onclick="pickUp({{ $student->id }}, 1)"><i class="fa fa-times-circle pick-up" aria-hidden="true" style="color: red; font-size: 15px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Student is not picked up by bus"></i></span>
                                @endif
                            </td>
                            <td>
                                @if ($student->status == 1)
                                    
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
                                    @php
                                        $student_trip = App\Models\SAndT::where('student_id','=', $student->id)->get();
                                    @endphp
                                    <a href="{{route('students.show', Crypt::encrypt($student->id))}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details" title="View more details" >
                                        <i class="fa-solid fa-eye text-info"></i>
                                    </a>

                                    <a href="#" class="span-delete" data-bs-toggle="modal" data-bs-target="#trip-definition{{$student->id}}">
                                        <i class="fas fa-road define-transport" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Define transport"></i>
                                    </a>
                                    @if ($student->transport == 1)
                                    
                                    {{-- for both students --}}
                                    @if ($student->trip_type)
                                        @if ($student->trip_type == 3)
                                            @if (count($student_trip) > 1)
                                            <a href="{{route('allocation_create', $student->id)}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Allocate bus" title="Allocate bus" >
                                                <i class="fa-solid fa-bus text-success"></i>
                                            </a>
                                            @else
                                            <a href="{{route('allocation_create', $student->id)}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Allocate drop off and pickup bus" title="Allocate drop off and pickup bus">
                                                <i class="fa-solid fa-bus text-warning"></i>
                                            </a>
                                            @endif
                                        @endif
                                    @endif
                                    {{-- for drop-off students --}}
                                    @if ($student->trip_type)
                                        @if ($student->trip_type == 2)
                                            @if (count($student_trip) > 0)
                                            <a href="{{route('allocation_create_dropoff', $student->id)}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Reallocate drop-off one way" title="Reallocate drop-off one way">
                                                <i class="fa-solid fa-bus text-success"></i>
                                            </a>
                                            @else
                                            <a href="{{route('allocation_create_dropoff', $student->id)}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Allocate drop-off bus" title="Allocate drop-off bus one way">
                                                <i class="fa-solid fa-bus text-warning"></i>
                                            </a>
                                            @endif
                                        @endif
                                    @endif
                                    {{-- for pickup students --}}
                                    @if ($student->trip_type)
                                        @if ($student->trip_type == 1)
                                            @if (count($student_trip) > 0)
                                                <a href="{{route('allocation_create', $student->id)}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Reallocate pickup one way" title="Reallocate pickup one way">
                                                    <i class="fa-solid fa-bus text-success"></i>
                                                </a>
                                            @else
                                                <a href="{{route('allocation_create', $student->id)}}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Allocate pickup bus" title="allocate pickup bus one way">
                                                    <i class="fa-solid fa-bus text-warning"></i>
                                                </a>
                                            @endif
                                        @endif
                                    @endif
                                    @endif
                                @endif

                                @endif
                            
                                <a href="{{ route('students.edit', Crypt::encrypt($student->id)) }}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit student details" title="Edit student details">
                                    <i class="fa fa-pencil icon text-success" aria-hidden="true"></i>
                                </a>

                                @if ($student->status == 0)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#activate{{$student->id}}">
                                        <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate student"></i>
                                    </a>
                                @endif

                                @if ($student->status == 1)
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#del{{$student->id}}" class="span-delete">
                                            <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate student" title="Deactivate student"></i>
                                        </a>
                                    @endif
                                @endif

                            </td>
                            <td style="display: none">{{$student->bus_assigned}}</td>
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
@foreach ($students as $student)
<div class="modal fade" id="trip-definition{{$student->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Define {{$student->first_name}} {{$student->last_name}} Transport</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('saveTripDefinition', Crypt::encrypt($student->id))}}" class="allocation-form" method="post">
                @csrf
            <div class="row">
                <div class="col-md-12 mb-3">
                    
                </div>

                <div class="col-md-12 mb-3">
                    <label for="" class="form-label">Transport Type</label>
                    <select id="owntrans" name="own_trans" class="form-select owntrans">
                        <option>select...</option>
                        <option @if ($student->transport === 0)
                            selected
                        @endif value="own">Own</option>
                        <option @if ($student->transport === 1)
                            selected
                        @endif value="school">School</option>
                    </select>
                    <span class="text-danger"></span>
                </div>

                <div class="col-md-12 mb-3 transport_type" id="transport_type">
                    <label for="" class="form-label">Trip Type</label>
                    <select name="transport_type" id="" class="form-select tr_type">
                        <option>select...</option>
                        <option @if ($student->trip_type == 1)
                            selected
                        @endif value="1">Pickup</option>
                        <option @if ($student->trip_type == 2)
                            selected
                        @endif value="2">Drop-off</option>
                        <option @if ($student->trip_type == 3)
                            selected
                        @endif value="3">Both</option>
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            </form>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success allocation-type">Save changes</button>
        </div>
      </div>
    </div>
</div>
@endforeach

 <!-- Modal -->
@foreach ($students as $student)
<div class="modal fade" id="del{{$student->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('students.destroy', $student->id) }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-danger" style="margin-right: 10px;"></i>
                    Deactivate student {{$student->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warning student will be deactivated. Are you sure?</p>
               
                @csrf
                @method('DELETE')
                <input type="hidden" name="student_id" value="{{ $student->id }}">
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

@foreach ($students as $student)
<div class="modal fade" id="activate{{$student->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('student_activate')}}" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="far fa-lightbulb text-success" style="margin-right: 10px;"></i>
                        Activate {{$student->first_name}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure?</p>
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

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
    <script defer>
        $( function() {

            $('.transport_type').each((e, i) => {
                $(i).hide();
            });

            $('.owntrans').each((e, i) => {
                $(i).on('change', function() {
                    if ($(i).find(':selected').val() == "school") {
                        $(i).parent().next().show('slow');
                    } else {
                        $(i).parent().next().hide('slow');
                    }
                })
            });

            $('.owntrans').each((e, i) => {
                if ($(i).find(':selected').val() == "school") {
                    $(i).parent().next().show('slow');
                }
            });
            
            let mytable = $('#dataTableExample').DataTable({
                "aLengthMenu": [
                    [10, 30, 50, -1],
                    [10, 30, 50, "All"]
                ],
                "iDisplayLength": 10,
                "language": {
                    search: ""
                }
            });
            $('#dataTableExample').each(function() {
                var datatable = $(this);
                // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Search');
                search_input.removeClass('form-control-sm');
                // LENGTH - Inline-Form control
                var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
            });


            const url = new URL(window.location.href);

            if (url.searchParams.has('unallocated')) {
                mytable.column( 8 ).search( 0 ).draw();
            } 


            $('.allocation-type').each((i, e) => {
                $(e).on('click', (ev) => {
                    let all_form = $(e).parent().prev().find('.allocation-form');
                    let own_trans = $(e).parent().prev().find('.owntrans');
                    let tr_type = $(e).parent().prev().find('.tr_type');

                    console.log(all_form, own_trans, tr_type.next());

                    if (own_trans.find(':selected').text() == 'select...') {
                        own_trans.next().text('field required');
                        return false;
                    } else {
                        own_trans.next().text('');
                    }

                    if (own_trans.find(':selected').val() == 'school') {
                        if (tr_type.find(':selected').text() == 'select...') {
                            tr_type.next().text('field required');
                            return false;
                        } else {
                            tr_type.next().text('');
                        }
                       
                    }

                    all_form.submit();
                });
            });
        });



        function pickUp(student_id, pickup_value) {

            var msg = '';

            if (pickup_value == 0) {
                msg = 'Student will not be picked up by bus';
            } else {
                msg = 'Student will be picked up by bus';
            }

            Swal.fire({
                title: 'Are you sure?',
                text: msg,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
            
            }).then((result) => {

                var dataPick = new FormData;
                dataPick.append('_token', '{{ csrf_token() }}');
                dataPick.append('pickup', pickup_value);

                var url = '/change-pickup/' + student_id;
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: dataPick,
                        error: function (err) {
                            console.log(err)
                        },
                        success: function (response) {
                            console.log(response);
                            
                            if (response) {
                                location.reload();
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'system error please try again',
                                    showConfirmButton: false,
                                    timer: 2000,
                        
                                });
                            }
                            

                        }
                    })
                }
            })

            


            
        }
       
        var lightbox = new PhotoSwipeLightbox({
            gallery: '.test-gallery',
            children: 'a',
            // dynamic import is not supported in UMD version
            pswpModule: PhotoSwipe 
        });
        lightbox.init();
    </script>
@endpush
