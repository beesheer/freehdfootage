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

	var clientPages = {
		"app"		: true,
		"form"		: true,
		"package"	: true,
		"page"		: true,
		"result"	: true,
		"survey"	: true,
		"team"		: true,
		"title"		: true,
		"user"		: true,
        "tag"       : true
	}

    // go to the page displays content of selected client ID
    var url = document.URL.split("/");
    // admin/client/[clientPages]
    // admin/review-portal
    var i = url.length - 2;
    var j = url.length - 1;
    if (url[i] == "client") {
    	var page = url[j];
        if (clientPages[page] == null) {
            $(".table").show();
        } else {
        	if (cookie.read("lifelearn-stratus-client")) {
                var link = document.URL;
                if (link.substr(link.length-1) == "/")
                    link = link.substr(0, link.length-1);
                link =  link + "/client/" + cookie.read("lifelearn-stratus-client");
                window.location.replace(link);
            } else {
            	$(".table").show();
            }
        }
    } else if (url[j] == "review-portal") {
    	if (cookie.read("lifelearn-stratus-client")) {
            var link = document.URL;
            if (link.substr(link.length-1) == "/")
                link = link.substr(0, link.length-1);
            link =  link + "/index/client/" + cookie.read("lifelearn-stratus-client");
            window.location.replace(link);
        } else {
        	$(".table").show();
        }
    } else {
    	$(".table").show();
    }


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



	// client
    if (cookie.read("lifelearn-stratus-client")) {
        var client = "";
        $(".menu-client").each(function() {
            if ($(this).attr("clientID") == cookie.read("lifelearn-stratus-client"))
                client = $(this).html();
        });
        $("#menu-clients-selected").html(client);
    }

    // get admin user's name
	/*
    $.post(
        "/admin/ajax/user-info",
        {
            "id": [0]
        },
        function(data) {
            for (var i in data["users"]) {
                var id = data["users"][i]["id"];
                var name = data["users"][i]["firstname"] + " " + data["users"][i]["surname"];
                $("#menu-user").html(name);
            }
        },
        "json"
    );*/

    if (document.URL.indexOf("/client/detail") > 0) {
        $("#menu-clients").hide();
    }

    //homepage
    $("#logo").click(function() {
        window.location.replace("/admin");
    });

    $("#overlay").click(function() {
        if ($("#loader").css("display") == "none") {
            $("#overlay").hide();
            $(".popup").fadeOut(200);
        }
    });



    $("input, button:not(.btn-default)").click(function(event) {
        event.stopPropagation();
    });

    $(".menu.link:not(#menu-logout)").click(function(event) {
        event.stopPropagation();
        window.location.replace($(this).attr("href"));
    });

    $("#menu-logout").click(function(event) {
        event.stopPropagation();
        //cookie.erase("lifelearn-stratus-client");
        //cookie.erase("lifelearn-stratus-theme");
        //cookie.erase("lifelearn-stratus-sidebar");
        window.location.replace("/logout");
    });

    $(".sidebar.list").click(function(event) {
        event.stopPropagation();
        window.location.replace($(this).attr("href"));
    });

    // display the active button for each section
    if (document.URL.indexOf("/review-portal") > 0) {
        $("#sidebar-portal").addClass("active");
    } else if (document.URL.indexOf("/index/type/document") > 0) {
        $("#sidebar-document").addClass("active");
    } else if (document.URL.indexOf("/media-asset") > 0){
        $("#sidebar-media-asset").addClass("active");
    } else if (document.URL.indexOf("/pdf-template") > 0) {
        $("#sidebar-pdf-template").addClass("active");
    } else if (document.URL.indexOf("/page-template") > 0) {
        $("#sidebar-page-template").addClass("active");
    } else if (document.URL.indexOf("/user") > 0) {
        $("#sidebar-user").addClass("active");
    } else if (document.URL.indexOf("/team") > 0) {
        $("#sidebar-team").addClass("active");
    } else if (document.URL.indexOf("/page") > 0) {
        $("#sidebar-page").addClass("active");
    } else if (document.URL.indexOf("/title") > 0) {
        $("#sidebar-title").addClass("active");
    } else if (document.URL.indexOf("/package") > 0) {
        $("#sidebar-package").addClass("active");
    } else if (document.URL.indexOf("/app") > 0) {
        $("#sidebar-app").addClass("active");
    } else if (document.URL.indexOf("/form") > 0) {
        $("#sidebar-form").addClass("active");
    } else if (document.URL.indexOf("/survey") > 0 && document.URL.indexOf("/survey-chart") ===-1) {
        $("#sidebar-survey").addClass("active");
	} else if (document.URL.indexOf("/survey-chart") > 0){
		$("#sidebar-survey-chart").addClass("active");
	} else if (document.URL.indexOf("/tag") > 0){
		$("#sidebar-tags").addClass("active");
	} else if (document.URL.indexOf("/result") > 0) {
        $("#sidebar-result").addClass("active");
    } else if (document.URL.indexOf("/meeting") > 0) {
        $("#sidebar-meeting").addClass("active");
    } else if (document.URL.indexOf("/contact") > 0) {
        $("#sidebar-contact").addClass("active");
    } else if (document.URL.indexOf("/client") > 0) {
        $("#sidebar-client").addClass("active");
        $("#menu-clients").hide();
    } else {
        // admin analytics
        $("#sidebar-analytics").addClass("active");
        $("#container-footer").hide();
        if (dark || cookie.read("lifelearn-stratus-theme")) {
            if (cookie.read("lifelearn-stratus-theme") == 1) {
                // dark theme
                $("html, body").css({
                    "background-color": "#444444"
                });
            } else {
                $("html, body").css({
                    "background-color": "#F7F7F7"
                });
            }
        }
    }

    // menu clients
    $("#menu-clients").click(function(event) {
        event.stopPropagation();
        if ($("#menu-clients-dropdown").css("display") == "none")
            $("#menu-clients-dropdown").stop().slideDown();
        else
            $("#menu-clients-dropdown").stop().slideUp();
    });

    $("body").click(function() {
        $(".dropdown").stop().slideUp();
    });

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



	$("#menu-config-dropdown").hover(function(){

	},function(){
		$("#menu-config-dropdown").stop(true,true).slideUp();
	});

	$(".config-item").click(function(e){
		console.log(e.currentTarget.innerHTML);
	});

    $("#menu-clients").hover(function() {
    }, function() {
        $("#menu-clients-dropdown").stop().slideUp();
    });


    $(".menu-client").click(function(event) {
        event.stopPropagation();
        cookie.create("lifelearn-stratus-client", $(this).attr("clientID"), 7);
        if (document.URL.indexOf("/review-portal") > 0) {
            window.location.replace("/admin/review-portal");
        } else if (document.URL.indexOf("/document") > 0) {
            window.location.replace("/admin/document");
        } else if (document.URL.indexOf("/user") > 0) {
            window.location.replace("/admin/client/user");
        } else if (document.URL.indexOf("/team") > 0) {
            window.location.replace("/admin/client/team");
        } else if (document.URL.indexOf("/page-template") > 0) {
            window.location.replace("/admin/client/page-template");
        } else if (document.URL.indexOf("/page") > 0) {
             window.location.replace("/admin/client/page");
        } else if (document.URL.indexOf("/pdf-template") > 0) {
            window.location.replace("/admin/client/pdf-template");
        } else if (document.URL.indexOf("/title") > 0) {
             window.location.replace("/admin/client/title");
        } else if (document.URL.indexOf("/package") > 0) {
             window.location.replace("/admin/client/package");
        } else if (document.URL.indexOf("/app") > 0) {
             window.location.replace("/admin/client/app");
        } else if (document.URL.indexOf("/form") > 0) {
             window.location.replace("/admin/client/form");
		} else if (document.URL.indexOf("/result") > 0) {
            window.location.replace("/admin/client/result");
        } else if (document.URL.indexOf("/survey") > 0) {
             window.location.replace("/admin/client/survey");
        } else if (document.URL.indexOf("/tag") > 0) {
            window.location.replace("/admin/client/tag");
        } else if (document.URL.indexOf("/client") > 0) {
             window.location.replace("/admin/client");
        } else {
            $("#menu-clients-selected").html($(this).html());
            $("#menu-clients-dropdown").slideUp();
        }
    });

    $("a").click(function(event) {
        event.stopPropagation();
    });

    // click a row in a table goes to its details
    $("tr").click(function() {

        // Not all tables need this, for example, the comments table in review portal.
        if ($(this).hasClass('noClickThrough'))
            return true;

        // if already in details page then do nothing
        if (document.URL.indexOf("/id/") > 0) {
            return true;
        }

        var id = $(this).attr("rel");

        var url = "/admin";

        if (document.URL.indexOf("/review-portal") > 0) {
            url += "/review-portal/pages/id/" + id;
        } else if (document.URL.indexOf("/pdf-template") > 0) {
            url += "/client/pdf-template-detail/id/" + id;
        } else if (document.URL.indexOf("/page-template") > 0) {
            url += "/client/page-template-detail/id/" + id;
        } else if (document.URL.indexOf("/user") > 0) {
            url += "/client/user-detail/id/" + id;
        } else if (document.URL.indexOf("/service-user") > 0) {
            url += "/config/service-user-detail/id/" + id;
        } else if (document.URL.indexOf("/team") > 0) {
            url += "/client/team-detail/id/" + id;
        } else if (document.URL.indexOf("/page") > 0) {
            url += "/client/page-detail/id/" + id;
        } else if (document.URL.indexOf("/title") > 0) {
            url += "/client/title-detail/id/" + id;
        } else if (document.URL.indexOf("/package") > 0) {
            url += "/client/package-detail/id/" + id;
        } else if (document.URL.indexOf("/app") > 0) {
            url += "/client/app-detail/id/" + id;
        } else if (document.URL.indexOf("/form") > 0) {
            url += "/client/form-detail/id/" + id;
        } else if (document.URL.indexOf("/survey") > 0) {
            url += "/client/survey-detail/id/" + id;
		} else if (document.URL.indexOf("/result") > 0) {
            var appId = $(this).attr("app_id");
            if (typeof appId !== typeof undefined && appId !== false) {
                url += "/client/result-student-list/id/" + appId
            } else {
                url += "/client/result-detail/id/" + id
            }
        } else if (document.URL.indexOf("/media-asset") > 0) {
            url += "/client/media-asset/id/" + id;
        } else if (document.URL.indexOf("/client") > 0) {
            url += "/client/detail/id/" + id;
        }

        window.location.replace(url);
    });

    // sidebar
    $("#sidebar-control").click(function() {
        if ($(this).hasClass("nonClickable")) return;

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
                "margin-left": "80px"
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
                "margin-left": "240px"
            });


        }
    });

    $("#sidebar-control").click(function() {
        if ($(this).hasClass("nonClickable"))
            return;
        $(this).addClass("nonClickable");
        var that = this;
        setTimeout(function() {
            $(that).removeClass("nonClickable");
        }, 400);
    });

    // checkbox
    $(".checkbox").click(function(event) {
        event.stopPropagation();
        $(this).toggleClass("checked");
    });

    // hide review portal nav bar
    $("#portal-nav-portals, #portal-nav-pages, #portal-nav-comments, #portal-nav-pdf, #portal-nav-details").hide();
    if (document.URL.indexOf("/review-portal/pages") > 0 || document.URL.indexOf("/review-portal/comments") > 0 ||
        document.URL.indexOf("/review-portal/pdf") > 0 || document.URL.indexOf("/review-portal/details") > 0 ||
        document.URL.indexOf("/review-portal/documents") > 0 || document.URL.indexOf("/review-portal/settings") > 0
    ) {
        $("#portal-nav-portals, #portal-nav-pages, #portal-nav-comments, #portal-nav-pdf, #portal-nav-details").show();
    }

    if (document.URL.indexOf("/review-portal/pages") > 0 || document.URL.indexOf("/review-portal/documents") > 0) {

        console.log("REVIEW PORTAL");


        $(window).resize( resizeReviewPortal );

        resizeReviewPortal();

    }

    /*
    // review portal
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
            var width = $(window).width() - 80 - $("#sidebar").width() - 110;
            if (width > 1024)
                width = 1024;
            $("#col-main, #panel-preview, #panel-status, #panel-notes, #panel-comments").css({
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
                "left": (width + 30) + "px",
            });
            $("#page-thumbnail").css({
                "height": (height + 36) + "px"
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
            $("#col-main, #panel-preview, #panel-status, #panel-notes, #panel-comments").css({
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
            $("#page-thumbnail").css({
                "height": (height + 36) + "px"
            });
        }

    }
    */

    // save edited client
    $('#client-edit-save').click(function(event){

        var clientName = $('#client_name').val();
        var clientType = $('#client_type').val();

        // Validation
        if (clientName == '') {
            alert('Please enter client name');
            return false;
        }
        if (clientType == '') {
            alert('Please select client type');
            return false;
        }

        // Submit
        $.ajax({
            url: "/admin/ajax/create-client",
            type: "POST",
            data: $('#edit-client-form').serialize(),
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Edit client failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Edit client request failed: " + textStatus);
        });

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