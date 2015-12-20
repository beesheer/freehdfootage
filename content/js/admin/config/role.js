$(function(){
    // Create or edit request
    $('.form-modal-submit').click(function(event){
        var form = $(event.target).parents('.modal-dialog').find('form');
        var formType = form.attr('formType');

        var name = $('#' + formType + '_name').val();
        var label = $('#' + formType + '_label').val();
        var description = $('#' + formType + '_description').val();

        // Validation
        if (name == '') {
            alert('Please enter name');
            $('#' + formType + '_name').parents('.form-group').addClass('has-error');
            return false;
        }
        if (label == '') {
            alert('Please enter label');
            $('#' + formType + '_label').parents('.form-group').addClass('has-error');
            return false;
        }

        // Submit
        $.ajax({
            url: "/admin/ajax-config/edit-role-permission",
            type: "POST",
            data: form.serialize() + '&type=' + formType,
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Edit failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Edit request failed: " + textStatus);
        });
    });

    // Create/Edit Role
    $('.edit-role').click(function(event){
        var id = $(event.target).attr('rel');
        var form = $('#role-form');
        if (typeof id == 'undefined') {
            // Create new
            $('#role-form-title').text('Create new role');
            form.find('[name=role_name]').val('');
            form.find('[name=role_label]').val('');
            form.find('[name=role_description]').val('');
            form.find('[name=role_id]').val('');
        } else {
            // Edit existing
            $('#role-form-title').text('Edit role');
            // Populate with existing details
            var details = JSON.parse($(event.target).attr('details'));
            form.find('[name=role_name]').val(details.name);
            form.find('[name=role_label]').val(details.label);
            form.find('[name=role_description]').val(details.description);
            form.find('[name=role_id]').val(details.id);
        }
        $('#role-form-modal').modal('show');
    });

    // Create/Edit Permission
    $('.edit-permission').click(function(event){
        var id = $(event.target).attr('rel');
        var form = $('#permission-form');
        if (typeof id == 'undefined') {
            // Create new
            $('#permission-form-title').text('Create new permission');
            form.find('[name=permission_name]').val('');
            form.find('[name=permission_label]').val('');
            form.find('[name=permission_description]').val('');
            form.find('[name=permission_id]').val('');
        } else {
            // Edit existing
            $('#permission-form-title').text('Edit permission');
            // Populate with existing details
            var details = JSON.parse($(event.target).attr('details'));
            form.find('[name=permission_name]').val(details.name);
            form.find('[name=permission_label]').val(details.label);
            form.find('[name=permission_description]').val(details.description);
            form.find('[name=permission_id]').val(details.id);
			form.find('[name=permission_name]').attr("readonly","readonly");
			form.find('[name=permission_label]').attr("readonly","readonly");
			form.find('[name=permission_description]').attr("readonly","readonly");
			$('.form-modal-submit').remove();
        }
        $('#permission-form-modal').modal('show');
    });

    // Activate role
    $('.activateRole').bind('click', function(){
        $('.activateRole').removeClass('success');
        $(this).addClass('success');

        $('input:checkbox').removeAttr('disabled');

        var roleId = $(this).attr('rel');
        // Get the permissions linked to this role
        $.ajax({
            url: "/admin/ajax-config/role-permission",
            type: "POST",
            data: {id: roleId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                $('input:checkbox').prop('checked', false);
                $.each(data.permissions, function(index, ele){
                    $('input:checkbox[value=' + parseInt(ele) + ']').prop('checked', true).parents('tr').show();
                });
                // Hide unchecked if it is show selected
                if ($('#showAllSwitcher').text() == 'Show All') {
                    // Show only checked
                    $('input:checkbox').each(function(index, ele){
                        if (!$(ele).is(':checked')) {
                            $(ele).parents('tr').hide();
                        }
                    });
                }
            } else {
                alert('Retrieve role permission failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Retrieve role permission request failed: " + textStatus);
        });
    });

    // Add/remove permission to role
    $('input:checkbox').bind('click', function(){
        var add = $(this).is(':checked');
        var roleId = $('tr.success').attr('rel');
        var permissionId = $(this).val();
        // Get the permissions linked to this role
        $.ajax({
            url: "/admin/ajax-config/add-remove-role-permission",
            type: "POST",
            data: {add: add ? 1 : 0, roleId: roleId, permissionId: permissionId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Don't have to do anything
            } else {
                alert('Add/remove role permission failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Add/remove permission request failed: " + textStatus);
        });
    });

    // Show selected/all switcher
    $('#showAllSwitcher').bind('click', function(){
         if (!$('tr.success').length) {
             return false;
         }
         var currentState = $(this).text();
         if (currentState == 'Show Selected') {
             $(this).text('Show All');
             // Show only checked
             $('input:checkbox').each(function(index, ele){
                 if (!$(ele).is(':checked')) {
                     $(ele).parents('tr').hide();
                 }
             });
         } else {
             $(this).text('Show Selected');
             $('input:checkbox').each(function(index, ele){
                 $(ele).parents('tr').show();
             });
         }
    });
});