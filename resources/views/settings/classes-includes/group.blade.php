<form action="{{ route('groups.store') }}" method="post">
    @csrf

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="mb-3">
                <label class="form-label text-muted" for="title">Group</label>
                <input type="text" name="group" class="form-control"  placeholder="Class Group" >
            </div>
        </div>
        
    </div>

    <div class="text-center">
    <button type="submit" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
</div>
</form>