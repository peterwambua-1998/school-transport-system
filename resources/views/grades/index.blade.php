@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:grid; grid-template-columns: 1fr 1fr;">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{ucfirst($tr->grade_class) ?? 'Grades'}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
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
                <h6 class="card-title">Grades</h6>
                <div class="">
                    <ul  class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                      <li class="nav-item text-center" style="width: 33.33%;">
                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">Group</a>
                      </li>
                      <li class="nav-item text-center" style="width: 33.33%;">
                        <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">{{ucfirst($tr->plural) ?? 'Grades'}}</a>
                      </li>
                      <li class="nav-item text-center" style="width: 33.33%;">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">Streams</a>
                      </li>
                    </ul>
                    <div class="tab-content mt-3" id="lineTabContent">
                      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @include('grades.includes.group')
                      </div>
                      <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        @include('grades.includes.grades')
                      </div>
                      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        @include('grades.includes.streams')
                      </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

@foreach ($groups as $group)
<div class="modal fade" id="del{{$group->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{route('deleteGroup', Crypt::encrypt($group->id))}}" method="post">
           @csrf
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                  <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    Deactivate {{$group->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure?</p>
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
 @foreach ($grades as $grade)
 <div class="modal fade" id="dele{{$grade->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{route('deleteGrade', Crypt::encrypt($grade->id))}}" method="post">
            @csrf
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                  <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    
                     
                  Deactivate {{$grade->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Are you sure?</p>
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
 @foreach ($streams as $stream)
 <div class="modal fade" id="stream{{$stream->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{route('deleteStream', Crypt::encrypt($stream->id))}}" method="post">
            @csrf
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                  <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                     
                  Deactivate {{$stream->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Are you sure?</p>
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
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
        
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
        } );

    </script>
@endpush