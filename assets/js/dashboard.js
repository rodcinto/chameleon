console.info('Dashboard Initiated.');

require('../css/forms.scss');

const $ = require('jquery');
const InfiniteScroll = require('infinite-scroll');
const Message = require('./Message.js');

const infScroll = new InfiniteScroll('#forms-content', {
    path: '.pagination__next',
    responseType: 'document',
    checkLastPage: true,
    status: '.scroller-status',
    history: false,
    prefill: true,
    append: '.form-group',
    debug: false,
    hideNav: '.pagination'
});
infScroll.on( 'append', ( event, response, path, items ) => {
    $('.preloader-new-content').hide();
});
infScroll.on( 'last.infiniteScroll', function( event, response, path ) {
    console.log( 'LAST Loaded: ' + path );
    new Message().flash('Last results.', 'secondary');
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

    $('.preloader-new-content').show();

    $.ajax({
        url: '/simulation-forms/edit/' + simulationId,
        method: 'post',
        dataType: 'json',
        data: data
    }).done((data) => {
        $('.preloader-new-content').hide();
        new Message().flash('Simulation updated.', 'success');
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
        new Message().flash('Simulation deleted.', 'info');
    });
}

window.exportSimulation = function(simulationId) {
    // @TODO This function is sloppy.
    console.log('SimulationId export: ' + simulationId);
    $('.preloader-new-content').show();

    $.ajax({
        url: '/simulation-forms/export/' + simulationId,
    }).done((data, textStatus, xhr) => {
        if (xhr.status === 200 && data !== '') {
            var textArea = document.createElement("textarea");
            textArea.value = data;
            textArea.style.position="fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            var copied = document.execCommand('copy');

            if (copied) {
                console.log('Copied', data);
                new Message().flash('Entry #' + simulationId + ' copied into your Clipboard (CTRL+C).', 'info');
            } else {
                new Message().flash('Sorry, an error occurred.', 'danger');
            }
        }
        $('.preloader-new-content').hide();
    }).fail(() => {
        $('.preloader-new-content').hide();
        new Message().flash('Sorry, an error occurred', 'danger');
    });
}

$('.btn-import-save').click(function() {
    var importData = $('#import_data').val();

    if ('' === importData) {
        return;
    }

    //@TODO Validate data with JSON.parse()

    $('.preloader-new-content').show();
    $('#modal_import').modal('hide');

    $.ajax({
        url: '/simulation-forms/import/',
        method: 'post',
        data: {
            import_data: importData
        }
    }).done(function(data, textStatus, xhr) {
        if (xhr.status === 200) {
            console.log('IMPORT SUCCESS');
            new Message().flash('New Entry imported. Make sure your filters are OK.', 'info');
        } else {
            new Message().flash('Sorry, couldn\'t import it.', 'warning');
        }
    }).fail(() => {
        new Message().flash('Sorry, an error occurred', 'danger');
    }).always(() => {
        $('.preloader-new-content').hide();
        $('#import_data').val('');
    });
});
