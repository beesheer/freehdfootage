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
        var selectedPackages = [];
        var selectedTeams = [];
        var selectedApps = [];
        $('.userPackages .checkbox.checked').each(function(index, ele){
            selectedPackages.push($(ele).parents('tr').attr('rel'));
        });
        $('.userTeams .checkbox.checked').each(function(index, ele){
            selectedTeams.push($(ele).parents('tr').attr('rel'));
        });
        $('.userApps .checkbox.checked').each(function(index, ele){
            selectedApps.push($(ele).parents('tr').attr('rel'));
        });
        $.ajax({
            url: "/admin/ajax/save-user-stuffs",
            type: "POST",
            data: {packages: selectedPackages, teams: selectedTeams, apps: selectedApps, userId: $('#userName').attr('rel')},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#create-new-user-form').trigger('submit');
            } else {
                alert('Save user stuffs failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save user stuffs request failed: " + textStatus);
        });

    });

    // Delete the user
    $('#user-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });
    
	
	$('#user-email-signature').click(function(event){
       // Change label and set id to empty
        $('#formModalLabel').text('Create/Edit Email Signature');
        $('#form-modal-email-signature').modal('show');
    });
	
	//submit
	$('.form-modal-submit').click(function (event) {
		// Validation
		if ($('#email_s').val() == '' && $('#position').val() == '' && $('#phone_nr').val() == '') 
		{
			alert('You must fill out at least one field');
			$('#email').parents('.form-group').addClass('has-error');
			$('#position').parents('.form-group').addClass('has-error');
			$('#phone_nr').parents('.form-group').addClass('has-error');
			return false;
		}
		// Submit
		$.ajax({
			url: "/admin/ajax/user-email-signature",
			type: "POST",
			data: $('#create-new-email-signature-form').serialize(),
			dataType: "json"
		})
		.done(function (data) 
		{
			if (data.meta.code == 200 && data.meta.error == '') 
			{
				window.location.reload();
			} 
			else 
			{
				alert('Create email signature failed: ' + data.meta.error);
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert("Create user request failed: " + textStatus);
		});
	})

});