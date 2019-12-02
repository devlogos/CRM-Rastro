/*
 * sellers list
 */

// set variables for task control
var refreshSellersList = true;
var countSellers = 0;
var sellers = null;
var tableSellers = $("#sellers");
var intervalSellers = null;
var rowSellerIndex = 0;
var incIndex = 0;

function readSellersList() {
    // define id company
    $(".companyid").val(companyId);

    $.ajax({
        type: "GET",
        url: domain + "/sellers/" + companyId + "/0",
        dataType: "json",
        beforeSend: function(xhr) {
            $.toast({
                text: "Aguarde...",
                showHideTransition: "slide",
                allowToastClose: false,
                stack: false,
                position: "mid-center",
                bgColor: "#ba4d4d",
                textColor: "#ffffff",
                textAlign: "center",
                loader: true,
                loaderBg: "#ffc107"
            });

            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(sellersContent) {
            if (sellersContent !== null) {
                sellers = $(tableSellers).DataTable({
                    paging: true,
                    destroy: true,
                    //lengthMenu: [10, 25, "Todos"],
                    data: sellersContent,
                    columns: [
                        { data: null, defaultContent: "" },
                        { data: "name" },
                        { data: null, defaultContent: "" },
                        { data: null, defaultContent: "" },
                        { data: null, defaultContent: "" },
                        { data: "challenge" },
                        { data: "email" },
                        { data: "telephone" },
                        { data: null, defaultContent: "" }
                    ],
                    columnDefs: [
                        {
                            targets: 0,
                            orderable: false,
                            width: "20px",
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                var strCheck =
                                    '<div class="custom-control custom-control-seller custom-checkbox">' +
                                    '<input type="checkbox" index=' +
                                    incIndex +
                                    ' name="check[]" id="row-' +
                                    rowData.id +
                                    '" class="custom-control-input seller-chk">' +
                                    '<label class="custom-control-label" for="row-' +
                                    rowData.id +
                                    '">&nbsp;</label>' +
                                    "</div>";

                                incIndex++;

                                $(td).prepend(strCheck);
                            }
                        },
                        {
                            targets: 2,
                            orderable: false,
                            width: "60px",
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                var imageProfile =
                                    domain +
                                    "/media/images/sellers/" +
                                    rowData.url_image;

                                if (rowData.url_image === null) {
                                    imageProfile =
                                        domain +
                                        "/assets/img/" +
                                        "seller-team.png";
                                }

                                $(td).prepend(
                                    '<img src="' +
                                        imageProfile +
                                        '" class="rounded-circle img-fluid image-profile" />'
                                );
                            }
                        },
                        {
                            targets: 3,
                            orderable: false,
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                var salesAmout = rowData.sales_amount;

                                if (salesAmout > 0) {
                                    $(td).prepend(
                                        '<span class="badge badge-dark badge-date-seller">' +
                                            salesAmout +
                                            "</span>"
                                    );
                                } else {
                                    $(td).prepend("0");
                                }
                            }
                        },
                        {
                            targets: 4,
                            orderable: false,
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                var totalSalesMade = rowData.total_sales_made;

                                if (totalSalesMade > 0) {
                                    $(td).prepend(
                                        '<span class="badge badge-success badge-date-seller">' +
                                            totalSalesMade +
                                            "</span>"
                                    );
                                } else {
                                    $(td).prepend("0");
                                }
                            }
                        },
                        {
                            targets: -1,
                            width: "62px",
                            orderable: false,
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                $(td).prepend(
                                    '<button dataid="' +
                                        rowData.id +
                                        '" class="btn btn-info select" type="button"><i class="icon-options"></i></button>'
                                );
                            }
                        }
                    ],
                    language: {
                        search: "Pesquisar",
                        lengthMenu: "Exibir _MENU_",
                        zeroRecords: "Conteúdo não encontrado!",
                        info: "_TOTAL_ vendedores",
                        infoEmpty: "Nenhum registro disponível!",
                        infoFiltered: "",
                        paginate: {
                            first: "Primeiro",
                            last: "Último",
                            next: "Próximo",
                            previous: "Anterior"
                        }
                    }
                });

                $(".dataTables_length").hide();

                sellerStorage(sellersContent);

                if (refreshSellersList) {
                    refreshSellersList = false;

                    var buttons =
                        '<div class="sellers-buttons-action">' +
                        '<div role="group" class="btn-group">' +
                        '<a class="sellers-button-action notification"><i class="material-icons">notifications</i></a>' +
                        "</div>" +
                        "</div>";

                    $(".dataTables_filter").prepend(buttons);

                    $(".notification").on("click", function() {
                        var badges = "";

                        $(".token-group").html("");
                        $(".badges-group").html("");

                        var incSellers = 0;

                        $(".seller-chk").each(function() {
                            if ($(this).prop("checked")) {
                                incSellers++;

                                var index = $(this).attr("index");
                                var seller = getSeller(index, "name");
                                var firebaseToken = getSeller(
                                    index,
                                    "firebase_token"
                                );

                                var input =
                                    '<input type="checkbox" checked name="firebase_token[]" value="' +
                                    firebaseToken +
                                    '">';

                                if (firebaseToken !== null) {
                                    $(".token-group").append(input);

                                    badges =
                                        badges +
                                        '<span class="badge badge-info mr-2">' +
                                        seller +
                                        "</span>";
                                } else {
                                    badges =
                                        badges +
                                        '<span class="badge badge-secondary mr-2">' +
                                        seller +
                                        "</span>";
                                }
                            }
                        });

                        $(".badges-group").append(badges);

                        if (incSellers === 0) {
                            $.toast({
                                heading: "Atenção",
                                text: "Não existem vendedores selecionados!",
                                loader: true,
                                loaderBg: "#E99636",
                                hideAfter: 5000
                            });
                        } else {
                            $("#title").val("");
                            $("#message").val("");
                            $(".result-notification").html("");
                            $("#notification").modal();
                        }
                    });

                    var rowTableSeller;

                    $(tableSellers).on("click", ".select", function() {
                        rowTableSeller = sellers.row($(this).closest("tr"));

                        // store the seller details
                        rowSellerIndex = rowTableSeller.index();

                        localStorage.setItem(
                            "seller",
                            getSeller(rowSellerIndex)
                        );

                        clearInterval(intervalSellers);

                        $("#new-seller").modal();

                        // call function to fill in seller details
                        loadDetailsSeller();
                    });
                }
            } else {
                $.toast({
                    heading: "Atenção",
                    text: "Sua pesquisa não obteve resultado!",
                    loader: true,
                    loaderBg: "#E99636",
                    hideAfter: 5000
                });
            }
        }
    });
}

