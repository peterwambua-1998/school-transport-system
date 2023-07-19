<form action="{{ url('/settings/store/payment') }}" method="post" id="my-form">
    @csrf
    <input type="hidden" name="flag" value="mpesa">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">Key</label>
                <input type="text" name="key" id="mpesa_key" class="form-control"  placeholder="Mpesa Key" value="{{ $mpesa->key ?? ''}}" required>
                <span class="issue" id="mpesa_key_error"></span>
            </div>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">Secret</label>
                <input type="text" name="secret" id="mpesa_secret" class="form-control"  placeholder="Mpesa Secret" value="{{ $mpesa->secret ?? ''}}" required>
                <span class="issue" id="mpesa_secret_error"></span>
            
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">ShortCode</label>
                <input type="text" name="short_code" id="short_code" class="form-control"  placeholder="Mpesa Short Code" value="{{ $mpesa->shortcode ?? ''}}" required>
                <span class="issue" id="short_code_error"></span>
            </div>
        </div>
        
    </div>

    <div class="text-center">
        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
    </div>

</form>


@push('custom-scripts')
    <script defer>
        $('#submit-btn').on('click',(e)=>{
            if(!$('#mpesa_key').val()){
                $('#mpesa_key').focus();
                $('#mpesa_key_error').text('field required');
                e.preventDefault();
                return;
            } else{
                $('#mpesa_key_error').text('');
            }

            if(!$('#mpesa_secret').val()){
                $('#mpesa_secret').focus();
                $('#mpesa_secret_error').text('field required');
                e.preventDefault();
                return;
            } else{
                $('#mpesa_secret_error').text('');
            }


            if(!$('#short_code').val()){
                $('#short_code').focus();
                $('#short_code_error').text('field required');
                e.preventDefault();
                return;
            } else{
                $('#short_code_error').text('');
            }

            $('#my-form').submit();
        })

    </script>
@endpush