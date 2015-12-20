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
            url: "/admin/ajax-review-portal/new-page-status",
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
            url: "/admin/ajax-review-portal/update-page-note",
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
        //var threadContent = $(this).parent().parent().parent().find('.edit-thread-content').val();
        var id = $(this).attr('rel');
        var threadContent = $(".thread-update-"+id+"-body").text().trim();
        // Submit
        $.ajax({
            url: "/admin/ajax-review-portal/update-thread",
            type: "POST",
            data: {id: id, threadContent: threadContent},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                $('.edit-thread-content').val('');
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
            url: "/admin/ajax-review-portal/new-thread",
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
            url: "/admin/ajax-review-portal/update-thread-status",
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

    // Page preview switcher
    if ($('.previewScreenshots').length > 0) {
        $('.previewSwitcher').show();

        // Restore the state of page switcher
        var key = 'page_switcher_state_' + $('#portalPageName').attr('rel');

        if ($.cookie(key) == 1 || (typeof $.cookie(key) == 'undefined' && useStaticScreenshots == 1)) {
            // Should show slider first
            $('.previewIframe').hide();
            $('.previewScreenshots').show();
            $('.previewSwitcher').text('View Actual Page');
        }
    }

    $('.previewSwitcher').bind('click', function(event){
        var key = 'page_switcher_state_' + $('#portalPageName').attr('rel');

        event.preventDefault();
        if ($('.previewIframe:visible').length > 0) {
            $('.previewIframe').hide();
            $('.previewScreenshots').show();
            $('.previewSwitcher').text('View Actual Page');

            $.cookie(key, 1);

        } else {
            $('.previewIframe').show();
            $('.previewScreenshots').hide();
            $('.previewSwitcher').text('View Screenshots');

            $.cookie(key, 0);
        }
    });

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
            window.open("/admin/review-portal/pages/id/" + rpID);
    	else (pageID > 0)
    		window.open("/admin/review-portal/pages/id/" + rpID + "/page/" + pageID, "_self");
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
            window.open("/admin/review-portal/pages/id/" + rpID + "/page/" + currentPageID, "_self");
        else (pageID > 0)
            window.open("/admin/review-portal/pages/id/" + rpID + "/page/" + pageID, "_self");
    	
    });

    // Add new thread
    $('.delete-handle').bind('click', function(){
        var id = $(this).attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax-review-portal/delete-thread",
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

    /**
     * Delete review portal
     */
    $('#portal-delete').bind('click', function(e){
        $('#delete-modal').modal('show');
    });

    // Delete user request
    $('.delete-modal-submit').click(function(event){
        var portalId = $('#portal-delete').attr('rel');
        deletePortal(portalId);
    });

    /**
     * Delete a review portal.
     *
     * @param integer portalId
     */
    function deletePortal(portalId)
    {
        $.ajax({
            url: "/admin/ajax-review-portal/delete",
            type: "POST",
            data: {id: portalId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // All good
                window.location.href = '/admin/review-portal';
            } else {
                alert('Delete portal failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete portal request failed: " + textStatus);
        });
    }


});

function changePageStatus(el)
{
    var key = $(el).parent().attr('rel');
    var label = $(el).text();
    $('#status-label').attr('rel',key);
    $('#status-label').text(label);
    $('.dropdown-menu').hide();
}