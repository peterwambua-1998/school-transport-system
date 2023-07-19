<form action="{{ route('store_app_links') }}" id="my-form" method="post">
    @csrf

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">IOS</label>
                <input type="text" id="ios" name="ios" class="form-control"  placeholder="IOS" @if($links) value="{{$links->ios}}" @endif required>
                <span class="issue" id="ios_error"></span>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">Andriod</label>
                <input type="text" id="android" name="android" class="form-control"  placeholder="Android" @if($links)  value="{{$links->android}}" @endif required>
                <span class="issue" id="android_error"></span>
            
            </div>
        </div>
    </div>
    <div class="text-center">
    <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
    </div>
</form>

@push('custom-scripts')
<script defer>
    $(function() {
      $('#submit-btn').on('click',(e)=>{
        if(!$('#ios').val()){
          $('#ios').focus();
          $('#ios_error').text('field required');
          e.preventDefault();
          return;
        } else{
          $('#ios_error').text('');
        }

        if(!$('#android').val()){
          $('#android').focus();
          $('#android_error').text('field required');
          e.preventDefault();
          return;
        } else{
          $('#android_error').text('');
        }
  
        $('#my-form').submit();
      })
      
    })
</script>
@endpush
