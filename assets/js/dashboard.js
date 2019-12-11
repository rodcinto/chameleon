console.info('Dashboard Initiated.');

require('../css/forms.scss');

const $ = require('jquery');
const InfiniteScroll = require('infinite-scroll');

//@FIXME When there is only one block, it keeps making new ajax request infinitely.
const infScroll = new InfiniteScroll('#forms-content', {
    path: '.pagination__next',
    responseType: 'document',
    checkLastPage: true,
    status: '.page-load-status',
    history: false,
    prefill: true,
    append: '.form-group',
    debug: false,
    hideNav: '.pagination'
});
infScroll.on( 'append', ( event, response, path, items ) => {
    $('.preloader-new-content').hide();
});

const getCurrentTime = function() {
    return  Math.floor(new Date().getTime() / 1000);
}

let timestampLastLoaded = getCurrentTime();

$(window).on('scroll', () => {
    if (0 === $(window).scrollTop()) {
        $('.preloader-new-content').show();

        $.ajax({
            url: '/simulation-forms/fresh/' + timestampLastLoaded,
            dataType: 'html'
        }).done(function(data) {
            $('#forms-content').prepend($.parseHTML(data));
            $('.preloader-new-content').hide();
            $('.scroller-status').hide();

            timestampLastLoaded = getCurrentTime();
        });
    }
})

window.simulationSubmit = function(simulationId) {
    this.event.preventDefault();
    let data = {};
    $(this.event.target).serializeArray().forEach((object)=>{
        data[object.name] = object.value;
    });
    console.log('Serialized data', data);

    if (simulationId === undefined) {
        console.error('Couldn\'t find simulationId value.');
        return;
    }

    $.ajax({
        url: '/simulation-forms/edit/' + simulationId,
        method: 'post',
        dataType: 'json',
        data: data
    }).done((data) => {
        console.log('Saved data response', data);
    });
}

window.deleteSimulation = function(simulationId) {

    console.log('SimulationId to delete: ' + simulationId);

    $('.preloader-new-content').show();

    $.ajax({
        url: '/simulation-forms/delete/' + simulationId,
        method: 'post'
    }).done(function(data, textStatus, xhr) {
        if (xhr.status === 200) {
            $('.simulation-form-' + simulationId).remove();
        }
        $('.preloader-new-content').hide();
    });
}