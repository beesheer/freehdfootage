$(function(){




    $("#asset-edit-submit").fileupload({
        formData: { client:clientId, description:$("#description").val(), id:$('#mediaAssetName').attr('rel'), 'name':$("#name").val(), allowType:'png,jpg,gif,mp4,webm,ogv,svg,pdf' },
        dataType: 'json',
        url: '/client/ajax-media-asset/update',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                $("#loader-info").html('');
                $("#overlay, #loader").hide();
                alert('Update failed:\r\n' + data.result.meta.error );
            } else {
                // Create the media asset directly
                console.log("worked: data = " + JSON.stringify( data.result.mediaAsset ));
                $("#loader-info").html('');
                $("#overlay, #loader").hide();
                window.location.reload();
            }
        },
        fail: function(e, data){
            console.log("Server error");
            $("#loader-info").html('');
            $("#overlay, #loader").hide();
        },
        progressall: function (e, data) {
            var progressPercentage = parseInt(100.0 * data.loaded / data.total);
            var message = 'uploading new asset... ' + progressPercentage + '% ';
            $("#loader-info").html(message);
            $("#overlay, #loader").show();
        }
    });

    // Form submit ajax.
    $("#asset-save-submit").click(function(event){
       $("#client-media-asset-form").trigger('submit');
    });


    // Delete clicked
    $('#asset-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete asset requested
    $('.delete-modal-submit').click(function(event){
        var assetId = $('#mediaAssetName').attr('rel');

        // Submit
        $.ajax({
            url: "/client/ajax-media-asset/delete",
            type: "POST",
            data: {id: assetId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete asset failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete asset request failed: " + textStatus);
        });
    });


});



