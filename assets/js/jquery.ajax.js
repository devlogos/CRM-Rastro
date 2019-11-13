/*!
 * Functions for get and post content using ajax
 * Requires jQuery v1.5 or later 
 */

var string = 'Aguarde...';

// function for get content
function getContent(content, url) {
    $.ajax({
        type: 'GET',
        url: url,
        async: true,
        beforeSend: function () {
            $(content).html(string);
        },
        success: function (data) {
            $(content).html(data);
        },
        error: function () {
            $(content).html(string);
        }
    });
}

// function for post content
function postContent(content, form, headers) {
    var sendHeaders = typeof headers === 'undefined' ? false : headers;
        
    $(document).ready(function () {
        $(form).ajaxForm({
            beforeSend: function (xhr) {
                if (sendHeaders) {
                    xhr.setRequestHeader('token', token);
                    xhr.setRequestHeader('key', secretKey);
                }
            },
            uploadProgress: function () {
                $(content).html(string);
            },
            success: function () {
                $(content).html(string);
            },
            complete: function (data) {
                $(content).html(data.responseText);
            },
            error: function () {
                $(content).html(string);
            }
        });
    });
}