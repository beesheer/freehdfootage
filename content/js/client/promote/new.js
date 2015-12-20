$(function(){
    var baseAjaxUrl = '/client/ajax-promote';

    // Form submit.
    $('#create-submit').click(function(event){
        $('#client-promote-create-form').trigger('submit');
    });

    // Delete previous session
    $('.session-delete').click(function(event){
        var id = $(this).parents('tr').attr('rel');
        var self = $(this);
        $.ajax({
            url: baseAjaxUrl + "/delete-session",
            type: "POST",
            data: {id: id},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // All good
                self.parents('tr').remove();
            } else {
                alert('Delete session failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete session request failed: " + textStatus);
        });
    });
});