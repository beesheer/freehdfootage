$(function(){
    $('#formModalLabel').text('Create New Title');
    $('#client').val($('#clientName').attr('rel'));
    // Create new title event from the select dropdown
    $('#title').bind('click', function(event){
        if ($(this).val() == 'new') {
            $('#client').prop('disabled', true);
            $('#form-modal').modal('show');
        }
    });

    // Create new title request
    $('.form-modal-submit').click(function(event){
        var titleName = $('#name').val();

        // Validation
        if (titleName == '') {
            alert('Please enter name');
            $('#name').parents('.form-group').addClass('has-error');
            return false;
        }

        // Enable client
        $('#client').prop('disabled', false);

        // Submit
        $.ajax({
            url: "/admin/ajax/create-title",
            type: "POST",
            data: $('#client-title-form').serialize(),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Append the new client to the dropdown and auto select it.
                $('#title').append('<option value="' + data.title + '" selected="selected">' + titleName + '</option>');
                $('#form-modal').modal('hide');
            } else {
                alert('Create title failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Create title request failed: " + textStatus);
        });
    });


    // Form submit.
    $('#upload-submit').click(function(event){
        $('#client-page-bulk-upload-form').trigger('submit');
    });
});