$(function(){
    var clientId = cookie.read("lifelearn-stratus-client");
    if (clientId == null || parseInt(clientId) == 0) {
        $('#uploadHandle').hide();
    } else {
        // Form modal preparation
        $('#client').val(clientId).prop('disabled','disabled');

        // New file upload
        $('#fileUpload').fileupload({
            formData: {client: clientId},
            dataType: 'json',
            url: '/admin/ajax-document/upload',
            done: function (e, data) {
                if (data.result.meta.error != '') {
                    alert('Document upload faield: ' + data.result.meta.error);
                } else {
                    // Populate the form
                    $('#formModalLabel').text('Create New Document');
                    $('#name').val(data.result.fileName);
                    $('#filepath').val(data.result.filePath).prop('disabled', 'disabled');
                    $('#form-modal').modal('show');
                }
            }
        });
    }

    // Create
    $('.form-modal-submit').click(function(event){
        var fileName = $('#name').val();
        var filePath = $('#filepath').val();

        // Validation
        if (fileName == '') {
            alert('Please enter name');
            $('#name').parents('.form-group').addClass('has-error');
            return false;
        }
        if (filePath == '') {
            alert('No file has been successfully uploaded yet. Please upload first.');
            return false;
        }

        // Enable so that we can get values.
        $('#filepath').removeAttr('disabled');
        $('#client').removeAttr('disabled');

        // Submit
        $.ajax({
            url: "/admin/ajax-document/create",
            type: "POST",
            data: $('#client-document-form').serialize(),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Create document failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Create document request failed: " + textStatus);
        });
    });

    // Click through the document items
    $('tr.noClickThrough').bind('click', function(event){
        event.preventDefault();
        var url = '/client/document/detail/id/' + $(this).attr('rel');
        window.location.href = url;
    });
});