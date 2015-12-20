$(function(){
    // CKEditor
    CKEDITOR.replace('template',{
        allowedContent: true
    });

    // Save
    $('#save-submit').click(function(event){
        $('#client-pdf-template-form').trigger('submit');
    });

    // Delete
    $('#delete').click(function(event){
        $('#delete-modal').modal('show');
    });

    // Delete request
    $('.delete-modal-submit').click(function(event){
        var id = $('#pdfTemplateName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-pdf-template",
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
});