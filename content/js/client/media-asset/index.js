$(function(){
    // Page status filter
    $('.usage-type-filter').click(function(event){
        event.preventDefault();
        var type = $(event.target).attr('rel');
        var link = '/client/media-asset/index/type/' + type;
        window.location = link;
    });

    // New file upload
    $('#media-asset-upload').fileupload({
        formData: {client:clientId, allowType:'png,jpg,gif,mp4,webm,ogv,svg,pdf'},
        dataType: 'json',
        url: '/client/ajax-media-asset/upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                $("#loader-info").html('');
                $("#overlay, #loader").hide();
                alert('Upload failed > error: ' + data.result.meta.error );
            } else {
                // Create the media asset directly
                $.ajax({
                    url: "/client/ajax-media-asset/create",
                    type: "POST",
                    data: {client: clientId, name: data.result.fileName, filepath: data.result.filePath, usageType: usageType},
                    dataType: "json"
                })
                    .done(function(data, textStatus){
                        $("#loader-info").html('');
                        $("#overlay, #loader").hide();
                        window.location.reload();
                    })
                    .fail(function(jqXHR, textStatus) {
                        $("#loader-info").html('');
                        $("#overlay, #loader").hide();
                        alert("Create media asset request failed: " + textStatus);
                    });
            }
        },
        progressall: function (e, data) {
            var progressPercentage = parseInt(100.0 * data.loaded / data.total);
            var message = 'uploading new asset... ' + progressPercentage + '% ';
            $("#loader-info").html(message);
            $("#overlay, #loader").show();
        }
    });

    $("thead tr th").off("mouseup");
    $("th").off("click");
    $("tr.off").off("click");

    // Click through the document items
    $('tr').bind('click', function(event){
        event.preventDefault();
        var url = '/client/media-asset/detail/id/' + $(this).attr('rel');
        window.location.href = url;
    });

    //list js search
    $(".table.table-striped.table-bordered tbody").addClass("list");
    $(".table.table-striped.table-bordered").after("<tr class='pagination'></tr>");

    var paginationOptions = {
        name: "paginationOptions",
        outerWindow: 4,
        innerWindow: 4
    };

    var options = {
        valueNames: [ 'medianame','preview','description','created_datetime','modified_datetime'],
        page: 5,
        plugins: [
            ListPagination(paginationOptions)
        ]
    };
    var mediaList = new List('media_list', options);

	$("tr.off").off("click");

    //sort buttons
    $('#alpha-sort').find('button').each( function() {
        $(this).click( function(event) {
            $(this).addClass('hide');
            $(this).siblings().removeClass('hide');
        });
    });

});