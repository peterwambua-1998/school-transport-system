<form action="{{ route('store_whatsapp') }}" method="post" >
    @csrf

    

    <div class="form-row">
        <div class="form-group col-md-6 col-sm-12">
          <label for="title">Enter SID</label>
          <input type="text" name="sid" class="form-control"  placeholder="" value="{{$whatsapp->sid ?? 'AC15413682a25156ad5c83227d6d355b95'}}">
        </div> 
        
        <div class="form-group col-md-6 col-sm-12">
          <label for="title">Enter Token</label>
          <input type="text" name="token" class="form-control"  placeholder="" value="{{$whatsapp->token ?? 'f90223590431a80e8e15bd1d181e8486'}}">
        </div> 
    </div>

    <div class="form-row">
      <div class="form-group col-md-6 col-sm-12">
        <label for="title">Enter Phone Number (eg +14155238886)</label>
        <input type="text" name="p_num" class="form-control"  placeholder="" value="{{$whatsapp->twilio_num ?? '+14155238886'}}" required>
      </div> 
      
      
  </div>


  <div class="text-center">
    
    <button class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
  </div>
</form>