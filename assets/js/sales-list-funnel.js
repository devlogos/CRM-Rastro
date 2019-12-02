/*
 * list and funnel content
 */

// set variables for task control
var refreshSalesList = true;
var countSales = 0;
var sales = null;
var tableSales = $("#sales");
var intervalSales = null;
var rowSaleIndex = 0;
var indexOfResult = 0;
var status = statusId;

$("#search_status_id").val(0);

$("#details-sale").on("hidden.bs.modal", function() {
    var audio = document.getElementById("audioSale");

    if (audio !== null) {
        audio.pause();
    }

    //readSalesList();

    intervalSales = setInterval(function() {
        //readSalesList();
    }, 300000);
});

$(".filter-sales").on("click", function() {
    var dateInicial = $("#dateinitial").val();
    var dateFinal = $("#datefinal").val();
    var dates = "0";

    if (dateInicial !== "" && dateFinal !== "") {
        dates = dateInicial + "," + dateFinal;
    }

    statusId = $("#search_status_id")
        .val()
        .join(",")
        .replace("0,", "");

    sellerId = $("#search_seller_id").val();

    readSalesList(dates);

    intervalSales = setInterval(readSalesList, 300000, dates);
});

$(".reset-sales").on("click", function() {
    var dates = 0;
    sellerId = 0;
    statusId = 0;

    readSalesList(dates);
});

