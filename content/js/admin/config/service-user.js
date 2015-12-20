$(function(){
    // Create new service user
    $('.form-modal-submit').click(function(event){
        // Validation
        if ($('#email').val() == '') {
            alert('Please enter email');
            $('#email').parents('.form-group').addClass('has-error');
            return false;
        }
        if ($('#password').val() == '') {
            alert('Please enter password');
            $('#password').parents('.form-group').addClass('has-error');
            return false;
        }

        // Submit
        $.ajax({
            url: "/admin/ajax-config/create-service-user",
            type: "POST",
            data: $('#create-new-service-user-form').serialize(),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Create failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Create request failed: " + textStatus);
        });
    });

    // Create a new user
    $('#create-new-service-user').click(function(event){
        // Change label and set id to empty
        $('#formModalLabel').text('Create');
        $('#form-modal').modal('show');
    });
});