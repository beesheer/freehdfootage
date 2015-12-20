$(function(){
    // CKEditor
    CKEDITOR.replace('template',{
        allowedContent: true,
        filebrowserBrowseUrl: '/admin/media-asset'
    });

    // Save
    $('#save-submit').click(function(event){
        // First need to save tags first
        var tags = [];
        $('.tag-item').each(function(index, item){
            tags.push($(item).text());
        });

        tags = JSON.stringify(tags);
        $.ajax({
            url: '/admin/ajax-tag/entity',
            data: {client_id: $('#client').val(), entity_type: 'page_template', entity_id: $('#pageTemplateName').attr('rel'), tags: tags},
            dataType: 'json',
            type: 'POST',
            success: function(r) {
                $('#client-page-template-form').trigger('submit');
            }
        });
    });

    // Delete
    $('#delete').click(function(event){
        $('#delete-modal').modal('show');
    });

    // Delete request
    $('.delete-modal-submit').click(function(event){
        var id = $('#pageTemplateName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-page-template",
            type: "POST",
            data: {id: id},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete request failed: " + textStatus);
        });
    });

    // Publish
    $('#publish').click(function(event){
        // Change label and set id to empty
        $('#formModalLabel').text('Publish Page Template');
        $('#form-modal').modal('show');
    });

    // Publish request
    $('.form-modal-submit').click(function(event){
        var pageID = $('#page_id').val();
        var pageName = $('#page_name').val();

        // Validation
        if (pageID == '') {
            alert('Please enter Page ID');
            $('#page_id').parents('.form-group').addClass('has-error');
            return false;
        }
        if (pageName == '') {
            alert('Please enter Page Name');
            $('#page_name').parents('.form-group').addClass('has-error');
            return false;
        }

        // Submit
        $.ajax({
            url: "/admin/ajax-page-template/publish",
            type: "POST",
            data: $('#client-page-template-publish-form').serialize() + '&id=' + $('#pageTemplateName').attr('rel'),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Redirect to page
                window.location.href = '/admin/client/page-detail/id/' + data.page;
            } else {
                alert('Publish page template failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Publish page template request failed: " + textStatus);
        });
    });

    var clientId = $('#pageTemplateName').attr('client');

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

    //Client side thumbnail visibility button
    $('#btn-hideOnClientSide').click(function(event){
        $('#thumb_client_visibility').val( $(this).hasClass('checked') ? 0 : 1 )
    });

});