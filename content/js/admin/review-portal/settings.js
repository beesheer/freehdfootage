$(function(){
    // Check all
    $('#checkAll').bind('click', function(event){
        if ($(this).hasClass('checked')) {
            $('.checkbox').removeClass('checked');
        } else {
            $('.checkbox').removeClass('checked');
            $('.checkbox').addClass('checked');
        }
    });

    // Save page settings
    $('#save-page-settings').click(function(event){
        event.preventDefault();

        var pageSettings = [];
        $('.checkbox:not("#checkAll")').each(function(index, ele){
            var pageId = $(ele).parents('tr').attr('rel');
            pageSettings.push({page: pageId, type: $(ele).hasClass('checked') ? 2 : 1});
        });

        $.ajax({
            url: "/admin/ajax-review-portal/screenshot-view-settings",
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