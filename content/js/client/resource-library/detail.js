$(function(){
    $('#filepath').prop('disabled', 'disabled');
    $('#client').prop('disabled', 'disabled');

    // Delete request
    $('.delete-modal-submit').click(function(event){
        var id = $('#objectName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax-document/delete",
            type: "POST",
            data: {id: id},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete request failed: " + textStatus);
        });
    });
});