function readSalesList(dates) {
    var initFinalDates = typeof dates === "undefined" ? 0 : dates;

    $.ajax({
        type: "GET",
        url:
            domain +
            "/sales/" +
            companyId +
            "/" +
            sellerId +
            "/0/" +
            initFinalDates +
            "/" +
            statusId +
            "/" +
            sectorsId +
            "/1,132",
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
        success: function(salesContent) {
            $.toast().reset("all");

            if (salesContent !== null) {
                sales = $(tableSales).DataTable({
                    paging: true,
                    destroy: true,
                    lengthMenu: [25, 50, "Todos"],
                    data: salesContent,
                    columns: [
                        {
                            data: null,
                            defaultContent: ""
                        },
                        {
                            data: null,
                            defaultContent: ""
                        },
                        {
                            data: "seller_name"
                        },
                        {
                            data: "product_name"
                        },
                        {
                            data: "district_name"
                        },
                        {
                            data: "status_name"
                        },
                        {
                            data: null,
                            defaultContent: ""
                        }
                    ],
                    columnDefs: [
                        {
                            targets: 0,
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                var code =
                                    rowData.code === ""
                                        ? "INDEFINIDO"
                                        : rowData.code;
                                $(td).prepend(code);
                            }
                        },
                        {
                            targets: 1,
                            orderable: false,
                            createdCell: function(
                                td,
                                cellData,
                                rowData,
                                row,
                                col
                            ) {
                                var date = new Date(rowData.creation_date);
                                var day =
                                    '<span class="badge badge-warning badge-date-sale mr-1">' +
                                    ("00" + date.getDate()).slice(-2) +
                                    "</span>";
                                var month =
                                    '<span class="badge badge-secondary badge-date-sale mr-1 text-white">' +
                                    ("00" + (date.getMonth() + 1)).slice(-2) +
                                    "</span>";
                                var year =
                                    '<span class="badge badge-dark badge-date-sale text-white">' +
                                    date.getFullYear() +
                                    "</span>";

                                $(td).prepend(
                                    '<div class="no-wrap">' +
                                        day +
                                        month +
                                        year +
                                        "<div>"
                                );
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
                        zeroRecords: "Sua pesquisa não obteve resultado!!",
                        info: "Exibindo últimas _TOTAL_ vendas",
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

                countSales = $("#sales > tbody  > tr").length;

                saleStorage(salesContent);

                var status = "";

                status =
                    status +
                    '<div class="sectors-name d-none d-print-none d-sm-none d-md-none d-lg-block d-xl-block">';
                status =
                    status +
                    '<span class="badge badge-info">' +
                    sectorsName +
                    "</span>";
                status = status + "</div>";

                $(".dataTables_length").hide();
                $(".dataTables_filter").prepend(status);

                $(tableSales).on("page.dt", function() {
                    var info = sales.page.info();

                    countSales = info.end;
                });

                if (refreshSalesList) {
                    refreshSalesList = false;

                    var rowTableSale;

                    $(tableSales).on("click", ".select", function() {
                        rowTableSale = sales.row($(this).closest("tr"));

                        // updates all buttons to default
                        $(".selected").each(function() {
                            $(this).attr("class", "btn btn-info select");
                            $(this).html('<i class="icon-options"></i>');
                        });

                        // update current button context
                        $(this).attr("class", "btn btn-info select selected");
                        $(this).html('<i class="icon-note"></i>');

                        // store the sale details
                        rowSaleIndex = rowTableSale.index();

                        localStorage.setItem("sale", getSale(rowSaleIndex));

                        if (!getKey("read_full_sale")) {
                            if (getKey("read_sample_sale")) {
                                clearInterval(intervalSales);

                                $("#details-sale").modal({
                                    backdrop: "static",
                                    keyboard: false
                                });
                            }
                        } else {
                            clearInterval(intervalSales);

                            $("#details-sale").modal();
                        }

                        // call function to fill in sale details
                        loadDetailsSale();
                    });

                    $(".next-sale:not(.disabled)").on("click", function(e) {
                        e.preventDefault();

                        if (e.keyCode === 13) {
                            nextSale();
                        }

                        nextSale();
                    });

                    $(".btn.btn-info.select").click(function() {
                        $("i.owner_edit").attr("style", "visibility: inherit;");
                        $("select.owner_name").attr(
                            "style",
                            "visibility: hidden;"
                        );
                        $("i.status_edit").attr(
                            "style",
                            "visibility: inherit;"
                        );
                        $("select.status_name").attr(
                            "style",
                            "visibility: hidden;"
                        );
                    });
                }

                disableRows();
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

function readSalesFunnel(dates) {
    var funnel = $("#funnel-content");

    var newStatusId = JSON.parse("[" + statusId + "]");

    // create columns for status
    for (var key in newStatusId.sort()) {
        readFunnelStatus(funnel, newStatusId[key], newStatusId.length);
    }

    var initFinalDates = typeof dates === "undefined" ? 0 : dates;

    $.ajax({
        type: "GET",
        url:
            domain +
            "/sales/" +
            companyId +
            "/" +
            sellerId +
            "/0/" +
            initFinalDates +
            "/" +
            statusId +
            "/" +
            sectorsId +
            "/1,200",
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
        success: function(statusContent) {
            //localStorage.clear();
            localStorage.setItem("saleFunnel", JSON.stringify(statusContent));

            // set index for funnel arrangement data selection
            var index = 0;

            // insert data into specific columns
            for (var key in statusContent) {
                var element =
                    "#card-body-funnel-" + statusContent[key]["status_id"] + "";

                var strContent = null;

                var initialDate = new Date(statusContent[key]["creation_date"]);
                var finalDate = new Date();
                var timeDiff = Math.abs(
                    finalDate.getTime() - initialDate.getTime()
                );
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                var diffHours = Math.ceil(timeDiff / (1000 * 3600));
                var diffMinuts = Math.ceil(timeDiff / (1000 * 60));

                var newDate = null;

                if (diffDays > 1) {
                    newDate = "Há " + diffDays + " dias";
                } else {
                    if (diffHours === 1) {
                        if (diffMinuts === 1) {
                            newDate = "Agora";
                        } else if (diffMinuts === 60) {
                            newDate = "Há 1 hora";
                        } else {
                            newDate = "Há " + diffMinuts + " minutos";
                        }
                    } else {
                        newDate = "Há " + diffHours + " horas";
                    }
                }

                var code = statusContent[key]["code"];
                var product = statusContent[key]["product_name"];
                var sellerName = statusContent[key]["seller_name"];

                var pos = sellerName.indexOf(" ");
                var newSellerName = sellerName.substring(0, pos);

                var finishedClass = "";

                if (statusContent[key]["its_finished"] === true) {
                    finishedClass =
                        '<i style="' +
                        statusContent[key]["status_color"] +
                        '" class="material-icons">check_box</i>';
                }

                // add too many cards to main card
                strContent =
                    '<div class="card item-sale" index="' +
                    index +
                    '" id="card-item-funnel-' +
                    statusContent[key]["id"] +
                    '" saleid="' +
                    statusContent[key]["id"] +
                    '" draggable="true" ondragstart="dragSale(event)">' +
                    '<div class="card-body">' +
                    "<span>" +
                    finishedClass +
                    "</span>" +
                    '<h6 class="text-muted card-subtitle title-time">' +
                    newDate +
                    " por " +
                    newSellerName +
                    "</h6>" +
                    '<h6 class="card-title">' +
                    code.toUpperCase() +
                    "</h6>" +
                    '<h6 class="text-muted card-subtitle mb-2">' +
                    product +
                    "</h6>" +
                    "</div>" +
                    "</div>";

                $(element).append(strContent);

                index++;

                $("#card-item-funnel-" + statusContent[key]["id"]).on(
                    "click",
                    function() {
                        var result = jQuery.parseJSON(
                            localStorage.getItem("saleFunnel")
                        );
                        var indexResult = $(this).attr("index");

                        localStorage.setItem(
                            "sale",
                            JSON.stringify(result[parseInt(indexResult)])
                        );

                        indexOfResult = parseInt(indexResult);

                        $("#details-sale").modal("show");

                        // call function to fill in sale details
                        loadDetailsSale();
                    }
                );
            }
        }
    });

    window.addEventListener("resize", getWidthNav);

    getWidthNav();

    function getWidthNav() {
        var widthFunnel = Math.round(newStatusId.length * 321.5);

        $("#funnel-content").attr(
            "style",
            "width: " + widthFunnel + "px; padding-right: 5px !important;"
        );
    }
}

function readFunnelStatus(element, statusId, countStatus) {
    var newStatusId = typeof statusId === "undefined" ? 0 : statusId;

    $.ajax({
        type: "GET",
        url: domain + "/status/" + companyId + "/" + newStatusId,
        dataType: "json",
        beforeSend: function(xhr) {
            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(statusContent) {
            var strContent = null;

            // create main card
            strContent =
                '<div class="col-md-3 col-funnel">' +
                '<div class="card card-over" id="card-status-' +
                statusContent[1]["id"] +
                '">' +
                '<div class="card-header">' +
                '<div class="status-circle" style="background-color: ' +
                statusContent[1]["color"] +
                '"></div>' +
                '<h6 class="mb-0 upper">' +
                statusContent[1]["name"] +
                "</h6>" +
                "</div>" +
                '<div id="card-body-funnel-' +
                statusContent[1]["id"] +
                '" itsfinished="' +
                statusContent[1]["finished"] +
                '" statusid="' +
                statusContent[1]["id"] +
                '" ondrop="dropSale(event)" ondrop="dropSale(event)" ondragover="allowDropSale(event)" class="card-body card-container"></div>' +
                "</div>" +
                "</div>";

            element.append(strContent);
        }
    });
}

function wait(ms) {
    var start = new Date().getTime();
    var end = start;
    while (end < start + ms) {
        end = new Date().getTime();
    }
}

function allowDropSale(ev) {
    ev.preventDefault();

    $(".card-over").each(function() {
        $(this).attr("style", "border: none");
    });

    $(ev.target)
        .closest(".card-over")
        .attr("style", "border: dashed 4px #7b7b7b !important;");
}

function dragSale(ev) {
    ev.dataTransfer.setData("card", ev.target.id);
}

function dropSale(ev) {
    ev.preventDefault();

    var cardClass = $(ev.target).attr("class");

    // check if the container is correct
    if (cardClass === "card-body card-container") {
        var data = ev.dataTransfer.getData("card");
        var element = document.getElementById(data);
        var statusId = $(ev.target).attr("statusid");
        var saleId = $(element).attr("saleid");

        var itsFinished = $($(element).parent("div")).attr("itsfinished").bool;
        var itsFinishedCurr = $(ev.target).attr("itsfinished").bool;

        if (itsFinished !== true) {
            update(ev, element);
        } else {
            $.toast({
                heading: "Atenção",
                text: [
                    "Esta venda encontra-se cancelada!",
                    "Deseja prosseguir?",
                    '<button id="yesStatusSale" style="cursor:pointer" class="btn btn-info next-sale mr-1 mt-1" type="button">SIM</button><button id="notStatusSale" style="cursor:pointer" class="btn btn-danger next-sale  mt-1" type="button">NÃO</button>'
                ],
                position: "mid-center",
                loader: true,
                loaderBg: "#FF4D4D",
                hideAfter: 10000
            });

            $("#yesStatusSale").on("click", function() {
                $.toast().reset("all");

                update(ev, element);
            });

            $("#notStatusSale").on("click", function() {
                $.toast().reset("all");
            });

            return;
        }

        function update(ev, element) {
            $.ajax({
                type: "POST",
                url: "status/update/" + companyId,
                data: {
                    statusId: statusId,
                    saleId: saleId
                },
                dataType: "html",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("token", token);
                    xhr.setRequestHeader("key", secretKey);
                },
                success: function(content) {
                    if (parseInt(content) === 1) {
                        ev.target.appendChild(element);

                        if (itsFinishedCurr === true) {
                            $(element)
                                .contents()
                                .find("span")
                                .html('<i class="icon-check"></i>');
                        }

                        $.toast({
                            heading: "Atenção",
                            text: "Status alterado com sucesso!",
                            position: "mid-center",
                            loader: true,
                            loaderBg: "#ffc107",
                            hideAfter: 10000
                        });

                        readSalesFunnel();
                    }
                }
            });
        }
    }
}

function updateStatusSale(statusId, saleId, statusName, funnel) {
    var flagVal;
    var rowIndexTemp = 0;

    var srcFunnel = typeof funnel === "undefined" ? false : funnel;

    if (srcFunnel) {
        flagVal = true;

        rowIndexTemp = indexOfResult;
    } else {
        var row = sales.row($("button[dataid='" + saleId + "']").closest("tr"));

        flagVal = typeof row.index() !== "undefined" ? true : false;

        rowIndexTemp = row.index();
    }

    if (flagVal) {
        var code = getSale(rowIndexTemp, "code");
        var itsFinished = JSON.parse(getSale(rowIndexTemp, "its_finished"));
        var itsCancelled = JSON.parse(getSale(rowIndexTemp, "its_cancelled"));

        if (!getKey("read_full_sale")) {
            if (getKey("read_sample_sale")) {
                if (itsFinished) {
                    if (!srcFunnel) {
                        nextSale();
                    }

                    $.toast({
                        heading: "Atenção",
                        text: [
                            "<strong>" + code + "</strong>",
                            "Esta venda encontra-se finalizada!"
                        ],
                        position: "mid-center",
                        loader: true,
                        loaderBg: getSale(rowIndexTemp, "status_color")
                    });

                    return;
                }

                if (itsCancelled) {
                    if (!srcFunnel) {
                        nextSale();
                    }

                    $.toast({
                        heading: "Atenção",
                        text: [
                            "<strong>" + code + "</strong>",
                            "Esta venda encontra-se cancelada!"
                        ],
                        position: "mid-center",
                        loader: true,
                        loaderBg: getSale(rowIndexTemp, "status_color")
                    });

                    return;
                }
            }
        } else {
            if (itsCancelled) {
                $.toast({
                    heading: "Atenção",
                    text: [
                        "Esta venda encontra-se cancelada!",
                        'Deseja alterar seu status para <span style="text-transform: lowercase;">' +
                            statusName +
                            "</span>?",
                        '<button id="yesStatusSale" style="cursor:pointer" class="btn btn-info next-sale mr-1 mt-1" type="button">SIM</button><button id="notStatusSale" style="cursor:pointer" class="btn btn-danger next-sale  mt-1" type="button">NÃO</button>'
                    ],
                    position: "mid-center",
                    loader: true,
                    loaderBg: getSale(rowIndexTemp, "status_color"),
                    hideAfter: 10000
                });

                $("#yesStatusSale").on("click", function() {
                    $.toast().reset("all");

                    updateStatusSaleFinally(statusId, saleId, statusName);
                });

                $("#notStatusSale").on("click", function() {
                    $.toast().reset("all");

                    if (!srcFunnel) {
                        nextSale();
                    }
                });

                return;
            }
        }

        updateStatusSaleFinally(statusId, saleId, statusName);
    }
}

function updateStatusSaleFinally(statusId, saleId, statusName) {
    $.ajax({
        type: "POST",
        url: "status/update/" + companyId,
        data: {
            statusId: statusId,
            saleId: saleId
        },
        dataType: "html",
        beforeSend: function(xhr) {
            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(content) {
            if (parseInt(content) === 1) {
                hideRowToNext(statusName);
            }
        }
    });
}

function updateSectorSale(sectorId, saleId) {
    $.ajax({
        type: "POST",
        url: "sector/update/" + companyId,
        data: {
            sectorId: sectorId,
            saleId: saleId
        },
        dataType: "html",
        beforeSend: function(xhr) {
            xhr.setRequestHeader("token", token);
            xhr.setRequestHeader("key", secretKey);
        },
        success: function(content) {
            if (parseInt(content) === 1) {
                sectors = sectorsId.split(",");
                var foundSector = sectors.indexOf(sectorId.toString());

                if (foundSector === -1) {
                    console.log("remove");

                    var row = $(".selected").closest("tr");
                    $(row).hide();

                    nextSale();
                } else {
                    console.log("not remove");
                }
            }
        }
    });
}

function showOwnerEdit() {
    $("i.owner_edit").attr("style", "visibility: hidden;");
    $("select.owner_name").attr("style", "visibility: inherit;");
}

function showStatusEdit() {
    $("i.status_edit").attr("style", "visibility: hidden;");
    $("select.status_name").attr("style", "visibility: inherit;");
}

function nextSale() {
    $("#sector_name").val(0);
    $("#result-convert").html("");

    console.log(countSales);

    // identifies row of data table and increments to advance
    if (rowSaleIndex < countSales - 1) {
        rowSaleIndex++;

        localStorage.setItem("sale", getSale(rowSaleIndex));

        loadDetailsSale();

        // focus button according to current record
        var btnDetailsNext =
            "button[dataid='" + getSale(rowSaleIndex, "id") + "']";
        var btnDetailsPrevious =
            "button[dataid='" + getSale(rowSaleIndex - 1, "id") + "']";

        if (rowSaleIndex >= 1) {
            $(btnDetailsNext).attr("class", "btn btn-info select selected");
            $(btnDetailsNext).html('<i class="icon-note"></i>');

            $(btnDetailsPrevious).attr("class", "btn btn-info select");
            $(btnDetailsPrevious).html('<i class="icon-options"></i>');

            var windowHeight = $(window)
                .height()
                .toFixed(0);
            var offSet = $(btnDetailsNext).offset().top;

            if (offSet > windowHeight) {
                var $target = $("html,body");
                $target.animate(
                    {
                        scrollTop: offSet - 10
                    },
                    1000
                );
            }
        }
    }
}

function disableRows() {
    // if user does not have full access, disables lines
    if (!getKey("read_full_sale")) {
        if (getKey("read_sample_sale")) {
            $("#sales > tbody  > tr > td > button.select").each(function() {
                if ($(this).attr("dataid") !== getSale(0, "id")) {
                    $(this)
                        .closest("td")
                        .html(
                            '<button dataid="' +
                                $(this).attr("dataid") +
                                '" class="btn btn-info select-over disabled" type="button"><i class="icon-lock"></i></button>'
                        );
                }
            });
        } else {
            $("#sales > tbody  > tr > td > button.select").each(function() {
                $(this)
                    .closest("td")
                    .attr("style", "text-align: center;");
                $(this)
                    .closest("td")
                    .html('<i class="icon-lock no-access"></i>');
            });
        }
    }
}

function hideRowToNext(statusName) {
    // update record, hide line and advance
    var row = $(".selected").closest("tr");

    if (!getKey("read_full_sale")) {
        if (getKey("read_sample_sale")) {
            row.html("");
        }
    } else {
        row.children("tr td:nth-child(6)").html(statusName);
    }

    nextSale();
}

function clearFieldsNewSale() {
    $("#form-scheduled-sale")[0].reset();

    $("#seller_id")
        .val(0)
        .trigger("change");

    $("#category_id")
        .val(-1)
        .trigger("change");
    $(".products-content").html("");

    $("#state_id")
        .val(-1)
        .trigger("change");
    $(".cities-content").html("");
    $(".districts-content").html("");

    $("#client_id")
        .val(0)
        .trigger("change");
}

function valNewSale() {
    // validate fields
    var creationDate =
        $("#creation_date").val() === "" ? null : $("#creation_date").val();
    var code = $("#code").val() === "" ? null : $("#code").val();
    var productId =
        typeof $("#product_id").val() === "undefined"
            ? null
            : $("#product_id").val();
    var districtId =
        typeof $("#district_id").val() === "undefined"
            ? null
            : $("#district_id").val();
    var clientId =
        typeof $("#client_id").val() === "undefined"
            ? null
            : $("#client_id").val();

    if (creationDate === null) {
        $.toast({
            heading: "Atenção",
            text: "Selecione uma data para agendamento!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#creation_date").focus();

        return false;
    } else if (code === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo código!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#code").focus();

        return false;
    } else if (productId === null) {
        $.toast({
            heading: "Atenção",
            text: "Selecione um produto!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#category_id").focus();

        return false;
    } else if (districtId === null) {
        $.toast({
            heading: "Atenção",
            text: "Selecione um bairro!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#state_id").focus();

        return false;
    } else if (clientId === null) {
        $.toast({
            heading: "Atenção",
            text: "Selecione um cliente!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#client_id").focus();

        return false;
    }

    return true;
}
