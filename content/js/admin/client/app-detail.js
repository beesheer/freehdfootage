$(function(){
    // Form submit.
    $('#app-save-submit').click(function(event){
        // First save contents.
        // Submit

        var selectedPackages = [];
        var selectedUsers = [];

        $('.appPackages .checked').each(function(index, ele){
            selectedPackages.push($(ele).parents('tr').attr('rel'));
        });
        $('.appUsers .checkbox.checked').each(function(index, ele){
            selectedUsers.push($(ele).parents('tr').attr('rel'));
        });
        $.ajax({
            url: "/admin/ajax/save-app-contents",
            type: "POST",
            data: {packages: selectedPackages, users: selectedUsers, appId: $('#appName').attr('rel')},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-app-form').trigger('submit');
            } else {
                alert('Save app package and user failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save app package and user failed: " + textStatus);
        });
    });

    // Delete the team
    $('#app-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete team request
    $('.delete-modal-submit').click(function(event){
        var teamId = $('#appName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-app",
            type: "POST",
            data: {id: teamId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete app failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete app request failed: " + textStatus);
        });
    });
});