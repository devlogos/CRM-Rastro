/* 
 * definition of functions for tagging and use of their values from array data
 */

function customTagSeller(tagName, fn) {
    //document.createElement(tagName);

    var tagInstances = document.getElementsByTagName(tagName);

    for (var i = 0; i < tagInstances.length; i++) {
        fn(tagInstances[i]);
    }
}

function attributesSeller(element) {
    if (element.attributes.name) {
        var value = element.attributes.name.value;

        if (localStorage.hasOwnProperty('seller')) {
            result = jQuery.parseJSON(localStorage.getItem('seller'));

            for (var item in result) {
                var objQuery = '#' + value;

                var newValue = value.replace('[]', '');

                if (newValue === item) {
                    if (newValue === 'url_image') {
                        var imageUrl = domain + '/media/images/sellers/' + result[newValue];

                        if (result[newValue] === null) {
                            imageUrl = '';
                        }

                        var drEvent = $('#url_image').dropify(
                                {
                                    defaultFile: imageUrl,
                                    messages: {
                                        'default': 'Arraste e solte sua imagem aqui',
                                        'replace': 'Arraste e solte sua imagem para substituir',
                                        'remove': 'Remover',
                                        'error': 'Algo errado aconteceu!'
                                    },
                                    error: {
                                        'fileSize': 'Tamanho superior a 5 megabytes!',
                                        'fileExtension': 'Somente imagem em formato png ou jpg!'
                                    }
                                });
                        drEvent = drEvent.data('dropify');
                        drEvent.resetPreview();
                        drEvent.clearElement();
                        drEvent.settings.defaultFile = imageUrl;
                        drEvent.destroy();
                        drEvent.init();
                    }
                    else {
                        element.value = result[newValue];

                        if (newValue === 'email' || newValue === 'telephone') {
                            if (result[newValue] === 'Indefinido') {
                                element.value = '';
                            } else {
                                element.value = result[newValue];
                            }
                        }

                        if (newValue === 'state_id') {
                            $('#state_id').val(result[newValue]).trigger('change');

                            readListCities(result[newValue], '.cities-content', false, true, result['city_id']);
                        }

                        if (value === 'send_after_sale') {
                            $('#send_after_sale').prop('checked', result[newValue]);
                        }
                    }
                }
            }
        }
    }
}

function loadDetailsSeller() {
    customTagSeller('input', attributesSeller);
    customTagSeller('select', attributesSeller);
}