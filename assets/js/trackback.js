/* 
 * definition of function for sales tracking
 */

var map;
var infoWindow;
var intervalTrackback;
var markers = [];
var latLngBounds;
var markerCluster = null;
var btnAudioMute = false;
var audioFile = new Audio();
var lastWindow = null;
var refreshTrackback = true;

function initMapTrackBack(content, dateinitial, datefinal, type, sellerid) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {

            var coordinates = {lat: position.coords.latitude, lng: position.coords.longitude};

            loadMap(content, coordinates, dateinitial, datefinal, type, sellerid);
        });
    } else {
        $.toast({
            heading: 'Atenção',
            text: 'Seu navegador não suporta geolocalização!',
            loader: true,
            loaderBg: '#CC0000',
            hideAfter: 5000
        });

        return;
    }
}

function loadMap(content, coordinates, dateinitial, datefinal, type, sellerid) {
    var dates = null;

    if (dateinitial === null || datefinal === null) {
        dates = 0;
    }
    else {
        dates = '' + dateinitial + ',' + datefinal;
    }

    map = new google.maps.Map(document.getElementById(content), {
        zoom: 10,
        center: coordinates,
        mapTypeId: 'roadmap'
    });

    infoWindow = new google.maps.InfoWindow();

    loadMarkers(dates, type, sellerid);

    intervalTrackback = setInterval(function (param1, param2, param3) {
        loadMarkers();
    }, 5000, dates, type, sellerid);
}

function loadMarkers(dates, type, sellerid) {    
    markers = [];
    
    $.ajax({
        type: 'GET',
        url: domain + "/geolocations/" + companyId + "/" + dates + "/" + type + "/" + sellerid,
        dataType: 'json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('token', token);
            xhr.setRequestHeader('key', secretKey);
        },
        success: function (contentGeolocation) {
            if (contentGeolocation === null) {
                $.toast({
                    heading: 'Atenção',
                    text: 'Não existem geolocalizações disponíveis para o vendedor selecionado!',
                    loader: true,
                    loaderBg: '#CC0000',
                    hideAfter: 5000
                });

                return;
            }

            // markers extension bounds
            latLngBounds = new google.maps.LatLngBounds();
            
            var i = 0;
            var path = 'M236.925,0.124c-96.9,3.4-177.4,79-186.7,175.5c-1.9,19.3-0.8,38,2.6,55.9l0,0c0,0,0.3,2.1,1.3,6.1 c3,13.4,7.5,26.4,13.1,38.6c19.5,46.2,64.6,123.5,165.8,207.6c6.2,5.2,15.3,5.2,21.6,0c101.2-84,146.3-161.3,165.9-207.7 c5.7-12.2,10.1-25.1,13.1-38.6c0.9-3.9,1.3-6.1,1.3-6.1l0,0c2.3-12,3.5-24.3,3.5-36.9C438.425,84.724,347.525-3.776,236.925,0.124 z M243.825,291.324c-52.2,0-94.5-42.3-94.5-94.5s42.3-94.5,94.5-94.5s94.5,42.3,94.5,94.5S296.025,291.324,243.825,291.324z';
            var scale = 0.098;

            for (var item in contentGeolocation) {
                i++;

                var geolocations = JSON.stringify(contentGeolocation[item]);
                var geolocation = jQuery.parseJSON(geolocations);

                var icon = {
                    path: path,
                    fillColor: geolocation.status_color,
                    fillOpacity: 1,
                    scale: scale,
                    strokeWeight: 0
                };

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(geolocation.latitude, geolocation.longitude),
                    icon: icon,
                    map: map
                });

                var geoSales = JSON.stringify(geolocation.sale);
                var geoSale = jQuery.parseJSON(geoSales);
                
                var saleAudio = '';

                var recAudio = geoSale[2] === '1' ? true : false;
                
                var saleSeller = typeof geoSale[4] === 'undefined' ? '' : 'Por ' + geoSale[4];
                var saleProduct = typeof geoSale[3] === 'undefined' ? '' : geoSale[3];
                
                if (recAudio) {
                    saleAudio = "<img onclick=\"clickAudio(this)\" audio=\"" + domain + "/media/audio/sales/" + geoSale[1] + ".flac" + "\" class=\"btn-audio-play\" src=\"" + domain + "/assets/img/audioplay.png" + "\" />";
                }
                
                var saleStatus = typeof geoSale[6] === 'undefined' ? 'Venda não realizada!' : 'Venda ' + '<span style="text-transform: lowercase;">' + geoSale[6] + '</span>';

                var strWindow = '<div>' +
                                saleSeller +
                                '</div>' +
                                '<div class="product-marker-track">' +
                                saleProduct +
                                '</div>' +
                                saleAudio +
                                "<div class=\"status-marker-track\" style=\"color:" + geolocation.status_color + "\">" +
                                saleStatus +
                                '</div>';

                var infoWindow = new google.maps.InfoWindow();

                google.maps.event.addListener(marker, 'click', (function (marker, content, infoWindow) {
                    return function () {
                        infoWindow.setContent(content);
                        
                        if (lastWindow) lastWindow.close();
                        
                        btnAudioMute = false;
                        
                        infoWindow.open(map, marker);
                        
                        lastWindow = infoWindow;
                    };
                })(marker, strWindow, infoWindow));

                markers.push(marker);

                latLngBounds.extend(marker.position);
            }
            
            var filePath = '../assets/img/markers/m';
            
            console.log(markers.length);

            markerCluster = new MarkerClusterer(map, markers, {
                imagePath: filePath
            });
            
            var newSellerId = typeof sellerid === 'undefined' ? 0 : sellerid;

            if (newSellerId !== 0) {
                map.fitBounds(latLngBounds);
            }
        }
    });
}

function clickAudio(obj) {
    var strAudioFile = $(obj).attr('audio');

    audioFile.src = strAudioFile;

    if (btnAudioMute) {
        audioFile.pause();

        $(obj).attr('src', domain + '/assets/img/audioplay.png');

        btnAudioMute = false;
    }
    else {
        audioFile.play();
        
        audioFile.onerror = function () {
            $.toast({
                heading: 'Atenção',
                text: 'Áudio não encontrado!',
                loader: true,
                loaderBg: '#E99636',
                hideAfter: 5000
            });
        };

        $(obj).attr('src', domain + '/assets/img/audiopause.png');

        btnAudioMute = true;
    }
}

$('.filter-trackback').on('click', function () {
    var dateInicial = $('#dateinitial').val() === '' ? null : $('#dateinitial').val();
    var dateFinal = $('#datefinal').val() === '' ? null : $('#datefinal').val();
    var type = 0;
    var sellerId = $('#seller_id').val();

    initMapTrackBack('mapTrackback', dateInicial, dateFinal, type, sellerId);
});