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
                let infos = @json ($entry['Body']['MeteorologicalInfos'])
                  , infoItems = infos[0] ? null : infos.MeteorologicalInfo.Item
                  , drawIsobar = async item => {
                        if (item.Kind.Property.Type === '\u7b49\u5727\u7dda') {
                            drawPolyline(item.Kind.Property.IsobarPart.Line);
                            return;
                        }
                    };
                if (infoItems !== null) {
                    infoItems.forEach(drawIsobar);
                    return;
                }

                Infos.forEach(info => info.MeteorologicalInfo.Item.forEach(drawIsobar));
            }
          , getPath = (vector) => {
                return vector.split('/').filter(v => v).map(x => {
                    let point = x.match(new RegExp('([()+-][0-9]+\.?[0-9]+?)','g'))
                      , latlng = {
                            lat: Number(point[0]),
                            lng: Number(point[1])
                        };

                    return latlng;
                });
            }
          , drawPolyline = (vector) => {
                let polyline = getPath(vector)
                  , path = new google.maps.Polyline({
                        path: polyline,
                        geodesic: true,
                        strokeColor: '#000000',
                        strokeOpacity: 0.5,
                        strokeWeight: 1
                    });
                path.setMap(this.map);
            };
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={!! config('services.googleapi.maps.js.key') !!}&callback=initMap"></script>
    </div>
</div>
@endsection
