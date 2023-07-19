<div class="table-responsive">
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div class="mb-3" style="width: 100%; text-align:end">
        <a class="btn btn-primary"  href="{{ route('group_view') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Group</a>
    </div>
    @endif
    
    <table class="table table-bordered table-striped"  id="dataTableExample2" data-ordering="false">
        <thead>
            <tr>
                <th>#</th>
                <th>Group</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($groups as $group)
                
                <tr>
                    <td>{{ $number }}</td>
                    <?php $number++; ?>
                    <td>{{$group->name}}</td>
                    <td style="display:flex;gap:15px;">
                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                        <a href="{{route('editGroupPage', Crypt::encrypt($group->id))}}">
                            <i class="fa fa-pencil icon" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit group details"></i>
                        </a>

                            @if ($group->status)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#del{{$group->id}}" class="span-delete" style="background: none; border: none">
                                <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate group"></i>
                            </button>
                            
                            @else
                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$group->id}}">
                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate zone" title="Activate group"></i>
                            </a>
                            
                            @endif
                        
                        @endif
                    </td>
                </tr>
            @endforeach
            
        </tbody>

        
    </table>
</div>


<!-- Modal -->
@foreach ($groups as $group)
 <div class="modal fade" id="activate{{$group->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('activateGroup') }}" method="post" style="display: inline-block">
             <div class="modal-header">
                 <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                     
                     Activate group {{$group->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Group will be active. Are you sure?</p>
                 
                 @csrf
                 <input type="hidden" name="group_id" value="{{$group->id}}">
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
