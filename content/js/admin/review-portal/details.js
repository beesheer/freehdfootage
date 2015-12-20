$(function(){
    // Client title filter for the client pages.
    $('.client-title-filter').click(function(){
        $('#title-filter-default').text($(this).text());
        var titleId = parseInt($(this).attr('rel'));
        if (titleId > 0) {
            var pages = JSON.parse($(this).attr('pages'));
            $('#client-pages li').hide();
            $.each(pages, function(index, page){
                $('#client-pages li[rel="' + page.id + '"]').show();
            });
        } else {
            // Show all pages
            $('#client-pages li').show();
        }
    });

    $(".page-list-item").click(function() {
        $(this).children(".checkbox").toggleClass("checked");
    });

    // multiple dragging
    // shift select
    $("#pages .checkbox, #pages .page-list-item").click(function(event) {
        if (event.shiftKey) {
            // find the first one on top
            var firstID = "";
            $("#pages .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (firstID == "" || $(this).offset().top < $("#" + firstID).offset().top)
                        firstID = $(this).attr("id");
                }
            });
            if (firstID == "")
               firstID = $(this).attr("id");
            // find the last one at bottom
            var lastID = "";
            $("#pages .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (lastID == "" || $(this).offset().top > $("#" + lastID).offset().top) {
                        if ($(this).attr("id") != firstID)
                            lastID = $(this).attr("id");
                    }
                }
            });
            if (lastID == "")
               lastID = $(this).attr("id");
            $("#pages .page-list-item").each(function() {
                if ($(this).offset().top >= $("#" + firstID).offset().top && $(this).offset().top <= $("#" + lastID).offset().top)
                    $(this).children(".checkbox").addClass("checked");
            });
        }
    });

    $("#portal-pages .checkbox, #portal-pages .page-list-item").click(function(event) {
        if (event.shiftKey) {
            // find the first one on top
            var firstID = "";
            $("#portal-pages .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (firstID == "" || $(this).offset().top < $("#" + firstID).offset().top)
                        firstID = $(this).attr("id");
                }
            });
            if (firstID == "")
               firstID = $(this).attr("id");
            // find the last one at bottom
            var lastID = "";
            $("#portal-pages .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (lastID == "" || $(this).offset().top > $("#" + lastID).offset().top) {
                        if ($(this).attr("id") != firstID)
                            lastID = $(this).attr("id");
                    }
                }
            });
            if (lastID == "")
               lastID = $(this).attr("id");
            $("#portal-pages .page-list-item").each(function() {
                if ($(this).offset().top >= $("#" + firstID).offset().top && $(this).offset().top <= $("#" + lastID).offset().top)
                    $(this).children(".checkbox").addClass("checked");
            });
        }
    });

    $(".page-list-item").mouseup(function(event) {
        $(window).unbind("mousemove");
        $(".ui-sortable-helper").css({
            "position"  : "relative",
            "left"      : "",
            "top"       : "",
            "z-index"   : ""
        });
        $(".sortable-placeholder").after($(".ui-sortable-helper"));
        // reset all checkboxes after release
        if ($(event.target).hasClass("ui-sortable-helper")) {
            $(".ui-sortable-helper").removeClass("ui-sortable-helper");
            $(".page-list-item .checkbox").removeClass("checked");
        }
    });

    $(".page-list-item").mousedown(function() {

        if ($(this).parent().parent().parent().attr("id") == "pages")
            $("#portal-pages .checkbox").removeClass("checked");
        else  if ($(this).parent().parent().parent().attr("id") == "portal-pages")
            $("#pages .checkbox").removeClass("checked");

        var width = $("#pages").width();

        $(window).mousemove(function(event) {

            var id = parseInt($(event.target).attr("rel"));
            var top = parseInt($(event.target).css("top"));
            var left = parseInt($(event.target).css("left"));
            var checked = {};

            $(".page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    checked[$(this).attr("rel")] = "";
                }
            });

            $(".page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked") && $(this).attr("rel") != id) {
                    $(this).addClass("ui-sortable-helper");
                    $(this).css({
                        "position"  : "absolute",
                        "left"      : left + "px",
                        "top"       : ($(this).offset().top - $("#pages").offset().top) + "px",
                        "width"     : width + "px",
                        "z-index"   : 1000
                    });
                    // keep items connected when moving
                     var idDiff = $(this).attr("rel") - id;
                    if (idDiff > 0) {
                        for (var i=id+1; i<$(this).attr("rel"); i++) {
                            if (checked[i] == null) {
                                idDiff = i - id;
                                checked[i] = "";
                                break;
                            }
                        }
                    } else if (idDiff < 0) {
                        for (var i=id-1; i>$(this).attr("rel"); i--) {
                            if (checked[i] == null) {
                                idDiff = i - id;
                                checked[i] = "";
                                break;
                            }
                        }
                    }
                    var topDiff = top + (100 * idDiff);
                    $(this).css({
                        "left"  : left + "px",
                        "top"   : topDiff + "px"
                    });
                }
            });
        });
    });


    // Form submit.
    $('.portal-save-submit').click(function(event){
        // First save portal pages and users
        var selectedUsers = [];
        $('.portalUsers .checked').each(function(index, ele){
            selectedUsers.push($(ele).parents('tr').attr('rel'));
        });

        // Submit
        $.ajax({
            url: "/admin/ajax-review-portal/save-review-portal-contents",
            type: "POST",
            data: $('#portal-pages').sortable('serialize') + '&' + $('#portal-docs').sortable('serialize') + '&portal=' + $('#portalName').attr('rel') + '&user=' + JSON.stringify(selectedUsers),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-portal-form').trigger('submit');
            } else {
                alert('Save portal page failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save portal page request failed: " + textStatus);
        });
    });

    // Connected sortable
    $("#client-pages, #portal-pages").sortable({
        connectWith: ".connectedSortable",
        placeholder: "sortable-placeholder"
    }).disableSelection();

    // Docs
    $("#client-docs, #portal-docs").sortable({
        connectWith: ".connectedSortableDocs"
    }).disableSelection();
});