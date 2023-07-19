@push('plugin-styles')
    <style>
        .shows-routine:hover {
            cursor: pointer;
            color: darkgreen;
        }
        .shows-routine {
            font-size: 16px;
            color: green;
            margin-right: 10px;
        }
    </style>
@endpush

<div class="table-responsive">
                
    <table class="table table-bordered table-striped" id="dataTableExample3">
        <thead>
            <tr>
                <th>#</th>
                <th>Bus Reg. No</th>
                <th>Location</th>
                <th>Last Service</th>
                <th>Next Service</th>
                <th>Date Created</th>
                <th>Date Recorded</th>
                <th>Photos</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($routines as $key => $daily)
            <tr>
                <td>{{ $number }}</td>
                <?php $number++; ?>
                <td>{{$vehicle->plate_num}}</td>
                <td>{{$daily->place_name}}</td>
                <td>{{$daily->last_service}} Km</td>
                <td>{{$daily->next_service}} Km</td>
                <td>{{$daily->created_at->toFormattedDateString()}}</td>
                <td>
                    @if ($daily->description)
                    {{$daily->updated_at->toFormattedDateString()}}
                    @endif
                </td>
                <td @if($daily->description && $daily->images->isNotEmpty() && $daily->video) style="display:flex; gap: 20px;" @endif>
                    @if ($daily->description)
                    <a href="#" class="span-delete" data-bs-toggle="modal" data-bs-target="#routine{{$daily->id}}">
                        <span class=""><i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i></span>
                    </a>
                    @endif


                    @if ($daily->images->isNotEmpty())
                    <a href="#">
                        <i class="fa-solid fa-image shows-routine" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="maintenance images"  data-ids="{{$daily->id}}"></i>
                    </a>
                    @endif

                    @if ($daily->video)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#video{{$daily->id}}">
                        <i class="fa-solid fa-video text-warning" style="font-size: 16px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="maintenance video"></i> 
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach ($routines as $daily)
<div class="modal fade" id="routine{{$daily->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Routine Maintenance For {{$vehicle->plate_num}}  </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Description:</span> <span>{{$daily->description}}</span>
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

{{-- video modal --}}
@foreach ($routines as $daily)
<div class="modal fade" id="video{{$daily->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Video Routine Maintenance For {{$vehicle->plate_num}}  </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
        @if ($daily->video)
          <video controls class="my-video" style="width: 100%; height: auto">
            <source type="video/mp4" src="{{asset('store/'.$daily->video)}}">
          </video>
        @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning modal-video" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endforeach

@push('custom-scripts')
    <script defer>
        
        $(function() {
            var nodeList = $('.shows-routine');
            nodeList.each(function(index, e) {
                $(e).on('click', function(){
                    let el = $(this).attr('data-ids') - 0;
                    console.log(el);

                
                    $.ajax({
                        type: "get",
                        url: `/mainte-image/${el}`,
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
            });

            //pause media on modal close
            let myNodeList = document.querySelectorAll('.modal-video');

            for (let i = 0; i < myNodeList.length; i++) {
                let item = myNodeList[i];
                item.addEventListener('click',(e) => {
                    let parent_el = item.parentNode.previousElementSibling;
                    let media = parent_el.querySelector('.my-video');
                    if (! media.paused) {
                        media.pause();
                    }
                })
            }
            /*
            $('.modal-video').each((i, e) => {
                $(e).on('click', () => {
                    let media = $(e).parent().prev().find('video');
                    console.log(media.paused);
                })
            });
            */
        })
    </script>
@endpush