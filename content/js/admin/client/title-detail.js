$(function(){
    // Page status filter
    $('.title-status-filter').click(function(event){
        event.preventDefault();

        var clientId = parseInt($('#client-filter-default').attr('rel'));
        var status = $(event.target).attr('rel');
        var link = '/admin/client/title';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Client filter
    $('.title-client-filter').click(function(event){
        event.preventDefault();

        var clientId = $(event.target).attr('rel');
        var status = $('#status-filter-default').attr('rel');
        var link = '/admin/client/title';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Form submit.
    $('#title-save-submit').click(function(event){
        // First save title pages
        // Submit
        $.ajax({
            url: "/admin/ajax/save-title-pages",
            type: "POST",
            data: $('#title-pages').sortable('serialize') + '&title=' + $('#titleName').attr('rel'),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-title-form').trigger('submit');
            } else {
                alert('Save title page failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save title page request failed: " + textStatus);
        });
    });

    // Delete the page
    $('#title-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete user request
    $('.delete-modal-submit').click(function(event){
        var titleId = $('#titleName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-title",
            type: "POST",
            data: {id: titleId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete title failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete title request failed: " + textStatus);
        });
    });

    // Connected sortable
    $("#client-pages, #title-pages").sortable({
        connectWith: ".connectedSortable"
    }).disableSelection();

    // Menu link if needed
    if ($('#nav_type').val() == 'tree') {
        $('[for=nav_type]').append(' <a href="/admin/client/title-menu/id/' + $('#titleName').attr('rel') + '" target="_blank">Set Up Menu</a>');
    }

    // Form modal preparation
    var clientId = $('#client').val();

    // New file upload
    $('#fileUpload').fileupload({
        formData: {client: clientId, allowType: 'png,jpg'},
        dataType: 'json',
        url: '/admin/ajax-media-asset/upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Upload failed: ' + data.result.meta.error);
            } else {
                // Create the media asset directly
                $.ajax({
                    url: "/admin/ajax-media-asset/create",
                    type: "POST",
                    data: {client: clientId, name: data.result.fileName, filepath: data.result.filePath},
                    dataType: "json"
                })
                .done(function(data){
                    $('#media_asset_id').val(data.id);
                    $('#filepath-image-preview').html(data.preview);
                })
                .fail(function(jqXHR, textStatus) {
                    alert("Create media asset request failed: " + textStatus);
                });
            }
        }
    });

    // Clone the title
    $('#title-clone').click(function(event){
        var titleId = $('#titleName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/clone-title",
            type: "POST",
            data: {id: titleId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                var newTitleId = data.title.id;
                window.location.href = '/admin/client/title-detail/id/' + newTitleId;
            } else {
                alert('Clone title failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Clone title request failed: " + textStatus);
        });
    });

    //Client side thumbnail visibility button
    $('#btn-hideOnClientSide').click(function(event){
       $('#thumb_client_visibility').val( $(this).hasClass('checked') ? 0 : 1 )
    });

});