/* 
 * definition of functions for users permissions
 */

function checkPermission() {
    // search for keys to set permissions

    document.createElement('validation');

    var tagPermission = document.getElementsByTagName('validation');

    for (var xInc = 0; xInc < tagPermission.length; xInc++) {

        var key = typeof $(tagPermission[xInc]).attr('key') === 'undefined' ? 'default' : $(tagPermission[xInc]).attr('key');
        var type = typeof $(tagPermission[xInc]).attr('type') === 'undefined' ? 'default' : $(tagPermission[xInc]).attr('type');

        if (key !== 'default') {
            setPermission(tagPermission[xInc], getKey(key), type);
        }
    }
}

function setPermission(obj, permission, type) {
    //set permission from stored key

    if (!permission) {
        $(obj).hide();

        if (type === 'default') {
            $(obj).find('a').removeAttr('href');
            $(obj).find('button').addClass('disabled');
            $(obj).find('button').removeAttr('data-dismiss');
        }
        else if (type === 'clear') {
            $(obj).html('');
        }
        else if (type === 'clearandfill') {
            $(obj).show();

            var image = $(obj).attr('src');

            $(obj).html("<img class=\"img-fluid d-flex flex-column mx-auto notfound\" style=\"max-width:400px;\" src=\"" + image + "\"/>");
        }
    }
}