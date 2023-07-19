@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/photoswipe.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/photoswipe-lightbox.umd.min.js') }}"></script>
    <link href="{{ asset('css/photoswipe.css') }}" rel="stylesheet" />
    <style>
        .show-student:hover {
            cursor: pointer;
        }

        .span-delete {
            margin-right: 15px;
        }
    </style>
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Parents</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="width: 15%">
        <a href="{{route('parents.create')}}"><button type="button" class="btn btn-primary" style="width: 100%"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Parent</button></a>
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

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Parents Table</h6>
                <div class="table-responsive">
                    
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Phone No</th>
                                <th>Email</th>
                                <th>ID No</th>
                                <th>Children</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($parents as $parent)
                                <tr>
                                    <td>{{ $number }}</td>
                                    <?php $number++; ?>
                                    <td>
                                        @if ($parent->image)
                                        <div class="test-gallery">
                                            <a href="{{ asset('store/'.$parent->image) }}" data-pswp-width="600" data-pswp-height="600">
                                                <img class="wd-80 ht-80 rounded-circle" src="{{ asset('store/'.$parent->image) }}" alt="">
                                            </a>
                                        </div>
                                        @else
                                            @if ($parent->gender == "male")
                                                <img class="wd-80 ht-80 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9875/9875255.png')}}" alt="staff">
                                            @else
                                            <img class="wd-80 ht-80 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9875/9875392.png')}}" alt="staff">
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $parent->name }}</td>
                                    <td>{{ $parent->phone_num }}</td>
                                    <td>{{ $parent->email }}</td>
                                    <td >{{ $parent->id_num ?? 'n/a' }}</td>
                                    @php
                                        $child = App\Models\Student::where('parent_id','=', $parent->id)->get();
                                        if ($parent->user_type == 'parent two') {
                                            $child = App\Models\Student::where('parent_id','=', $parent->linked_to)->get();
                                        }
                                        $num = count($child);
                                        $using_transport = false;
                                        foreach ($child as $key => $chl) {
                                            if ($chl->transport == 1) {
                                                $using_transport = true;
                                            }
                                        }
                                    @endphp
                                    <td class="show-student" data-bs-toggle="modal" data-bs-target="#students{{$parent->id}}">{{ $num }}</td>
                                    <td>
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
                                            @if($parent->status && $using_transport)
                                            <a href="{{route('send_app_links', Crypt::encrypt($parent->id))}}" class="span-delete" >
                                                <i class="fa-solid fa-share-nodes" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send app link" title="Send app link" style="font-size: 16px;"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('parents.edit', Crypt::encrypt($parent->id)) }}" class="span-delete" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit parent details" title="Edit parent details">
                                                <i class="fa fa-pencil icon" aria-hidden="true" style="color: rgb(2, 167, 2);"></i>
                                            </a>
                                        @endif

                                       

                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                            @if($parent->status === 1) 
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#del{{$parent->id}}" class="span-delete">
                                                <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px;"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate parent"></i>
                                            </a>
                                            @else
                                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$parent->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate parent" ></i>
                                            </a>
                                            @endif
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                        
                    </table>
                </div>
                <div class="text-center">
                    
                </div>
            </div>
        </div>
    </div>
</div>
                    

<!-- Modal -->
@foreach ($parents as $parent)
<div class="modal fade" id="students{{$parent->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        @php
            $children = App\Models\Student::where('parent_id','=', $parent->id)->get();
            if ($parent->user_type == 'parent two') {
                $children = App\Models\Student::where('parent_id','=', $parent->linked_to)->get();
            }
            $count = count($children);
        @endphp
        <div class="modal-content">
        <div class="modal-header">
            @if ($count > 1)
            <h5 class="modal-title" id="exampleModalLabel">Children Information</h5>
            @else
            <h5 class="modal-title" id="exampleModalLabel">Child Information</h5>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            
            @foreach ($children as $child)
            @php
            $grade = 
                $stream = App\Models\Stream::where('id','=',$child->stream)->first();

                $teacher = App\Models\User::where('id','=',$stream->class_teacher)->first();
            @endphp
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$child->first_name}} {{$child->last_name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Adm No:</span> <span>{{$child->add_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">{{$tr->grade_class ?? 'Grade'}}:</span> <span>{{DB::table('student_classes')->where('id','=',$child->grade)->first()->name }}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Stream:</span> <span>{{$stream->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Teacher:</span> <span>{{$teacher->name ?? 'n/a'}}</span>
                </li>
            </ul>

            <br>
            @endforeach
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>
@endforeach


 <!-- Modal -->
 @foreach ($parents as $parent)
 <div class="modal fade" id="del{{$parent->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('parents.destroy', $parent->id) }}" method="post" >
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-danger" style="margin-right: 10px;"></i>
                     Deactivate {{$parent->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>{{$parent->name}} will be inactive. Are you sure?</p>
                
                 @csrf
                 @method('DELETE')
                 <input type="hidden" name="parent_id" value="{{ $parent->id }}">
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

 @foreach ($parents as $parent)
 <div class="modal fade" id="activate{{$parent->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('parent_activate')}}" method="post" >
             <div class="modal-header">
                 <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 10px;"></i>
                     Activate parent {{$parent->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Warning! Parent will be activated. Are you sure?</p>
                 
                 @csrf
                 <input type="hidden" name="parent_id" value="{{ $parent->id }}">
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
        $( function() {
            $( document ).tooltip();
        });

       
        var lightbox = new PhotoSwipeLightbox({
            gallery: '.test-gallery',
            children: 'a',
            // dynamic import is not supported in UMD version
            pswpModule: PhotoSwipe 
        });
        lightbox.init();
    </script>
@endpush