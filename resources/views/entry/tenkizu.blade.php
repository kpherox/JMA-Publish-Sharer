@extends ('entry')

@section ('moredetails')
<div class="card">
    <div class="card-body">
        <div id="map"></div>
        <script>
        var map
          , initMap = () => {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: {lat: 36.59444, lng: 136.62556},
                    mapTypeId: 'terrain'
                });
            };
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={!! config('services.googleapi.maps.js.key') !!}&callback=initMap"></script>
    </div>
</div>
@endsection
