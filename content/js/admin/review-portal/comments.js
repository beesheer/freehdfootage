$(function(){
    // Add new thread
    $('.edit-thread').bind('click', function(){
        var threadContent = $(this).parent().parent().parent().find('.edit-thread-content').val();
        var id = $(this).attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax-review-portal/update-thread",
            type: "POST",
            data: {id: id, threadContent: threadContent},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Update portal page thread failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Update portal page thread request failed: " + textStatus);
        });
    });

    // Update thread.
    $('.add-new-thread').bind('click', function(){
        var threadContent = $(this).parent().parent().parent().find('.new-thread').val();
        var parentId = $(this).attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax-review-portal/new-thread",
            type: "POST",
            data: {portalPageId: $(this).parents('tr').attr('ppid'), threadContent: threadContent, parentId: parentId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Save portal page thread failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save portal page thread request failed: " + textStatus);
        });
    });

    // Update thread is_approve status
    $('.comment-approved').bind('click', function(){
        var status = $(this).is(':checked') ? 1 : 0;
        var threadId = $(this).attr('rel');
        $.ajax({
            url: "/admin/ajax-review-portal/update-thread-status",
            type: "POST",
            data: {id: threadId, status: status},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // All good
            } else {
                alert('Save thread status failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save thread status request failed: " + textStatus);
        });
    });

    // Reply handles
    $('.reply-handle').bind('click', function(event){
        event.preventDefault();
        $('.' + $(this).attr('rel')).toggle().find('textarea').focus();
    });

    // Reply cancel
    $('.edit-thread-cancel').bind('click', function(event){
        event.preventDefault();
        $(this).parent().parent().parent().hide();
    });

    // Edit handles
    $('.edit-handle').bind('click', function(event){
        event.preventDefault();
        $('.' + $(this).attr('rel')).toggle().find('textarea').focus();
    });

    // Edit cancel
    $('.add-new-thread-cancel').bind('click', function(event){
        event.preventDefault();
        $(this).parent().parent().parent().hide();
    });
});