@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .my-success {
            display: none;
        }
        .span-delete {
            margin-right: 15px;
        }
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:grid;grid-template-columns:1fr 1fr;">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">School Fees</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="display:flex;flex-direction:row-reverse">
        <a class="btn btn-primary" href="{{route('create_school_fees')}}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Fee</a>
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
                <h6 class="card-title">School Fees Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Grade</th>
                                <th>Term</th>
                                <th>Year</th>
                                <th>Fee ({{$settings->currency}})</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($schoolFees as $fee)
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++ ?>
                                <td>
                                    <?php $grade = DB::table('student_classes')->where('id','=', $fee->grade)->first(); ?>
                                    {{$grade->name}}
                                </td>
                                <td>
                                    {{App\Models\SchoolTermDate::where('id','=',$fee->term)->first()->name}}
                                </td>
                                <td>{{date('Y')}}</td>
                                <td>{{number_format($fee->amount, 2)}}</td>
                                <td>
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
                                    @if ($fee->active)
                                    <a href="{{route('school-fees.show', Crypt::encrypt($fee->id))}}" class="span-delete mr-4" >
                                        <span class=""><i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i></span>
                                    </a>
                                    @endif  
                                    

                                    @endif
                                    
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                    <a href="{{route('school-fees.edit', Crypt::encrypt($fee->id))}}" class="span-delete"><i class="fa fa-pencil text-success" aria-hidden="true"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit school fees details"></i></a>

                                    @if ($fee->active)
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#del{{$fee->id}}" class="span-delete" style="background: none; border: none">
                                        <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate school fees"></i>
                                    </button>
                                    @else
                                    <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$fee->id}}">
                                        <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate school fees" title="Activate school fees"></i>
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
 @foreach ($schoolFees as $fee)
 <div class="modal fade" id="del{{$fee->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('school-fees.destroy', $fee->id) }}" method="post" >
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                     
                     <?php $grade = DB::table('student_classes')->where('id','=', $fee->grade)->first(); ?>
                     Deactivate school fees for {{$grade->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                <p>Warning school fees will be inactive. Are you sure?</p>
                <p>If has payments you will not be able to deactivate</p>
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

@foreach ($schoolFees as $fee)
 <div class="modal fade" id="activate{{$fee->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('activate_schoolfees') }}" method="post" >
             <div class="modal-header">
                 <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                     <?php $grade = DB::table('student_classes')->where('id','=', $fee->grade)->first(); ?>
                     Activate school fees for {{$grade->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                    <p>Warning school fees will be active. Are you sure?</p>
                    <p>Payments can be made</p>
                    @csrf
                    <input type="hidden" name="fee_id" value="{{$fee->id}}">
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
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
    
    </script>
@endpush