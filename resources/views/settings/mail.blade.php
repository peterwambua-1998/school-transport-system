<form action="{{ route('email_store') }}" method="post" id="email-my-form">
  @csrf

  
  <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="title">Senders Name</label>
          <input required type="text" id="email_name" name="name" class="form-control"  placeholder="Senders Name" @if($emailSettings) value="{{ $emailSettings->name}}" @endif>
          <span class="issue" id="email_name_error"></span>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="title">Senders Email</label>
          <input required type="email" name="email" class="form-control" id="email_email" placeholder="Senders Email" @if($emailSettings) value="{{ $emailSettings->email}}"@endif>
          <span class="issue" id="email_email_error"></span>
        </div>
      </div>
  </div>
  

  <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="title">Username</label>
          <input required type="text" name="username" id="email_username" class="form-control"  placeholder="Username" @if($emailSettings) value="{{ $emailSettings->username}}"@endif>
          <span class="issue" id="email_username_error"></span>
        
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="title">Password</label>
          <input required type="text" name="password" id="email_password" class="form-control"  placeholder="Password" @if($emailSettings) value="{{ $emailSettings->password}}"@endif>
          <span class="issue" id="email_password_error"></span>
        
        </div>
      </div>
  </div>

  <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="">SMTPAutoTLS</label>
          <input required type="text" name="SMTPAutoTLS" id="SMTPAutoTLS" class="form-control"  placeholder="SMTPAutoTLS" @if($emailSettings) value="{{ $emailSettings->SMTPAutoTLS}}"@endif>
          <span class="issue" id="SMTPAutoTLS_error"></span>
        
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="mb-3">
          <label class="form-label " for="">SMTPAuth</label>
          <input required type="text" name="SMTPAuth" id="SMTPAuth" class="form-control"  placeholder="SMTPAuth" @if($emailSettings) value="{{ $emailSettings->SMTPAuth}}"@endif>
          <span class="issue" id="SMTPAuth_error"></span>
        
        </div>
      </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <div class="mb-3">
        <label class="form-label " for="">Host</label>
        <input required type="text" name="host" id="email_host" class="form-control"  placeholder="smtp.mailtrap.io" @if($emailSettings) value="{{ $emailSettings->host}}"@endif>
        <span class="issue" id="email_host_error"></span>
      
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <div class="mb-3">
        <label class="form-label " for="">Port</label>
        <input required type="text" name="port" id="email_port" class="form-control"  placeholder="2525" @if($emailSettings) value="{{ $emailSettings->port}}"@endif>
        <span class="issue" id="email_port_error"></span>
      
      </div>
    </div>
</div>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <div class="mb-3">
        <label class="form-label " for="">Security</label>
        <input required type="text" name="security" id="security" class="form-control"  placeholder="tls" @if($emailSettings) value="{{ $emailSettings->security}}" @endif>
        <span class="issue" id="email_security_error"></span>
      
      </div>
    </div>
</div>

<div class="text-center">
  <button type="button" id="email-submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
</div>

</form>


@push('custom-scripts')
    <script defer>
      $('#email-submit-btn').on('click',(e)=>{
            if(!$('#email_name').val()){
              $('#email_name').focus();
              $('#email_name_error').text('field required');
              $('html, body').animate({
                scrollTop: "0px"
            }, 800);
              e.preventDefault();
              return;
            } else{
              $('#email_name_error').text('');
            }

            if(!$('#email_email').val()){
              $('#email_email').focus();
              $('#email_email_error').text('field required');
              $('html, body').animate({
                scrollTop: "0px"
            }, 800);
              e.preventDefault();
              return;
            } else{
              $('#email_email_error').text('');
            }


            if(!$('#email_username').val()){
              $('#email_username').focus();
              $('#email_username_error').text('field required');
              
              e.preventDefault();
              return;
            } else{
              $('#email_username_error').text('');
            }



            if(!$('#email_password').val()){
              $('#email_password').focus();
              $('#email_password_error').text('field required');
              e.preventDefault();
              return;
            } else{
              $('#email_password_error').text('');
            }



            if(!$('#SMTPAutoTLS').val()){
              $('#SMTPAutoTLS').focus();
              $('#SMTPAutoTLS_error').text('field required');
              e.preventDefault();
              return;
            } else{
              $('#SMTPAutoTLS_error').text('');
            }



            if(!$('#SMTPAuth').val()){
              $('#SMTPAuth').focus();
              $('#SMTPAuth_error').text('field required');
              e.preventDefault();
              return;
            } else{
              $('#SMTPAuth_error').text('');
            }



            if(!$('#email_host').val()){
              $('#email_host').focus();
              $('#email_host_error').text('field required');
              e.preventDefault();
              return;
            } else{
              $('#email_host_error').text('');
            }


            if(!$('#email_port').val()){
              $('#email_port').focus();
              $('#email_port_error').text('field required');
              e.preventDefault();
              return;
            } else{
              $('#email_port_error').text('');
            }

            if(!$('#security').val()){
              $('#security').focus();
              $('#email_security_error').text('field required');
              e.preventDefault();
              return;
            } else{
              $('#email_security_error').text('');
            }
            $('#email-my-form').submit();
        })
    </script>
@endpush