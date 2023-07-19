<form method="POST" action="{{ route('centermap_store') }}">
    @csrf
    

    <div class="map-wrapper">
        <input
        id="pac-input"
        class="controls"
        type="text"
        placeholder="Search Box"
        />
        <div id="map"></div>

        <input type="hidden" class="form-control lat" name="lat" value="{{$settings->lat}}">
        <input type="hidden" class="form-control lng" name="lng" value="{{$settings->lng}}">
    
        <input type="hidden" name="settings_id" value="{{ $settings->id ?? 'not settings' }}">
</div>
<div class="text-center">
    <button type="submit" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
</div>
</form>
