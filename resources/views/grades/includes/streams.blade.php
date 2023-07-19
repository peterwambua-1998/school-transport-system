<div class="table-responsive">
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div class="mb-3" style="width: 100%; text-align:end">
        <a class="btn btn-primary"  href="{{ route('stream_view') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Stream</a>
    </div>
    @endif
    
    <table class="table table-bordered table-striped"  id="dataTableExample3" data-ordering="false">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ucfirst($tr->grade_class) ?? 'Grades'}}</th>
                <th>Stream</th>
                <th>Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($streams as $stream)
                <?php
                    $cl = DB::table('student_classes')->where('id','=', $stream->student_classes_id)->first();
                ?>
                <tr>
                    <td>{{ $number }}</td>
                    <?php $number++; ?>
                    <td>{{$cl->name}}</td>
                    <td>{{$stream->name}}</td>
                    <td>{{App\Models\User::where('id','=', $stream->class_teacher)->first()->name}}</td>
                    <td style="display:flex;gap:15px;">
                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                            <a href="{{route('streamEditPage', Crypt::encrypt($stream->id))}}">
                                <i class="fa fa-pencil icon" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit stream details"></i>
                            </a>

                            @if($stream->status)
                                <button type="button" data-bs-toggle="modal" data-bs-target="#stream{{$stream->id}}" class="span-delete" style="background: none; border: none" >
                                    <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate stream"></i>
                                </button>
                            @else
                                <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$stream->id}}">
                                    <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate stream" title="Activate stream"></i>
                                </a>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            
        </tbody>

        
    </table>
</div>

@foreach ($streams as $stream)
<div class="modal fade" id="activate{{$stream->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('activateStream') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Activate {{$stream->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Stream will be active. Are you sure?</p>
                
                @csrf
                <input type="hidden" name="stream_id" value="{{$stream->id}}">
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