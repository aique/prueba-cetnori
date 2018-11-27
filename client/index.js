var _config;

$(function() {
    showCarsList();
});

function showCarsList() {
    parseConfigFile();
}

function parseConfigFile() {
    $.getJSON('config.json', function(configJsonData) {
        _config = configJsonData;
        makeServiceCall();
    });
}

function makeServiceCall() {
    $.ajax({
        method: 'GET',
        url: _config.url,
        contentType: 'application/json'
    })
    .done(function() {
        console.log("done");
    })
    .fail(function() {
        showError('Service unavailable');
    });
}

function showError(errorMsg) {
    var responseLayout = $('#rest-service-response');

    responseLayout.append($('<p></p>')
        .addClass("alert alert-danger")
        .html(errorMsg));
}