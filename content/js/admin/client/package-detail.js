$(function(){
    // Package status filter
    $('.package-status-filter').click(function(event){
        event.preventDefault();

        var clientId = parseInt($('#client-filter-default').attr('rel'));
        var status = $(event.target).attr('rel');
        var link = '/admin/client/package';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Client filter
    $('.package-client-filter').click(function(event){
        event.preventDefault();

        var clientId = $(event.target).attr('rel');
        var status = $('#status-filter-default').attr('rel');
        var link = '/admin/client/package';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Form submit.
    $('#package-save-submit').click(function(event){
        // First save package titles & libraries
        // Submit

        var selectedTitles = [];
        var selectedApps = [];
        $('.packageTitles .checkbox.checked').each(function(index, ele){
            selectedTitles.push($(ele).parents('tr').attr('rel'));
        });
        $('.packageApps .checkbox.checked').each(function(index, ele){
            selectedApps.push($(ele).parents('tr').attr('rel'));
        });
        $.ajax({
            url: "/admin/ajax/save-package-contents",
            type: "POST",
            data: {titles: selectedTitles, apps: selectedApps, packageId: $('#packageName').attr('rel')},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-package-form').trigger('submit');
            } else {
                alert('Save package contents failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save package contents request failed: " + textStatus);
        });
    });

    // Delete the package
    $('#package-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete user request
    $('.delete-modal-submit').click(function(event){
        var packageId = $('#packageName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-package",
            type: "POST",
            data: {id: packageId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete package failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete package request failed: " + textStatus);
        });
    });

    // Generate new manifest
    $('#new-manifest').click(function(event){
        var packageId = $('#packageName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/new-package-production-manifest",
            type: "POST",
            data: {id: packageId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Generate manifest failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Generate manifest request failed: " + textStatus);
        });
    });

    // Form modal preparation
    var clientId = $('#client').val();

    // New file upload
    $('#fileUpload').fileupload({
        formData: {client: clientId, allowType: 'png,jpg'},
        dataType: 'json',
        url: '/admin/ajax-media-asset/upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Upload faield: ' + data.result.meta.error);
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

    // Menu link if needed
    if ($('#nav_type').val() == 'tree') {
        $('[for=nav_type]').append('&nbsp;<a href="/admin/client/package-nav/id/' + $('#packageName').attr('rel') + '" target="_blank">Set Up Navigation</a>');
    }
    
    //Client side thumbnail visibility button
    $('#btn-hideOnClientSide').click(function(event){
        $('#thumb_client_visibility').val( $(this).hasClass('checked') ? 0 : 1 )
    });
    
});