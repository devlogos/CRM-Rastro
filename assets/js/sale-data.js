/* 
 * definition of functions for tagging and use of their values from array data
 */

function customTagSale(tagName, fn) {
    document.createElement(tagName);

    var tagInstances = document.getElementsByTagName(tagName);

    for (var i = 0; i < tagInstances.length; i++) {
        fn(tagInstances[i]);
    }
}

function attributesSale(element) {
    if (element.attributes.field) {
        var value = element.attributes.field.value;

        if (localStorage.hasOwnProperty('sale')) {
            result = jQuery.parseJSON(localStorage.getItem('sale'));

            for (var item in result) {
                if (value === item) {
                    localStorage.setItem('seller_id', result['id']);
                    
                    if (value === 'code') {
                        element.innerHTML = result[item].toUpperCase();
                    }
                    else if (value === 'creation_date') {
                        var date = new Date(result[item]);
                        var day = date.getDate();
                        var month = (date.getMonth() + 1);
                        var year = date.getFullYear();
                        var time = ("00" + date.getHours()).slice(-2) + ':' + ("00" + date.getMinutes()).slice(-2);

                        element.innerHTML = day + ' de ' + getMonth(month) + ' de ' + year + ' às ' + time;
                    }
                    else if (value === 'client_is_holder') {
                        if (result[item] === true) {
                            element.innerHTML = '<span class=\"badge badge-success text-white\">Titular</span>';
                        }
                        else {
                            element.innerHTML = '';
                        }
                    }
                    else if (value === 'client_email') {
                        if (result[item] === '') {
                            element.innerHTML = 'E-mail';
                        }
                        else {
                            element.innerHTML = result[item];
                        }
                    }
                    else if (value === 'client_telephone') {
                        if (result[item] === '') {
                            element.innerHTML = 'Telefone';
                        }
                        else {
                            element.innerHTML = result[item];
                        }
                    }
                    else if (value === 'note') {
                        if (result[item] === '') {
                            element.innerHTML = '<div role=\"alert\" class=\"alert alert-warning\"><span>Nenhuma observação realizada!</span></div>';
                        }
                        else {
                            element.innerHTML = result[item];
                        }
                    }
                    else {
                        element.innerHTML = '<span>' + result[item] + '</span>';
                    }
                }
                else {
                    if (value === 'map') {
                        element.innerHTML = '<div class=\"card-body\" id=\"map\"></div>';

                        if (result['latitude'] === 0 || result['longitude'] === 0) {
                            element.innerHTML = '<div role=\"alert\" class=\"alert alert-warning\"><span><span><strong>Atenção! </strong>Geolocalização não identificada pelo dispositivo da venda.</span></div>';

                            return;
                        }

                        initMapDetailSale(result['seller_url_image'], result['latitude'], result['longitude'], result['district_name'], result['seller_name']);
                    }
                    else if (value === 'status') {
                        element.innerHTML = "<div class=\"status-details-sale\" style=\"background-color: " + result['status_color'] + "\"></div>";
                    }
                    else if (value === 'audio') {
                        if (result['send_audio_file'] === true) {
                            var audioFilepath = domain + '/media/audio/sales/' + result['audio_time_stamp'] + '.flac';

                            element.innerHTML = "<audio id=\"audioSale\" src=\"" + audioFilepath + "\" preload=\"auto\" controls><p>Seu nevegador não suporta o elemento audio.</p></audio>";
                        }
                        else {
                            element.innerHTML = '<div role=\"alert\" class=\"alert alert-warning\"><span><strong>Atenção! </strong>Gravação não sincronizada.</span></div>';
                        }
                    }
                    else if (value === 'average_time') {
                        var initialDate = moment(new Date(result['creation_date']));
                        var finalDate = moment(new Date(result['update_date']));                        
                        var duration = moment.duration(finalDate.diff(initialDate));
                        
                        var minutes = duration.get('minutes') <= 1 ? duration.get('minutes') === 0 ? '' : '1 minuto e ' : duration.get('minutes') + ' minutos e ';
                        var seconds = duration.get('seconds') <= 1 ? duration.get('seconds') === 0 ? '' : '1 segundo' : duration.get('seconds') + ' segundos';                        
                                                
                        var time = minutes + seconds;
                        
                        element.innerHTML = (time);
                    }
                    else if (value === 'documents') {
                        var images = jQuery.parseJSON(JSON.stringify(result['image_time_stamp']));

                        var strImages = '';
                        
                        if (images.length > 0){
                            strImages = strImages +
                                        '<div class="row ml-0">' +
                                            '<div class="custom-control custom-checkbox">' +
                                                '<input type="checkbox" name="check_all" class="custom-control-input" id="check_all">' +
                                                '<label class="custom-control-label" for="check_all">Todos</label>' +
                                            '</div>' +
                                        '</div>';
                        }                                               
                        
                        strImages = strImages + '<div class="row sortable">';

                        var inc = 0;

                        for (var index in images) {
                            inc++;

                            var imageFilepath = '/media/images/documents/' + images[index]['image'] + '.jpg';
                            var docId = 'doc-' + inc;
                            var colStartId = 'col-doc-' + inc;

                            strImages = strImages +
                                        "<div class=\"col-sm-6 col-md-4 col-lg-3 col-doc col-doc-" + result['id'] + "\" id=\"" + colStartId + "\">" +
                                            '<div class="custom-control custom-checkbox check_doc">' +
                                                "<input type=\"checkbox\" name=\"check_doc[]\" data=\"" + imageFilepath + "\" class=\"custom-control-input check-document\" id=\"" + docId + "\">" +
                                                "<label class=\"custom-control-label\" for=\"" + docId + "\">&nbsp;</label>" +
                                            '</div>' +
                                            "<a href=\"" + domain + imageFilepath + "\" data-lightbox=\"photos\">" +
                                                "<img class=\"img-thumbnail img-fluid\" src=\"" + domain + imageFilepath + "\">" +
                                            '</a>' +
                                        '</div>';
                        }

                        strImages = strImages + "</div>";
                        
                        strImages = strImages + 
                                    '<div class="row mt-3">' +
                                        '<div class="col">' +
                                            '<button class="btn btn-info convert-to-pdf" type="button"><i class="material-icons">picture_as_pdf</i></button>' +
                                        '</div>' +
                                    '</div>';

                        if (inc === 0) {
                            element.innerHTML = '<div role=\"alert\" class=\"alert alert-warning\"><span>Nenhum documento enviado! Verifique o motivo em caso de cancelamento.</span></div>';
                        }
                        else {
                            element.innerHTML = strImages;
                        }
                        
                        $(".sortable").sortable();
                        
                        $('.col-doc-' + result['id']).each(function () {
                            
                            $(this).draggable({
                                connectToSortable: '.sortable'
                            });
                            
                        });
                        
                        $('#check_all').on('click', function(){
                            if ($(this).prop('checked')) {
                                $('.check-document').each(function () {
                                    $(this).prop('checked', true);
                                });
                            }
                            else {
                                $('.check-document').each(function () {
                                    $(this).prop('checked', false);
                                });
                            }
                        });
                        
                        $('.convert-to-pdf').on('click', function (e) {
                            var paths = '';

                            $('.check-document').each(function () {
                                if ($(this).prop('checked')) {
                                    paths = paths + $(this).attr('data') + ',';
                                }
                            });

                            if (paths !== '') {
                                convertImagesToPDF(paths, result['audio_time_stamp']);
                            }
                            else {
                                $('#result-convert').html('<div role=\"alert\" class=\"alert alert-warning\"><span>Não existem documentos selecionados!</span></div>');
                            }
                        });
                    }
                    else if (value === 'button_finally') {
                        var background = getKey('finished_color');

                        element.innerHTML = "<button class=\"btn btn-info finally-sale ml-1\" style=\"background-color: " + background + "; border-color: " + background + "\" type=\"button\">Finalizar</button>";

                        $('.finally-sale').click(function () {
                            updateStatusSale(getKey('finished_id'), result['id'], getKey('finished_name'));
                        });
                    }
                    else if (value === 'reason') {
                        var itsCancelled = result['its_cancelled'];

                        if (itsCancelled) {
                            element.innerHTML = 'Motivo: ' + result['reason_name'];
                        }
                        else {
                            element.innerHTML = '';
                        }
                    }
                }
            }
        }
    }
}

