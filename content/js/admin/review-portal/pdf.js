$(function(){
    $('#create-new-pdf').click(function(event){
        event.preventDefault();
        $('#messageModal').modal('show');
        // PDF generation request
        $.ajax({
            url: "/admin/ajax-review-portal/new-pdf",
            type: "POST",
            data: 'portal=' + $('#portalName').attr('rel'),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Generate new pdf for review portal failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Generate new pdf for review portal request failed: " + textStatus);
        });
    });

    // Check all
    $('#checkAll').bind('click', function(event){
        if ($(this).is(':checked')) {
            $('.checkOne').prop('checked', 'checked');
        } else {
            $('.checkOne').removeAttr('checked');
        }
    });

    // Save page settings
    $('#save-page-settings').click(function(event){
        event.preventDefault();

        var pageSettings = [];
        $('.checkOne').each(function(index, ele){
            var pageId = $(ele).parents('tr').attr('rel');
            pageSettings.push({page: pageId, type: $(ele).is(':checked') ? 2 : 1});
        });

        $.ajax({
            url: "/admin/ajax-review-portal/screenshot-settings",
            type: "POST",
            data: {pageSettings: pageSettings},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Save settings failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save settings request failed: " + textStatus);
        });
    });
});