@push('plugin-styles')
    <style>
        .showsss:hover {
            cursor: pointer;
            color: darkgreen;
        }
        .showsss {
            color: green;
        }
    </style>
@endpush


<div class="table-responsive">
    <h5 class="card-title">Incidents caused by attedants</h5>
    <table class="table table-bordered table-striped"  id="dataTableExample2" style="width: 100%" data-ordering="false">
        <thead>
            <tr>
                <th>#</th>
                <th>Attendant</th>
                <th>bus reg no</th>
                <th>source</th>
                <th>trip</th>
                <th>date</th>
                <th>type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($incidentAts as $incidentAt)
                
                <tr>
                    <td>{{ $number }}</td>
                    <?php $number++; ?>
                    <td>{{App\Models\User::where('id','=',$incidentAt->user_assulter)->first()->name}}</td>
                    <td>{{App\Models\Vehicle::where('id','=',$incidentAt->vehicle_id)->first()->plate_num}}</td>
                    <td>{{$incidentAt->source}}</td>
                    <td>{{App\Models\Trip::where('id','=',$incidentAt->trip)->first()->title}}</td>
                    <td>{{$incidentAt->date}}</td>
                    <td>{{$incidentAt->type}}</td>
                    
                    <td>
                        <?php $incident_ims = App\Models\IncidentImages::where('incident_id','=', $incidentAt->id)->get();  ?>
                        @if ($incident_ims->isNotEmpty())
                        <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Show images">
                            <i class="fa-solid fa-image showsss" data-ids="{{$incidentAt->id}}" style="margin-right: 10px;"></i>
                        </a>
                        @endif

                        @if ($incidentAt->video)
                        <a href="#"  data-bs-toggle="modal" data-bs-target="#video{{$incidentAt->id}}">
                            <i class="fa-solid fa-video text-warning" style="font-size: 16px; margin-right:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Show video"></i> 
                        </a>
                            
                        @endif
                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                        <a href="#" data-bs-toggle="modal" data-bs-target="#incidentAt{{$incidentAt->id}}">
                            <i class="fa-solid fa-eye text-info"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i>
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            
        </tbody>
    </table>
</div>

{{-- video modal --}}
@foreach ($incidentAts as $incidentAt)
<div class="modal fade" id="video{{$incidentAt->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Video Incident Caused By {{App\Models\User::where('id','=',$incidentAt->user_assulter)->first()->name}}  </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @if ($incidentAt->video)
                <video controls style="width: 100%">
                    <source type="video/mp4" src="{{asset('store/'.$incidentAt->video)}}">
                </video>
            @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endforeach


<!-- modal -->
@foreach ($incidentAts as $incidentAt)
<div class="modal fade" id="incidentAt{{$incidentAt->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Incident </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                    
                <li class="list-group-item">
                    <?php $st= App\Models\User::where('id','=',$incidentAt->user_assulter)->first() ?>
                    <span class="ml-5 text-muted">Name:</span> <span>{{$st->name}}</span>
                </li>
                
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Bus Reg. No:</span> <span>{{App\Models\Vehicle::where('id','=',$incidentAt->vehicle_id)->first()->plate_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Source:</span> <span>{{$incidentAt->source}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Trip:</span> <span>{{App\Models\Trip::where('id','=',$incidentAt->trip)->first()->title}} {{App\Models\Trip::where('id','=',$incidentAt->trip)->first()->time}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Reported By:</span> <span>{{App\Models\User::where('id','=',$incidentAt->user_id)->first()->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Description:</span> <span>{{$incidentAt->description}}</span>
                </li>
            </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endforeach

@push('custom-scripts')
    <script defer>
        $(function() {
            var nodeList = $('.showsss');
            nodeList.each(function(index) {
                $(this).on('click', function(){
                    let el = $(this).attr('data-ids') - 0;
                    console.log(el);

                
                    $.ajax({
                        type: "get",
                        url: `/incident/images/${el}`,
                        processData: false,
                        cache: false,
                        contentType: false,
                        error: function(err) {
                            console.log(err);
                        },
                        success: function(response) {
                            let its = [];
                            for (let t = 0; t < response.length; t++) {
                                let im = {
                                    src: `{{asset('store/${response[t].path}')}}`,
                                    width: 1500,
                                    height: 1000,
                                    alt: 'test image '.t,
                                };      
                                its.push(im);                        
                            }
                            const options = {
                                dataSource: its,
                                showHideAnimationType: 'none'
                            }

                            options.index = 0;
                            const pswp = new PhotoSwipe(options);
                            pswp.init();
                        }
                    })
                
                })
            })
            
        
        })
    </script>
@endpush