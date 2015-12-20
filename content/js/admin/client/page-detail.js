$(function(){
    var pageId = $('#pageName').attr('rel');

    // Page group link
    $('[for=linked_pages]').append(' <a href="/admin/client/page-group/id/' + pageId + '">Set Up</a>');

    //question callout function
    $('.page-list-item-label').each( function() {
        $(this).on("mouseover", function() {
            $(this).find('.page-list-item-callout').show();
        });
        $(this).on("mouseout", function() {
            $(this).find('.page-list-item-callout').hide();
        });
    })
    // Page status filter
    $('.page-status-filter').click(function(event){
        event.preventDefault();

        var clientId = parseInt($('#client-filter-default').attr('rel'));
        var status = $(event.target).attr('rel');
        var link = '/admin/client/page';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Client filter
    $('.page-client-filter').click(function(event){
        event.preventDefault();

        var clientId = $(event.target).attr('rel');
        var status = $('#status-filter-default').attr('rel');
        var link = '/admin/client/page';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Generate new versioned page
    $('#new-page-version').click(function(event){
        var message = 'Generating new approved version, please wait...';
        $("#loader-info").html(message);
        $("#overlay, #loader").show();
        var pageId = $('#pageName').attr('rel')
        // Submit
        $.ajax({
            url: "/admin/ajax/new-page-version",
            type: "POST",
            data: {id: pageId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location = window.location.href;
            } else {
                alert('Generate new page version failed: ' + data.meta.error);
                $("#overlay, #loader").hide();
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Generate new page version request failed: " + textStatus);
            $("#overlay, #loader").hide();
        });
    });

    // Form submit.
    /*$('#page-save-submit').click(function(event){
        $('#client-page-form').trigger('submit');
    });*/


    // Form submit ajax.
    $('#page-save-submit').click(function(event){

        var surveyType = $('#type').val();
        var clientName = $('#client option:selected').text();

		switch ($('#type').val()) {
			case "survey":
			case "question":
            case "cefeedback":
				// First save title pages
				// Submit
				$.ajax({
					url: "/admin/ajax/save-page-questions",
					type: "POST",
					data: $('#page-questions').sortable('serialize') + '&page=' + $('#pageName').attr('rel') + '&pagename=' + $('#name').val() + '&clientid=' + $('#client').val() + '&clientname=' + clientName + '&lang=' + $('#pagelanguage').val() + '&surveytype=' + surveyType ,
					dataType: "json"
				})
				.done(function(data){
					if (data.meta.code == 200 && data.meta.error == '') {
						// Ok, good to submit the form as well.
						$('#client-page-form').trigger('submit');

					} else {
						alert('Save page questions failed: ' + data.meta.error);
					}
				})
				.fail(function(jqXHR, textStatus) {
					alert("Save page questions request failed: " + textStatus);
				});
			    break;
			default:
                // Need to save page extra contents first
                savePageDetails();
			    break;
		}

    });

    function savePageDetails()
    {
        var selectedTemplates = [];
        $('.pagePdfTemplates .checkbox.checked').each(function(index, ele){
            selectedTemplates.push($(ele).parents('tr').attr('rel'));
        });
        $.ajax({
            url: "/admin/ajax/save-page-contents",
            type: "POST",
            data: {templates: selectedTemplates, pageId: $('#pageName').attr('rel')},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-page-form').trigger('submit');
            } else {
                alert('Save page contents failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save page contents request failed: " + textStatus);
        });
    }


    // Delete the page
    $('#page-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete page request
    $('.delete-modal-submit').click(function(event){
        var pageId = $('#pageName').attr('rel');

        // Submit
        $.ajax({
            url: "/admin/ajax/delete-page",
            type: "POST",
            data: {id: pageId},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                window.location.reload();
            } else {
                alert('Delete page failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Delete page request failed: " + textStatus);
        });
    });

    // Page navigation element.
    $('#navigation').parent().append('<div id="navigation_ui"></div>');
    $('#navigation_ui').pageNavigationUi({items: pageNavItems});

    // Audio file upload
    $('#audioUpload').fileupload({
        formData: {page: pageId},
        dataType: 'json',
        url: '/admin/ajax/audio-upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Audio file upload faield: ' + data.result.meta.error);
            } else {
                $('#audio_url').val(data.result.audio)
            }
        }
    });

	//page type filtering
	$("#type").change( function(event) {
		var cn = $('#client').val();

		var pageType = $("#type").val();
		if( pageType == "survey" || pageType == "question" || pageType == "cefeedback") {
			$("#question-management").removeClass('hide');
			showHideSurveyDropDown(true);
			filterSurveyDropDown( cn );
		} else {
			$("#question-management").addClass('hide');
			showHideSurveyDropDown(false);
		}
	});

	//fudge: shift the survey drop down to the right column
	$( "#survey" ).insertAfter( $( "#questions_list_head" ) );
	$('label[for=survey]').insertAfter( $( "#questions_list_head" ) );

	//survey filtering questions
	$("#survey").change( function(event) {
		filterQuestions();
	});

	// Connected sortable
    $("#client-questions, #page-questions").sortable({
        connectWith: ".connectedSortable"
    }).disableSelection();

	//configure according to page type
	var pageType = $("#type").val();
	if ( pageType == "survey" || pageType == "question" || pageType == "cefeedback") {
		$("#question-management").removeClass('hide');
	} else {
		showHideSurveyDropDown(false);
	}

    //Client side thumbnail visibility button
    /*$('#btn-hideOnClientSide').click(function(event){
        $('#icon_client_visibility').val( $(this).hasClass('checked') ? 0 : 1 )
    });*/

    //init the tag management code
    setTagManager();

	surveyDropDownEvent_FilterPage();

	hideClientSurveyQuestions();

    if($('#client-page-form').find('#status').val()=="3")
    {
        $('#new-page-version').prop("disabled",false);
    }else
    {
        $('#new-page-version').prop("disabled",true);
    }

});

var TAGMANAGER;

function setTagManager() {
    TAGMANAGER = null;
    TAGMANAGER = new TagManager( setTagManager );
    if (updateMessage != "") TAGMANAGER.showSaveMessage( true );
}

function hideClientSurveyQuestions() {
	$("#client-questions li").each( function( event ) {
		$(this).hide();
	});
}

function filterQuestions() {
	var survey_id = $("#survey").val();
	$("#client-questions li").each( function( event ) {
		if( $(this).find("#survey_id").val() == survey_id ) {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}

function showHideSurveyDropDown(bool) {
	if(bool==true) {
		$("#survey").show();
		$('label[for=survey]').show();
	} else {
		$("#survey").hide();
		$('label[for=survey]').hide();
	}
}

function filterSurveys() {
    //show surveys by client

    setTimeout(function() {
        filterSurveyDropDown($('#client').val()) , 250
    })
}
filterSurveys();

//Filter:Page on Client change
function surveyDropDownEvent_FilterPage() {
	$('#client').change(function(event) {
		var cn = $(this).val();
		filterSurveyDropDown(cn);
		if(cn!="") {
			$("#page option:first").text("Select a page");
			filterPageOnSurvey(cn);
		} else {
			$("#page option:first").text("Choose a client first");
			$("#page").val("");
			hidePages();
		}
	});
	var cn = $('#client').val();
	//if(cn!="") initPage($('#client').val());
}

function filterPageOnSurvey(n) {
	$('#page').val("0");
	//initPage(n);
}

function filterSurveyDropDown(client_n) {
	var foundpages = false;
	$('#survey option').each(function(event) {
		var ref = $(this).attr("ref");
		if(client_n==ref) {
			$(this).show();
			foundpages = true;
			$("#survey option:first").text("Choose a survey");
		} else {
			if( $(this).val()!="") $(this).hide();
		}
	})
	if(foundpages==false) {
		$("#survey option:first").text("No surveys created for this client");
	}
}

