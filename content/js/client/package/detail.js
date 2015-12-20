$(function(){
    // Disable all of the controls
    $('input').attr('disabled',true);
    $('select').attr('disabled',true);
    $('textarea').attr('disabled',true);
    $('#uploadHandle').remove();

    // Menu link if needed
    if ($('#nav_type').val() == 'tree') {
        $('[for=nav_type]').append('&nbsp;<a href="/client/package/nav/id/' + $('#packageName').attr('rel') + '" target="_blank">Set Up Navigation</a>');
    }

    // Form submit.
    $('#package-titles-save-submit').click(function(event){
        // Save package titles
        var selectedTitles = [];
        $('.packageTitles .checkbox.checked').each(function(index, ele){
            selectedTitles.push($(ele).parents('tr').attr('rel'));
        });
        $.ajax({
            url: "/client/ajax/save-package-titles",
            type: "POST",
            data: {titles: selectedTitles, packageId: $('#packageName').attr('rel')},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                alert('Successfully saved.');
            } else {
                alert('Save package titles failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save package titles request failed: " + textStatus);
        });
    });

    //increase checkbox click area
    $(".packageTitles .checkbox").each( function() {
        $(this).parent().click( function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).find(".checkbox").trigger("click");
        })
    });

    // Generate new manifest
    $('#new-manifest').click(function(event){
        var packageId = $('#packageName').attr('rel');
        // Submit
        $.ajax({
            url: "/client/ajax/new-package-production-manifest",
            type: "POST",
            data: {id: packageId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Generate manifest failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Generate manifest request failed: " + textStatus);
        });
    });
});