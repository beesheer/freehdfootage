$(function(){
    // Create or edit client request
    $('.form-modal-submit').click(function(event){
        var clientName = $('#client_name').val();
        var clientType = $('#client_type').val();

        // Validation
        if (clientName == '') {
            alert('Please enter client name');
            $('#client_name').parents('.form-group').addClass('has-error');
            return false;
        }
        if (clientType == '') {
            alert('Please select client type');
            $('#client_type').parents('.form-group').addClass('has-error');
            return false;
        }

        // Submit
        $.ajax({
            url: "/admin/ajax/create-client",
            type: "POST",
            data: $('#create-new-client-form').serialize(),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Create/edit client failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Create/edit client request failed: " + textStatus);
        });
    });

    // Create a new client
    $('#create-new-client').click(function(event){
        // Change label and set id to empty
        $('#formModalLabel').text('Create New Client');
        $('#client_name').val('');
        $('#client_type').val('');
        $('#form-modal').modal('show');
    });
    
    /*
    Edit client has been moved to client's details page
    // Edit a client
    $('.edit-client').click(function(event){
        // Change label and set id to empty
        var $button = $(event.target);
        $('#formModalLabel').text('Edit Client');
        $('#client_id').val($button.parents('tr').attr('rel'));
        $('#client_name').val($button.parents('tr').find('td.client-name').text());
        $('#client_type').val($button.parents('tr').find('td.client-type').text());
        $('#form-modal').modal('show');
    });
    */
    
    // confirm to delete clients
    $('.delete-client').click(function(event){
        
        var clients = "";
        
        $(".select-client").each(function() {
            if ($(this).hasClass("checked")) {
                var clientName = $(this).parent().parent().children(".client-name").html();
                if (clients.length == 0)
                    clients += clientName;
                else
                    clients += ", " + clientName;
            }
        });
        
        if (clients.length > 0) {
            // confirm popup
            $('#delete-client-name').text(clients);
            $('#delete-modal').modal('show');
        }
    });
    
    // Delete clients request
    $('.delete-modal-submit').click(function(event){
        
        var total = 0;
        var done = 0;
        
        $(".select-client").each(function() {
            if ($(this).hasClass("checked")) {
                total++;
            }
        });
        
        $(".select-client").each(function() {
            if ($(this).hasClass("checked")) {
                var clientID = $(this).attr("clientID");
                // Submit
                $.ajax({
                    url: "/admin/ajax/delete-client",
                    type: "POST",
                    data: {id: clientID},
                    dataType: "json"
                })
                .done(function(data){
                    if (data.meta.code == 200 && data.meta.error == '') {
                        done++;
                        if (done == total)
                            window.location.reload();
                    } else {
                        alert('Delete client failed: ' + data.meta.error);
                    }
                })
                .fail(function(jqXHR, textStatus) {
                    alert("Delete client request failed: " + textStatus);
                });
            }
        });
    });
});