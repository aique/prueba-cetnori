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
    .done(function(cars) {
        printServiceResponse(cars)
    })
    .fail(function() {
        showError('Service unavailable');
    });
}

function printServiceResponse(cars) {
    var responseLayout = $('#rest-service-response');

    responseLayout.html('');

    var table = ($('<table></table>').addClass('table'));

    table.append(createCarTableHeader());
    table.append(createCarTableBody(cars));

    responseLayout.append(table);
}

function createCarTableHeader() {
    return $('<thead></thead>')
            .append($('<tr></tr>')
                .append($('<th></th>').html('Plate'))
                .append($('<th></th>').html('Color'))
                .append($('<th></th>').html('Kilometers'))
                .append($('<th></th>').html('Owner'))
                .append($('<th></th>').html('Picture'))
            );
}

function createCarTableBody(cars) {
    var tbody = $('<tbody></tbody>');

    for (var i = 0 ; i < cars.length ; i++) {
        tbody.append(createCarRow(cars[i]));
    }

    return tbody;
}

function createCarRow(car) {
    return $('<tr></tr>')
        .append($('<td></td>').html(car.plate))
        .append($('<td></td>').html(car.color))
        .append($('<td></td>').html(car.kilometers))
        .append($('<td></td>').html(car.owner))
        .append($('<td></td>').html(getImage(car.picture)));
}

function getImage(url) {
    var imageLink = $('<a></a>')
        .attr('href', url)
        .attr('target', '_blank');

    var image = $('<img />')
        .attr('src', url)
        .attr('height', 50);

    imageLink.append(image);

    return imageLink;
}

function showError(errorMsg) {
    var responseLayout = $('#rest-service-response');

    responseLayout.html('');
    responseLayout.append($('<p></p>')
        .addClass("alert alert-danger")
        .html(errorMsg));
}