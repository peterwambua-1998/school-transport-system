@push('plugin-styles')
    <style>
        .showsss:hover {
            cursor: pointer;
            color: darkgreen;
        }
        .showsss {
            font-size: 16px;
            color: green;
            margin-left: 10px;
        }
    </style>
@endpush

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample">
        <thead>
            <tr>
                <th>#</th>
                <th>Bus Reg. No</th>
                <th>Shift</th>
                <th>Location</th>
                <th>Description</th>
                <th>Photos</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($dailys as $daily)
            <tr>
                <td>{{ $number }}</td>
                <?php $number++; ?>
                <td>{{$vehicle->plate_num}}</td>
                <td>{{$daily->shift}}</td>
                <td>{{$daily->place_name}}</td>
                <td>{{Str::limit($daily->description, 20)}}</td>
                <td>
                    @if ($daily->images->isNotEmpty())
                    <i class="fa-solid fa-image showsss" data-ids="{{$daily->id}}"></i>
                    @endif

                    @if ($daily->video)
                        <i class="fa-solid fa-video text-warning" style="font-size: 16px;" data-bs-toggle="modal" data-bs-target="#video{{$daily->id}}"></i> 
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- video modal --}}
@foreach ($dailys as $daily)
<div class="modal fade" id="video{{$daily->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Video Routine Maintenance For {{$vehicle->plate_num}}  </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @if ($daily->video)
          <video controls style="width: 100%">
            <source type="video/mp4" src="{{asset('store/'.$daily->video)}}">
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
            })
            
        
        })
    </script>
@endpush