function convertImagesToPDF(paths, timestamp) {
    $.ajax({
        type: 'POST',
        url: "images/convert/" + companyId,
        data: {'paths': paths, 'timestamp': timestamp},
        dataType: 'html',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('token', token);
            xhr.setRequestHeader('key', secretKey);
        },
        success: function (content) {
            $('#result-convert').html(content);
        }
    });
}

function initMapDetailSale(url_image_seller, latitude, longitude, district, sellerName) {
    var coordinates = {lat: latitude, lng: longitude};

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: coordinates
    });

    var strInfoWindow = '<div>' +
            "<img class='img-seller-map' src='" + url_image_seller + "' />" +
            sellerName + '<br>' +
            'Latitude: ' + latitude + '<br>' +
            'Longitude: ' + longitude +
            '<div>';

    var infowindow = new google.maps.InfoWindow({
        content: strInfoWindow
    });

    var icon = {
        path: 'M236.925,0.124c-96.9,3.4-177.4,79-186.7,175.5c-1.9,19.3-0.8,38,2.6,55.9l0,0c0,0,0.3,2.1,1.3,6.1 c3,13.4,7.5,26.4,13.1,38.6c19.5,46.2,64.6,123.5,165.8,207.6c6.2,5.2,15.3,5.2,21.6,0c101.2-84,146.3-161.3,165.9-207.7 c5.7-12.2,10.1-25.1,13.1-38.6c0.9-3.9,1.3-6.1,1.3-6.1l0,0c2.3-12,3.5-24.3,3.5-36.9C438.425,84.724,347.525-3.776,236.925,0.124 z M243.825,291.324c-52.2,0-94.5-42.3-94.5-94.5s42.3-94.5,94.5-94.5s94.5,42.3,94.5,94.5S296.025,291.324,243.825,291.324z',
        fillColor: '#FF4D4D',
        fillOpacity: 1,
        scale: 0.098,
        strokeWeight: 0
    };

    var marker = new google.maps.Marker({
        position: coordinates,
        map: map,
        title: district,
        icon: icon,
        animation: google.maps.Animation.BOUNCE
    });

    marker.addListener('click', function () {
        infowindow.open(map, marker);
    });
}

function getMonth(monthNumber) {
    switch (monthNumber) {
        case 1:
            return 'Janeiro';
            break;
        case 2:
            return 'Fevereiro';
            break;
        case 3:
            return 'Março';
            break;
        case 4:
            return 'Abril';
            break;
        case 5:
            return 'Maio';
            break;
        case 6:
            return 'Junho';
            break;
        case 7:
            return 'Julho';
            break;
        case 8:
            return 'Agosto';
            break;
        case 9:
            return 'Setembro';
            break;
        case 10:
            return 'Outubro';
            break;
        case 11:
            return 'Novembro';
            break;
        case 12:
            return 'Dezembro';
            break;
    }
}

function loadDetailsSale() {
    customTagSale('sale', attributesSale);
}