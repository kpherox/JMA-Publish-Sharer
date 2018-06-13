@extends ('entry')

@section ('moredetails')
<div class="card">
    <div class="card-body">
        <div id="map"></div>
        <script>
        var map
          , initMap = async () => {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: { lat: 36.59444, lng: 136.62556 },
                    mapTypeId: 'terrain'
                });
                let getLatLng = async (vector) => {
                        return vector.slice(0, -1).split('/').map(x => {
                            let point = x.match(/([()+-][0-9]+\.?[0-9]+?)/g);

                            return { lat: Number.parseFloat(point[0]), lng: Number.parseFloat(point[1]) };
                        });
                    }
                  , drawIsobar = async (vector) => {
                        let latLng = await getLatLng(vector)
                          , isobar = new google.maps.Polyline({
                                path: latLng,
                                geodesic: true,
                                strokeColor: '#000000',
                                strokeOpacity: 0.5,
                                strokeWeight: 1
                            });
                        isobar.setMap(map);
                    }
                  , markerSize = new google.maps.Size(48, 48)
                  , markerPoint = new google.maps.Point(23, 15)
                  , drawMarker = async (vector, title, label, color) => {
                        let latLng = await getLatLng(vector)
                          , marker = new google.maps.Marker({
                                position: latLng[0],
                                title: title,
                                label: {
                                    color: '#FFF',
                                    text: label
                                },
                                icon: {
                                    url: 'https://maps.google.com/mapfiles/ms/icons/'+color+'.png',
                                    labelOrigin: markerPoint,
                                    scaledSize: markerSize,
                                    size: markerSize
                                }
                            });

                        marker.setMap(map);
                    }
                  , drawOverlay = async (item) => {
                        if (item[0]) {
                            console.log('Not supported multiple kind');
                            return;
                        }

                        let property = item.Kind.Property;
                        let propertyType = property.Type;
                        switch (propertyType) {
                            case '等圧線':
                                drawIsobar(property.IsobarPart.Line);
                                break;
                            case '高気圧':
                                drawMarker(property.CenterPart.Coordinate, '高気圧', '高', 'blue');
                                break;
                            case '低気圧':
                                drawMarker(property.CenterPart.Coordinate, '低気圧', '低', 'red');
                                break;
                            default:
                                console.log('Not supported kind type:', propertyType);
                                break;
                        }
                    }
                  , infoItemForEach = async (info) => {
                        switch (info['@attributes'].type) {
                            case '天気図情報':
                                info.MeteorologicalInfo.Item.forEach(drawOverlay);
                                break;
                            default:
                                console.log('Not supported info type:', info['@attributes'].type);
                                break;
                        }
                    }
                  , infos = @json ($entry['Body']['MeteorologicalInfos']);

                if (infos[0]) {
                    infos.forEach(infoItemForEach);

                    return;
                };

                infoItemForEach(infos);
            };
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={!! config('services.googleapi.maps.js.key') !!}&callback=initMap"></script>
    </div>
</div>
@endsection