function valNewSeller() {
    // validate fields
    var name = $("#name").val() === "" ? null : $("#name").val();
    var user = $("#user").val() === "" ? null : $("#user").val();
    var password = $("#password").val() === "" ? null : $("#password").val();
    var cityId =
        typeof $("#city_id").val() === "undefined" ||
        $("#city_id").val().length === 0 ||
        $("#city_id").val()[0] === "0"
            ? null
            : $("#city_id").val();
    var recordingTime =
        $("#recording_time").val() === "" ? null : $("#recording_time").val();
    var sampleRate =
        $("#sample_rate").val() === "" ? null : $("#sample_rate").val();
    var bitsPerSample =
        $("#bits_per_sample").val() === "" ? null : $("#bits_per_sample").val();

    if (name === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo nome!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-basicdata").trigger("click");
        $("#name").focus();

        return false;
    } else if (user === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo usuário!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-basicdata").trigger("click");
        $("#user").focus();

        return false;
    } else if (password === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo senha!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-basicdata").trigger("click");
        $("#password").focus();

        return false;
    } else if (cityId === null) {
        $.toast({
            heading: "Atenção",
            text: "Selecione uma Cidade!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-settingsdata").trigger("click");
        $("#state_id").focus();

        return false;
    } else if (recordingTime === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo tempo de gravação!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-settingsdata").trigger("click");
        $("#recording_time").focus();

        return false;
    } else if (sampleRate === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo taxa de amostragem!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-settingsdata").trigger("click");
        $("#sample_rate").focus();

        return false;
    } else if (bitsPerSample === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo bits por amostra!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $(".tab-settingsdata").trigger("click");
        $("#bits_per_sample").focus();

        return false;
    }

    return true;
}

function valSendNotification() {
    // validate fields
    var title = $("#title").val() === "" ? null : $("#title").val();
    var message = $("#message").val() === "" ? null : $("#message").val();

    if (title === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo título!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#title").focus();

        return false;
    } else if (message === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo mensagem!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#message").focus();

        return false;
    }

    return true;
}
