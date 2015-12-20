$(function(){
    $('#filepath').prop('disabled', 'disabled');
    $('#client').prop('disabled', 'disabled');

    // New file upload
    $('#fileUpload').fileupload({
        formData: {client: $('#client').val()},
        dataType: 'json',
        url: '/admin/ajax-media-asset/upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Upload faield: ' + data.result.meta.error);
            } else {
                // Populate the form
                $('#name').val(data.result.fileName);
                $('#filepath').val(data.result.filePath).prop('disabled', 'disabled');
            }
        }
    });

    // Create
    $('#doc-save-submit').click(function(event){
        $('#filepath').removeAttr('disabled');
        $('#client').removeAttr('disabled');
        $('#client-media-asset-form').trigger('submit');
    });

    // Delete
    $('#doc-delete').click(function(event){
        $('#delete-modal').modal('show');
    });

    // Delete request
    $('.delete-modal-submit').click(function(event){
        var id = $('#objectName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax-media-asset/delete",
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

    //init the tag management code
    function setTagManager() {
        TAGMANAGER = null;
        TAGMANAGER = new TagManager( setTagManager );
    }
    setTagManager();

});

var TAGMANAGER;