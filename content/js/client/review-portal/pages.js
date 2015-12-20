$(function(){
    // Restore collapse
    $('.collapse-handle').each(function(index, ele){
        var key = 'collapse_state_' + $(ele).attr('rel') + '_' + $('#portalPageName').attr('rel');
        var currentState = $.cookie(key);
        if (currentState == 1) {
            $(ele).addClass('collapsed');
            $($(ele).attr('href')).removeClass('in').addClass('collapse');
        }
    });


    $('#portal-nav-details').remove();
    $('#portal-nav-pdf').remove();
    var baseAjaxUrl = '/client/ajax-review-portal';
    // Change status
    $('.changeStatus').bind('click', function(){
        var key = $(this).attr('rel');
        var label = $(this).find('a').text();
        $('#status-label').attr('rel',key);
        $('#status-label').text(label);
    });

    // Add new status
    $('#add-new-status').bind('click', function(){
        var key = parseInt($('#status-label').attr('rel'));
        if (key == 0) {
            alert('Please select a status first.');
            return false;
        }
        // Submit
        $.ajax({
            url: baseAjaxUrl + "/new-page-status",
            type: "POST",
            data: {portalPageId: $('#portalPageName').attr('rel'), status: key},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Save portal page failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save portal page status request failed: " + textStatus);
        });
    });

    // Update slide note
    $('#update-note').bind('click', function(){
        var note = $('#update-note-content').val();
        // Submit
        $.ajax({
            url: baseAjaxUrl + "/update-page-note",
            type: "POST",
            data: {portalPageId: $('#portalPageName').attr('rel'), note: note},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Save portal page note failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save portal page note request failed: " + textStatus);
        });
    });

    // Add new thread
    $('.edit-thread').bind('click', function(){        
        var id = $(this).attr('rel');
        var threadContent = $(".thread-update-"+id+"-body").text().trim();
        // Submit
        $.ajax({
            url: baseAjaxUrl + "/update-thread",
            type: "POST",
            data: {id: id, threadContent: threadContent},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                $('.new-thread').val('');
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
            url: baseAjaxUrl + "/new-thread",
            type: "POST",
            data: {portalPageId: $('#portalPageName').attr('rel'), threadContent: threadContent, parentId: parentId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                $('.new-thread').val('');
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
            url: baseAjaxUrl + "/update-thread-status",
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
        //$(this).parent().parent().parent().hide();
        $('.thread-update-' + $(this).attr('rel')+"-action").css("display","none");
        $('.thread-update-' + $(this).attr('rel')+"-body").attr("contenteditable",false);
    });

    // Edit handles
    $('.edit-handle').bind('click', function(event){
        event.preventDefault();
        //$('.' + $(this).attr('rel')).toggle().find('textarea').focus();
        $('.' + $(this).attr('rel')+"-action").show();
        $('.' + $(this).attr('rel')+"-body").attr("contenteditable",true).focus();
    });

    // Edit cancel
    $('.add-new-thread-cancel').bind('click', function(event){
        event.preventDefault();
        $(this).parent().parent().parent().hide();
    });

    // Collapse status
    $('.collapse-handle').bind('click', function(event){
        var key = 'collapse_state_' + $(this).attr('rel') + '_' + $('#portalPageName').attr('rel');
        if ($(this).hasClass('collapsed')) {
            $.cookie(key, 0);
        } else {
            $.cookie(key, 1);
        }
    });

    // Client side behavior. JIRA:STRAT-23
    if ($('.previewScreenshots').length > 0 && useStaticScreenshots) {
        $('.previewIframe').hide();
        $('.previewScreenshots').show();
    } else {
        $('.previewIframe').show();
        $('.previewScreenshots').hide();
    }

    /**
     * Prevent un-submitted comment.
     *
     */
    $(window).bind('beforeunload', function(event){
        var hasComment = false;
        $('.new-thread').each(function(index, ele){
            if ($(ele).val() != '') {
                hasComment = true;
            }
        })
        if (hasComment) {
            return 'There are comments not submitted yet.';
        }
    });


     // prev/next buttons
    $("#prevPage").click(function() {
        var url = document.URL;
        // get review portal ID
        var rpID = 1;
        var i = url.indexOf("id/") + 3;
            rpID = parseInt(url.substr(i));
        // get page ID
        /*var pageID = 1;
        i = url.indexOf("page/");
        if (i >= 0) {
            i += 5;
            pageID = parseInt(url.substr(i));
        } else {
            pageID = 1;
        }
        pageID--;*/
        var pageID = $("#prevPageId").val();
        if(pageID == "")
            window.open("/client/review-portal/pages/id/" + rpID);
        else (pageID > 0)
            window.open("/client/review-portal/pages/id/" + rpID + "/page/" + pageID, "_self");
    });

    $("#nextPage").click(function() {
        var url = document.URL;
        // get review portal ID
        var rpID = 1;
        var i = url.indexOf("id/") + 3;
            rpID = parseInt(url.substr(i));
        // get page ID
        /*var pageID = 1;
        i = url.indexOf("page/");
        if (i >= 0) {
            i += 5;
            pageID = parseInt(url.substr(i));
        } else {
            pageID = 1;
        }
        pageID++;*/
        var currentPageID = $("#currentPageId").val();
        var pageID = $("#nextPageId").val();
        if(pageID == "")
            window.open("/client/review-portal/pages/id/" + rpID + "/page/" + currentPageID, "_self");
        else (pageID > 0)
            window.open("/client/review-portal/pages/id/" + rpID + "/page/" + pageID, "_self");
        
    });


    // Add new thread
    $('.delete-handle').bind('click', function(){
        var id = $(this).attr('rel');
        // Submit
        $.ajax({
            url: baseAjaxUrl + "/delete-thread",
            type: "POST",
            data: {id: id},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete portal page thread failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete portal page thread request failed: " + textStatus);
        });
    });


});