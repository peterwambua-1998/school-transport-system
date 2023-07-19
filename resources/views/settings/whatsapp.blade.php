<form action="{{ route('store_whatsapp') }}" method="post" id="wh-form">
    @csrf

    

    <div class="row">
        <div class="col-md-6 col-sm-12">
          <div class="mb-3">
            <label class="form-label " for="title">Enter SID</label>
            <input type="text" id="sid" name="sid" class="form-control"  placeholder="SID" @if($whatsapp) value="{{$whatsapp->sid}}" @endif required>
            <span class="issue" id="wh_sid_error"></span>
          </div> 
        </div>
        
        <div class="col-md-6 col-sm-12">
          <div class="mb-3">
            <label class="form-label " for="title">Enter Token</label>
            <input type="text" id="wh_token" name="token" class="form-control"  placeholder="Token" @if($whatsapp) value="{{$whatsapp->token ?? ''}}" @endif required>
            <span class="issue" id="wh_token_error"></span>
          </div> 
        </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="title">Enter Phone Number (eg +254700238886)</label>
          <input type="text" id="wh_pnum" name="p_num" class="form-control"  placeholder="Phone Number" @if($whatsapp) value="{{$whatsapp->twilio_num}}" @endif required>
          <span class="issue" id="wh_p_num_error"></span>
        </div> 
      </div>
      
      
  </div>

  <div class="text-center">
    <button type="button" id="wh_submit_btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
  </div>
  
</form>

@push('custom-scripts')
    <script defer>
      $(function(){
        $('#wh_submit_btn').on('click',(e)=>{
          if(!$('#sid').val()){
            $('#sid').focus();
            $('#wh_sid_error').text('field required');
            e.preventDefault();
            return;
          } else{
            $('#wh_sid_error').text('');
          }


          if(!$('#wh_token').val()){
            $('#wh_token').focus();
            $('#wh_token_error').text('field required');
            e.preventDefault();
            return;
          } else{
            $('#wh_token_error').text('');
          }

          if(!$('#wh_pnum').val()){
            $('#wh_pnum').focus();
            $('#wh_p_num_error').text('field required');
            e.preventDefault();
            return;
          } else{
            $('#wh_p_num_error').text('');
          }

          $('#wh-form').submit();
        })

      });
    </script>
@endpush