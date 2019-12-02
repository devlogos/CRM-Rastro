/*
 * clients list
 */

function readClientsList() {}

function clearFieldsNewClient() {
    $("#form-add-client")[0].reset();
}

function valNewClient() {
    // validate fields
    var name = $("#name").val() === "" ? null : $("#name").val();

    if (name === null) {
        $.toast({
            heading: "Atenção",
            text: "Preencha o campo nome!",
            loader: true,
            loaderBg: "#E99636",
            hideAfter: 5000
        });

        $("#name").focus();

        return false;
    }

    return true;
}
