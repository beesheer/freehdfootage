var dark = false;

function COOKIE() {
}

COOKIE.prototype.create = function (name, value, days) {

	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else {
		var expires = "";
	}

	document.cookie = name + "=" + value + expires + "; path=/";
}

COOKIE.prototype.read = function (name) {

	var nameEQ = name + "=";
	var ca = document.cookie.split(';');

	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
			c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0)
			return c.substring(nameEQ.length,c.length);
	}

	return null;
}

COOKIE.prototype.erase = function (name) {
	this.create(name, "", -1);
}

var cookie = new COOKIE();




// stratus dark
//document.styleSheets[4].disabled = true;
// analytics dark
//document.styleSheets[6].disabled = true;
if (cookie.read("lifelearn-stratus-theme")) {
    if (cookie.read("lifelearn-stratus-theme") == 1) {
        document.styleSheets[4].disabled = false;
        document.styleSheets[6].disabled = false;
        dark = true;
    }
}


function showSidebar(){
    $("#sidebar").removeClass("sidebar-closed");
    $("#sidebar").addClass("sidebar-open");
    $(".sidebar-title").fadeIn();
    $(".sidebar-toggle-left").fadeIn();
    $(".sidebar-toggle-right").fadeOut();

    $("#sidebar-toggle").removeClass("sidebar-toggle-closed");
    $("#sidebar-toggle").addClass("sidebar-toggle-open");

    $("#container-body").removeClass("container-body-closed");
    $("#container-body").addClass("container-body-open");

    cookie.create("lifelearn-stratus-sidebar", 1, 7);

    setTimeout( function(){
        $(window).trigger('resize');
    }, 250 );
}

function hideSidebar(){
    $("#sidebar").removeClass("sidebar-open");
    $("#sidebar").addClass("sidebar-closed");
    $(".sidebar-title").fadeOut();
    $(".sidebar-toggle-left").fadeOut();
    $(".sidebar-toggle-right").fadeIn();

    $("#sidebar-toggle").removeClass("sidebar-toggle-open");
    $("#sidebar-toggle").addClass("sidebar-toggle-closed");

    $("#container-body").removeClass("container-body-open");
    $("#container-body").addClass("container-body-closed");

    cookie.create("lifelearn-stratus-sidebar", 0, 7);

    setTimeout( function(){
        $(window).trigger('resize');
    }, 250 );
}

function resizeReviewPortal(){
    var width = $("#pagePreview").width();


    var scale = (width / 1024);

    console.log("PAGe PREVIEw WIDTH = "+width+"  scale=  "+scale);

    $("#review-portal-preview-iframe").css(
        {
            "webkit-transform":"scale("+scale+")",
            "transform":"scale("+scale+")",
            "webkit-transform-origin":"0px 0px",
            "transform-origin":"0px 0px",

        });

    $("#pagePreview").height( scale * 768 + 50 );

    $("#panel-preview").height( scale * 768 + 50 );
}


