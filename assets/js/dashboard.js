console.info('Dashboard Initiated.');

const $ = require('jquery');
var InfiniteScroll = require('infinite-scroll');

var infScroll = new InfiniteScroll('#forms-content', {
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

$(window).on('scroll', () => {
    // @TODO This should be on last loaded time, not last saved ID.
    if (0 === $(window).scrollTop()) {
        $('.preloader-new-content').show();

        $firstSimulationId = $('.form-group').first().data('simulationid');

        $.ajax({
            url: '/simulation-forms/fresh/' + $firstSimulationId,
            dataType: 'html'
        }).done(function(data) {
            $('#forms-content').prepend($.parseHTML(data));
            $('.preloader-new-content').hide();
            $('.scroller-status').hide();
        });
    }
})

window.simulationSubmit = function() {
    this.event.preventDefault();
    let data = {};
    $(this.event.target).serializeArray().forEach((object)=>{
        data[object.name] = object.value;
    });
    console.log(data);

    //console.log(JSON.stringify(data));
    var simulationid = $(this.event.target).parents('.form-group').data('simulationid');
    $.ajax({
        url: '/simulation-forms/edit/' + simulationid,
        method: 'post',
        dataType: 'json',
        data: data
    }).done((data) => {
        console.log('Saved data response', data);
    });
}
