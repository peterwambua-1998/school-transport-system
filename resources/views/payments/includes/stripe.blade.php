<form action="{{ route('paygate-store') }}" method="post">
    @csrf

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">Stripe Public Key</label>
                <input type="text" name="stripe_public" class="form-control"  placeholder="Stripe Public Key" value="{{$paySettings->public_key ?? ''}}" required>
            </div>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <div class="mb-3">
                <label class="form-label" for="title">Stripe Private Key</label>
                <input type="text" name="stripe_private" class="form-control"  placeholder="Stripe Private Key" value="{{$paySettings->private_key ?? ''}}" required>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
    </div>
    
</form>