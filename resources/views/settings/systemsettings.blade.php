<form action="{{ route('settings.store') }}" method="post" enctype="multipart/form-data" id="my-form">
    @csrf

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label ">Company Name</label>
                <input required type="text" id="company_name" name="company_name" class="form-control inputs"  placeholder="Company Name" value="{{ $settings->company_name ?? 'My Company' }}">
                <span class="issue" id="company_name_error"></span>
            </div>
        </div>

        <div class="form-group col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label " for="title">Company Email</label>
                <input required type="email" id="company_email" name="company_email" class="form-control inputs"  placeholder="Company Contact Email" value="{{ $settings->company_email ?? 'My Contact Number' }}">
                <span class="issue" id="company_email_error"></span>
            
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label " for="title">Company Phone Number</label>
                <input required type="text" id="company_pnum" name="company_pnum" class="form-control inputs"  placeholder="Company Contact Number" value="{{ $settings->company_pnum ?? '0000' }}">
                <span class="issue" id="company_pnum_error"></span>
            
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label " for="title">Company Address</label>
                <input required type="text" id="company_address" name="company_address" class="form-control inputs"  placeholder="Company Address" value="{{ $settings->company_address ?? 'My Contact Number' }}" required>
                <span class="issue" id="company_address_error"></span>
            
            </div>
        </div>

       
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label " for="title">Company Logo</label>
                <input required type="file" name="image" class="form-control inputs"  placeholder="Company Contact Details">
            
            </div>
        </div>

        <div class="form-group col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label " for="title">Currency</label>
                <input required type="text" id="currency" name="currency" class="form-control inputs"  placeholder="Company Address" value="{{ $settings->currency ?? 'USD' }}">
                <span class="issue" id="currency_error"></span>
            
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label " for="title">Time Zone</label>
                <input required type="text" name="time_zone" class="form-control inputs"  placeholder="Time zone" required id="time-zone">
                <span class="issue" id="time_zone_error"></span>
            </div>
        </div>
    </div>

    <input required type="hidden" class="form-control inputs lat" name="lat" value="{{$settings->lat}}">
    <input required type="hidden" class="form-control inputs lng" name="lng" value="{{$settings->lng}}">
    

    <div class="text-center">
        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
    </div>
    
</form>

@push('custom-scripts')
    <script defer>
        $(function() {
            $('#submit-btn').on('click', (e) => {
              
                if(!$('#company_name').val()){
                    $('#company_name').focus();
                    $('#company_name_error').text('field required');
                    e.preventDefault();
                    return;
                } else{
                    $('#company_name_error').text('');
                }

                if(!$('#company_email').val()){
                    $('#company_email').focus();
                    $('#company_email_error').text('field required');
                    e.preventDefault();
                    return;
                } else{
                    $('#company_email_error').text('');
                }

                if(!$('#company_pnum').val()){
                    $('#company_pnum').focus();
                    $('#company_pnum_error').text('field required');
                    e.preventDefault();
                    return;
                } else{
                    $('#company_pnum_error').text('');
                }


                if(!$('#company_address').val()){
                    $('#company_address').focus();
                    $('#company_address_error').text('field required');
                    e.preventDefault();
                    return;
                } else{
                    $('#company_address_error').text('');
                }


                if(!$('#currency').val()){
                    $('#currency').focus();
                    $('#currency_error').text('field required');
                    e.preventDefault();
                    return;
                } else{
                    $('#currency_error').text('');
                }

                if(!$('#time-zone').val()){
                    $('#time-zone').focus();
                    $('#time_zone_error').text('field required');
                    e.preventDefault();
                    return;
                } else{
                    $('#time_zone_error').text('');
                }

                $('#my-form').submit();
            });
        })
    </script>
@endpush