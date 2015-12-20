$(function(){
    $('#filepath').prop('disabled', 'disabled');
    $('#client').prop('disabled', 'disabled');

    // New file upload
    $('#fileUpload').fileupload({
        formData: {client: $('#client').val()},
        dataType: 'json',
        url: '/admin/ajax-document/upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Document upload faield: ' + data.result.meta.error);
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
        $('#client-document-form').trigger('submit');
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
            url: "/admin/ajax-document/delete",
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

    //tag management
    $(".checkbox").each( function () {
        $(this).click( function() {
            if ( $(this).attr("parent-id") !== ""  ) {
                if( $(this).hasClass("checked")) {
                    searchParents( $(this).attr("parent-id"), true )
                } else {
                    searchParents( $(this).attr("parent-id"), false );
                    deselectChildren( $(this).attr("tag-id") );
                }
            } else {
                if( !$(this).hasClass("checked")) {
                    deselectChildren( $(this).attr("tag-id") );
                }
            }
        });
    });

    function searchParents( id, enable ) {
        $("#document-tags").find(".checkbox").each(function () {
            if($(this).attr("tag-id") == id) {
                if( enable === true) {
                    $(this).addClass("checked");
                    searchParents( $(this).attr("parent-id"), true );
                }

            }
        })
    }

    function deselectChildren( id ) {
        $("#document-tags").find(".checkbox").each(function () {
            if( $(this).attr("parent-id") === id && $(this).hasClass("checked") ) {
                $(this).removeClass("checked");
                deselectChildren( $(this).attr("tag-id") )
            }
        });
    }


    $("#save-tags").click(function() {
        $checked = $.find(".checked");
        $selectedTags = [];
        for ( $item in $checked ) {
            //$selectedTags.push($($checked[$item]).attr("tag-id"));
            $selectedTags.push($($checked[$item]).attr("tag-name"));
        }
        $('#form-doc-tag-list').val( JSON.stringify($selectedTags));
        $('#form-document-tags').trigger('submit');
    });

});