$(function() {


    // Create default console object for IE
    if (!window.console) console = {log: function() {}};

    $("#container-body").removeClass("app-transition-ease");
    $("#sidebar").removeClass("app-transition-ease");
    if (cookie.read("lifelearn-stratus-sidebar")) {
        if (cookie.read("lifelearn-stratus-sidebar") == 0) {
            hideSidebar();
            $(".sidebar-title").hide();
        } else {
            showSidebar();
            $(".sidebar-title").show();
        }
    }
    $("#container-body").addClass("app-transition-ease");
    $("#sidebar").addClass("app-transition-ease");

    $("#logo").click(function() {
        window.location.replace("/");
    });

    $("#overlay").click(function() {
        $("#overlay").hide();
        $(".popup").fadeOut(200);
    });

    $(".menu").click(function(event) {
        //event.stopPropagation();
        //window.location.replace($(this).attr("href"));
    });

    $("input, button:not(.btn-default)").click(function(event) {
        event.stopPropagation();
    });

    $(".sidebar.list").click(function(event) {
        event.stopPropagation();
        window.location.replace($(this).attr("href"));
    });

    if (document.URL.indexOf("/analytics") > 0) {
        $("#sidebar-analytics").addClass("active");
    } else if (document.URL.indexOf("/review-portal") > 0) {
        $("#sidebar-portal").addClass("active");
    } else if (document.URL.indexOf("/index/type/document") > 0) {
        $("#sidebar-document").addClass("active");
    } else if (document.URL.indexOf("/media-asset") > 0){
        $("#sidebar-media-asset").addClass("active");
    } else if (document.URL.indexOf("/promote") > 0) {
        $("#sidebar-promote").addClass("active");
    } else if (document.URL.indexOf("/meeting") > 0) {
        $("#sidebar-meeting").addClass("active");
    } else if (document.URL.indexOf("/contact") > 0) {
        $("#sidebar-contact").addClass("active");
    } else if (document.URL.indexOf("/my-presentations") > 0) {
        $("#sidebar-my-presentations").addClass("active");
    } else if (document.URL.indexOf("/result") > 0) {
        $("#sidebar-result").addClass("active");
    } else if (document.URL.indexOf("/page") > 0) {
        $("#sidebar-page").addClass("active");
    } else if (document.URL.indexOf("/title") > 0) {
        $("#sidebar-title").addClass("active");
    } else if (document.URL.indexOf("/package") > 0) {
        $("#sidebar-package").addClass("active");
    }



    $("#menu-settings").click(function(event) {
        event.stopPropagation();
        if ($("#menu-config-dropdown").css("display") == "none")
            $("#menu-config-dropdown").stop().slideDown();
        else
            $("#menu-config-dropdown").stop().slideUp();
    });

    $("#sidebar-toggle").click(function(event) {
        if ($("#sidebar").hasClass("sidebar-open")){
            hideSidebar();
        } else {
            showSidebar();
        }
    });

    $("#menu-logout").click(function(event) {
        event.stopPropagation();
        //cookie.erase("lifelearn-stratus-client");
        //cookie.erase("lifelearn-stratus-theme");
        //cookie.erase("lifelearn-stratus-sidebar");
        window.location.replace("/logout");
    });

    // click a row in a table goes to its details
    $("tr").click(function() {

        // Not all tables need this, for example, the comments table in review portal.
        if ($(this).hasClass('noClickThrough')) {
            return true;
        }

        var id = $(this).attr("rel");

        var url = "/client";

        if (document.URL.indexOf("/review-portal") > 0) {
            url += "/review-portal/pages/id/" + id;
        }
        if (document.URL.indexOf("/result") > 0) {
            url += "/result/detail/id/" + id;
        }
        if (document.URL.indexOf("/package") > 0) {
            url += "/package/detail/id/" + id;
        }
        if (document.URL.indexOf("/media-asset") > 0) {
            url += "/media-asset/detail/id/" + id;
        }
        if (document.URL.indexOf("/title") > 0) {
            url += "/title/detail/id/" + id;
        }
        if (document.URL.indexOf("/page") > 0) {
            url += "/page/detail/id/" + id;
        }
        window.location.replace(url);
    });

    // sidebar
    $("#sidebar-control").click(function() {
        if ($("#sidebar").width() > 100) {
            // min sidebar
            cookie.create("lifelearn-stratus-sidebar", 0, 7);
            $("#sidebar").stop().animate({
                "width": "40px"
            }, function() {
                $("#sidebar-control-icon").removeClass("sidebar-control-min");
                $("#sidebar-control-icon").addClass("sidebar-control-max");
            });
            $("#container-body, #container-footer").stop().animate({
                "margin-left": "40px"
            });
        } else {
            // max sidebar
            cookie.create("lifelearn-stratus-sidebar", 1, 7);
            $("#sidebar").stop().animate({
                "width": "200px"
            }, function() {
                $("#sidebar-control-icon").removeClass("sidebar-control-max");
                $("#sidebar-control-icon").addClass("sidebar-control-min");
            });
            $("#container-body, #container-footer").stop().animate({
                "margin-left": "200px"
            });
        }
    });

    // checkbox
    $(".checkbox").click(function(event) {
        event.stopPropagation();
        $(this).toggleClass("checked");
    });
    $(".deletebox").click(function(event) {
        event.stopPropagation();
        $(this).toggleClass("checked");
    });

    // hide review portal nav bar
    $("#portal-nav-portals, #portal-nav-pages, #portal-nav-comments, #portal-nav-pdf, #portal-nav-details, #portal-nav-docs, #portal-nav-settings").hide();
    if (document.URL.indexOf("/review-portal/pages") > 0 || document.URL.indexOf("/review-portal/documents") > 0) {
        $("#portal-nav-portals, #portal-nav-pages, #portal-nav-docs").show();
    }

    if (document.URL.indexOf("/review-portal/pages") > 0 || document.URL.indexOf("/review-portal/documents") > 0) {

        console.log("REVIEW PORTAL");

        $(window).resize( resizeReviewPortal );

        resizeReviewPortal();

    }

    // review portal
    /*
    if (document.URL.indexOf("/review-portal/pages") > 0 || document.URL.indexOf("/review-portal/documents") > 0) {

        $("#container-body").css({
            "overflow"  : "visible"
        });
        $("#container-body .container.row").css({
            "overflow": "visible"
        });

        if ($(window).width() >= 992) {

            $("#col-main").css({
                "max-width" : "1024px"
            });

            // width of preview, status, slide notes, comments
            var width = $(window).width() - $("#sidebar").width() - 20 - 140;
            if (width > 1024)
                width = 1024;
            $("#panel-preview, #panel-status, #panel-notes, #panel-comments").css({
                "width"     : width + "px",
                "max-width" : "1024px",
                "overflow"  : "hidden"
            });
            // height of preview
            var height = width * 768 / 1024;
            $("#pagePreview").css({
                "height"    : height + "px"
            });
            // scale iframe
            var ratio = width / 1024;
            if (ratio < 1) {
                var left = -(1024 - width) / 2;
                var top = -(768 - height) / 2;
                $("iframe").css({
                    "transform"     : "scale(" + ratio + ", " + ratio + ")",
                    "margin-left"   : left + "px",
                    "margin-top"    : top + "px"
                });
            }
            // thumbnails
            $("#col-thumbnail").css({
                "left": (20 + width + 10) + "px",
            });

        } else {

            $("#container-body").css({
                "margin-left": "0px"
            });

            $("#col-main").css({
                "left"      : "0px",
                "max-width" : "1024px"
            });
            // width of preview, status, slide notes, comments
            var width = $(window).width() - 20 - 30 - 110;
            if (width > 1024)
                width = 1024;
            $("#panel-preview, #panel-status, #panel-notes, #panel-comments").css({
                "width"     : width + "px",
                "max-width" : "1024px",
                "overflow"  : "hidden"
            });
            // height of preview
            var height = width * 768 / 1024;
            $("#pagePreview").css({
                "height"    : height + "px"
            });
            // scale iframe
            var ratio = width / 1024;
            if (ratio < 1) {
                var left = -(1024 - width) / 2;
                var top = -(768 - height) / 2;
                $("iframe").css({
                    "transform"     : "scale(" + ratio + ", " + ratio + ")",
                    "margin-left"   : left + "px",
                    "margin-top"    : top + "px"
                });
            }
            // thumbnails
            $("#col-thumbnail").css({
                "left": (width + 20) + "px",
            });
        }

    }

    */

    // Launch presentation window upon click of "My Presentations"
    $("#sidebar-my-presentations").on("click", function(event){
        event.preventDefault();
        event.stopPropagation();
        console.log("MY PRESENTATION BUTTON EMAIL ADDRESS = "+email+"  CLIENT ID = "+clientId);

        window.open( "/folio/framework/index.html?email="+email+"&clientId="+clientId, "_blank", "width=1024, height=768, top:0, left:0");

    });

     // highlight sorted table head of sorted column
    $("thead tr th").mouseup(function() {
        // use mouseup because click event does not fire - no idea why
        $(this).parent().children("th").removeClass("sort");
        $(this).addClass("sort");
    });

    $("thead tr th:first-child").addClass("sort");


    // multiple dragging
    // shift select
    $("#pages-out .checkbox, #pages-out .page-list-item").click(function(event) {
        if (event.shiftKey) {
            // find the first one on top
            var firstID = "";
            $("#pages-out .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (firstID == "" || $(this).offset().top < $("#" + firstID).offset().top)
                        firstID = $(this).attr("id");
                }
            });
            if (firstID == "")
               firstID = $(this).attr("id");
            // find the last one at bottom
            var lastID = "";
            $("#pages-out .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (lastID == "" || $(this).offset().top > $("#" + lastID).offset().top) {
                        if ($(this).attr("id") != firstID)
                            lastID = $(this).attr("id");
                    }
                }
            });
            if (lastID == "")
               lastID = $(this).attr("id");
            $("#pages-out .page-list-item").each(function() {
                if ($(this).offset().top >= $("#" + firstID).offset().top && $(this).offset().top <= $("#" + lastID).offset().top)
                    $(this).children(".checkbox").addClass("checked");
            });
        }
    });

    $("#pages-in .checkbox, #pages-in .page-list-item").click(function(event) {
        if (event.shiftKey) {
            // find the first one on top
            var firstID = "";
            $("#pages-in .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (firstID == "" || $(this).offset().top < $("#" + firstID).offset().top)
                        firstID = $(this).attr("id");
                }
            });
            if (firstID == "")
               firstID = $(this).attr("id");
            // find the last one at bottom
            var lastID = "";
            $("#pages-in .page-list-item").each(function() {
                if ($(this).children(".checkbox").hasClass("checked")) {
                    if (lastID == "" || $(this).offset().top > $("#" + lastID).offset().top) {
                        if ($(this).attr("id") != firstID)
                            lastID = $(this).attr("id");
                    }
                }
            });
            if (lastID == "")
               lastID = $(this).attr("id");
            $("#pages-in .page-list-item").each(function() {
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

        if ($(this).parent().parent().parent().attr("id") == "pages-out")
            $("#pages-in .checkbox").removeClass("checked");
        else  if ($(this).parent().parent().parent().attr("id") == "pages-in")
            $("#pages-out .checkbox").removeClass("checked");

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

});