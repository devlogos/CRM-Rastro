/*
 * other main functions
 */

// main variables for api endpoints
var domain = getKey("DOMAIN");
var domain_unsafe = getKey("DOMAIN").replace("https", "http");
var sellerId = 0;
var statusId = getKey("USER_STATUS_ID");
var sectorsId = getKey("USER_SECTORS_ID");
var sectorsName = getKey("USER_SECTORS_NAME");
var companyId = getKey("USER_COMPANY_ID");
var secretKey = getKey("SECRETKEY");
var token = getKey("TOKEN");

Object.defineProperty(String.prototype, "bool", {
    get: function() {
        return /^(true|1)$/i.test(this);
    }
});

function getKey(item) {
    return localStorage.getItem(item);
}

function saleStorage(content) {
    localStorage.setItem("salesContent", JSON.stringify(content));
}

function sellerStorage(content) {
    localStorage.setItem("sellersContent", JSON.stringify(content));
}

function getSale(index, key) {
    var key = typeof key === "undefined" ? null : key;

    var sale = jQuery.parseJSON(localStorage.getItem("salesContent"));

    if (key !== null) {
        return sale[index]["" + key + ""];
    } else {
        return JSON.stringify(sale[index]);
    }
}

function getSeller(index, key) {
    var key = typeof key === "undefined" ? null : key;

    var sale = jQuery.parseJSON(localStorage.getItem("sellersContent"));

    if (key !== null) {
        return sale[index]["" + key + ""];
    } else {
        return JSON.stringify(sale[index]);
    }
}

function readListProducts(categoryId, content) {
    if (parseInt(categoryId) === -1) {
        $(content).html(
            '<div role="alert" class="alert alert-warning"><span>Selecione uma categoria!</span></div>'
        );

        return;
    }

    $.ajax({
        type: "GET",
        url: domain + "/products/" + companyId + "/" + categoryId + "/0",
        dataType: "json",
        beforeSend: function(xhr) {
            $(content).html("Aguarde...");

            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(productsContent) {
            var option =
                '<select name="product_id" class="form-control select2" id="product_id">';

            for (var item in productsContent) {
                var products = JSON.stringify(productsContent[item]);

                var product = jQuery.parseJSON(products);

                option +=
                    "<option value='" +
                    product.id +
                    "'>" +
                    product.name +
                    "</option>";
            }

            option += "</select>";

            $(content).html(option);

            $(".select2").select2();
            $(".select2-container").width("auto");
        }
    });
}

function readListCities(stateId, content, showDistrict, showmultiple, values) {
    var districtShow =
        typeof showDistrict === "undefined" ? true : showDistrict;
    var multipleShow =
        typeof showmultiple === "undefined" ? false : showmultiple;
    var valuesShow = typeof values === "undefined" ? null : values;

    if (stateId.length === -1) {
        $(content).html(
            '<div role="alert" class="alert alert-warning"><span>Selecione um estado!</span></div>'
        );

        return;
    }

    $.ajax({
        type: "GET",
        url: domain + "/cities/" + companyId + "/" + stateId,
        dataType: "json",
        beforeSend: function(xhr) {
            $(content).html("Aguarde...");

            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(citiesContent) {
            if (citiesContent !== null) {
                var option =
                    '<label for="city_id"><span class="mr-1 required">*</span>Cidade</label>';

                if (multipleShow) {
                    option +=
                        '<select name="city_id[]" class="form-control select2" id="city_id" multiple="multiple">';
                } else {
                    option +=
                        '<select name="city_id" class="form-control select2" id="city_id">';
                }

                option += '<option value="0">Selecione</option>';

                for (var item in citiesContent) {
                    var cities = JSON.stringify(citiesContent[item]);

                    var city = jQuery.parseJSON(cities);

                    option +=
                        "<option value='" +
                        city.id +
                        "'>" +
                        city.name +
                        "</option>";
                }

                option += "</select>";

                $(content).html(option);

                if (districtShow) {
                    $("#city_id").on("change", function() {
                        readListDisctricts($(this).val(), ".districts-content");
                    });
                }

                $(".select2").select2();
                $(".select2-container").width("auto");

                if (valuesShow !== null) {
                    $("#city_id")
                        .val(valuesShow)
                        .trigger("change");
                }
            }
        }
    });
}

function readListDisctricts(cityId, content) {
    if (parseInt(cityId) === 0) {
        $(content).html(
            '<div role="alert" class="alert alert-warning"><span>Selecione uma cidade!</span></div>'
        );

        return;
    }

    $.ajax({
        type: "GET",
        url: domain + "/districts/cities/" + companyId + "/" + cityId,
        dataType: "json",
        beforeSend: function(xhr) {
            $(content).html("Aguarde...");

            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(districtsContent) {
            if (districtsContent !== null) {
                var option =
                    '<select name="district_id" class="form-control select2" id="district_id">';

                for (var item in districtsContent) {
                    var districts = JSON.stringify(districtsContent[item]);

                    var district = jQuery.parseJSON(districts);

                    option +=
                        "<option value='" +
                        district.id +
                        "'>" +
                        district.name +
                        "</option>";
                }

                option += "</select>";

                $(content).html(option);

                $(".select2").select2();
                $(".select2-container").width("auto");
            } else {
                $(content).html(
                    '<div role="alert" class="alert alert-warning"><span>Não existem bairros para a cidade selecionada!</span></div>'
                );
            }
        }
    });
}

function readListClients(clientId, content) {
    $.ajax({
        type: "GET",
        url: domain + "/clients/" + companyId + "/" + clientId,
        dataType: "json",
        beforeSend: function(xhr) {
            $(content).html("Aguarde...");

            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(clientsContent) {
            if (clientsContent !== null) {
                var option =
                    '<select name="client_id" class="form-control select2" id="client_id">';

                option += "<option value='0'>Selecione</option>";

                for (var item in clientsContent) {
                    var clients = JSON.stringify(clientsContent[item]);

                    var client = jQuery.parseJSON(clients);

                    option +=
                        "<option value='" +
                        client.id +
                        "'>" +
                        client.name +
                        "</option>";
                }

                option += "</select>";

                $(content).html(option);

                $(".select2").select2();
            } else {
                $(content).html("Não foi possível listar clientes!");
            }
        }
    });
}

$.fn.datepicker.language["pt-br"] = {
    days: [
        "Domingo",
        "Segunda",
        "Terça",
        "Quarta",
        "Quinta",
        "Sexta",
        "Sábado"
    ],
    daysShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
    daysMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
    months: [
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro"
    ],
    monthsShort: [
        "Jan",
        "Fev",
        "Mar",
        "Abr",
        "Mai",
        "Jun",
        "Jul",
        "Ago",
        "Set",
        "Out",
        "Nov",
        "Dez"
    ],
    today: "Hoje",
    clear: "Limpar",
    dateFormat: "yyyy-mm-dd",
    timeFormat: "hh:ii",
    firstDay: 0
};

function checkAllSellers() {
    $(".seller-chk").each(function() {
        if ($(this).prop("checked")) {
            $(this).prop("checked", false);
        } else {
            $(this).prop("checked", true);
        }
    });
}
