$(function(){
    var hostPageId = $('#pageName').attr('rel');
    // Highlight the hosted page
    $('#page-group-pages li[rel=' + hostPageId + '] a').addClass('hostPage');

    // Connected sortable
    $("#client-pages").sortable({
        connectWith: ".connectedSortable",
        receive: function(ev, ui) {
            // Don't allow the current page to be dropped back to client pages.
            if (ui.item.attr('rel') == hostPageId) {
                ui.sender.sortable("cancel");
            }
        }
    }).disableSelection();

    $("#page-group-pages").sortable({
        connectWith: ".connectedSortable"
    }).disableSelection();



    $('#page-group-save-submit').click(function(event){
        $.ajax({
            url: "/admin/ajax/save-page-group-pages",
            type: "POST",
            data: $('#page-group-pages').sortable('serialize') + '&pageId=' + $('#pageName').attr('rel'),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                alert('Saved successfully.');
            } else {
                alert('Save linked pages failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save linked pages failed: " + textStatus);
        });
    });
});