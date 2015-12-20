$(function(){
    // Form submit.
    $('#team-save-submit').click(function(event){
        // First save team packages & users.
        // Submit

        var selectedPackages = [];
        var selectedUsers = [];
        var selectedTeams = [];
        $('.teamPackages .checked').each(function(index, ele){
            selectedPackages.push($(ele).parents('tr').attr('rel'));
        });

        $('.teamUsers .checked').each(function(index, ele){
            selectedUsers.push($(ele).parents('tr').attr('rel'));
        });
        $('.teamTeams .checked').each(function(index, ele){
            selectedTeams.push($(ele).parents('tr').attr('rel'));
        });


        $.ajax({
            url: "/admin/ajax/save-team-contents",
            type: "POST",
            data: {packages: selectedPackages, users: selectedUsers, teams: selectedTeams, teamId: $('#teamName').attr('rel')},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-team-form').trigger('submit');
            } else {
                alert('Save team package and user failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save team package and user failed: " + textStatus);
        });
    });

    // Delete the team
    $('#team-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete team request
    $('.delete-modal-submit').click(function(event){
        var teamId = $('#teamName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-team",
            type: "POST",
            data: {id: teamId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete team failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete team request failed: " + textStatus);
        });
    });
});