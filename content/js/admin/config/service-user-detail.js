$(function(){
    // Delete user request
    $('.delete-modal-submit').click(function(event){
        var userId = $('#userName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-user",
            type: "POST",
            data: {id: userId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete user failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete user request failed: " + textStatus);
        });
    });


    // Edit the user
    $('#user-save-submit').click(function(event){
        $('#create-new-service-user-form').trigger('submit');
    });

    // Delete the user
    $('#user-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

});