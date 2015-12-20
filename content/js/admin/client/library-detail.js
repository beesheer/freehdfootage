$(function(){
    // Page status filter
    $('.library-status-filter').click(function(event){
        event.preventDefault();

        var clientId = parseInt($('#client-filter-default').attr('rel'));
        var status = $(event.target).attr('rel');
        var link = '/admin/client/library';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Client filter
    $('.library-client-filter').click(function(event){
        event.preventDefault();

        var clientId = $(event.target).attr('rel');
        var status = $('#status-filter-default').attr('rel');
        var link = '/admin/client/library';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Form submit.
    $('#library-save-submit').click(function(event){
        // First save title pages
        // Submit
        $.ajax({
            url: "/admin/ajax/save-library-pages",
            type: "POST",
            data: $('#library-pages').sortable('serialize') + '&library=' + $('#libraryName').attr('rel'),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-library-form').trigger('submit');
            } else {
                alert('Save library page failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save library page request failed: " + textStatus);
        });
    });

    // Delete the page
    $('#library-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete user request
    $('.delete-modal-submit').click(function(event){
        var libraryId = $('#libraryName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-library",
            type: "POST",
            data: {id: libraryId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete library failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete library request failed: " + textStatus);
        });
    });

    // Connected sortable
    $("#client-pages, #library-pages").sortable({
        connectWith: ".connectedSortable"
    }).disableSelection();
});