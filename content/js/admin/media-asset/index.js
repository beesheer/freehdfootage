$(function(){
    // Page status filter
    $('.usage-type-filter').click(function(event){
        event.preventDefault();
        var type = $(event.target).attr('rel');
        var link = '/admin/media-asset/index/type/' + type;
        window.location = link;
    });

    var clientId = cookie.read("lifelearn-stratus-client");
    if (clientId == null || parseInt(clientId) == 0) {
        $('#uploadHandle').hide();
    } else {
        // Form modal preparation
        $('#client').val(clientId).prop('disabled','disabled');

        // New file upload
        $('#fileUpload').fileupload({
            formData: {client: clientId},
            dataType: 'json',
            url: '/admin/ajax-media-asset/upload',
            done: function (e, data) {
                if (data.result.meta.error != '') {
                    alert('Upload faield: ' + data.result.meta.error);
                } else {
                    // Populate the form
                    $('#formModalLabel').text('Create New ' + typeTitle);
                    $('#name').val(data.result.fileName);
                    $('#filepath').val(data.result.filePath).prop('disabled', 'disabled');
                    $('#form-modal').modal('show');
                }
            }
        });
    }

    // Create
    $('.form-modal-submit').click(function(event){
        var fileName = $('#name').val();
        var filePath = $('#filepath').val();

        // Validation
        if (fileName == '') {
            alert('Please enter name');
            $('#name').parents('.form-group').addClass('has-error');
            return false;
        }
        if (filePath == '') {
            alert('No file has been successfully uploaded yet. Please upload first.');
            return false;
        }

        // Enable so that we can get values.
        $('#filepath').removeAttr('disabled');
        $('#client').removeAttr('disabled');

        // Submit
        $.ajax({
            url: "/admin/ajax-media-asset/create",
            type: "POST",
            data: $('#client-media-asset-form').serialize() + '&usage_type=' + usageType,
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Create failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Create request failed: " + textStatus);
        });
    });

    $("thead tr th").off("mouseup");
    $("th").off("click");
    $("tr.off").off("click");
    // Click through the document items
    $('tbody tr.noClickThrough').bind('click', function(event){
        event.preventDefault();
        var url = '/admin/media-asset/detail/id/' + $(this).attr('rel');
        window.location.href = url;
    });

    /**
     * CKEditor Stuffs
     *
     * @param paramName
     */
    // Helper function to get parameters from the query string.
    function getUrlParam( paramName ) {
        var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
        var match = window.location.search.match(reParam) ;
        return ( match && match.length > 1 ) ? match[ 1 ] : null ;
    }
    var funcNum = getUrlParam( 'CKEditorFuncNum' );

    $('td.selectMedia').bind('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        var fileUrl = $(this).attr('rel');
        window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
    });

    //list js search
    $(".table.table-striped.table-bordered tbody").addClass("list");
    $(".table.table-striped.table-bordered").after("<tr class='pagination'></tr>");

    var paginationOptions = {
        name: "paginationOptions",
        outerWindow: 5,
        innerWindow: 5
    };

    var options = {
        valueNames: [ 'name', 'created', 'modified', 'desc' ],
        page: 5,
        plugins: [
            ListPagination(paginationOptions)
        ]
    };
    var mediaList = new List('media_list', options);

    //sort buttons
    $('#alpha-sort').find('button').each( function() {
        $(this).click( function(event) {
            $(this).addClass('hide');
            $(this).siblings().removeClass('hide');
        })
    })

});