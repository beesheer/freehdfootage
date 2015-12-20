$(function () {
	var pageId = $('#pageName').attr('rel');

    // Page group link
    $('[for=linked_pages]').append(' <a href="/client/page/page-group/id/' + pageId + '">Set Up</a>');

	//question callout function
	$('.page-list-item-label').each(function () {
		$(this).on("mouseover", function () {
			$(this).find('.page-list-item-callout').show();
		});
		$(this).on("mouseout", function () {
			$(this).find('.page-list-item-callout').hide();
		});
	});

    // Generate new versioned page
    $('#new-page-version').click(function(event){
        var message = 'Generating new approved version, please wait...';
        $("#loader-info").html(message);
        $("#overlay, #loader").show();
        var pageId = $('#pageName').attr('rel')
        // Submit
        $.ajax({
            url: "/client/ajax/new-page-version",
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


	$("#page-edit-submit").click(function (event) {
		event.preventDefault();
		var url = '/client/page/edit/id/' + $('#pageName').attr('rel');
		window.location.href = url;
	});

	// Form submit ajax.
	$("#page-save-submit").click(function (event) {

		var surveyType = $('#type').val();
		var clientName = $('#client option:selected').text();

		//NOTE: client side > currently only handles static pages
		switch ($('#type').val()) {
			case "survey":
			case "question":
			case "cefeedback":
				// First save title pages
				// Submit
				$.ajax({
					url: "/client/ajax/save-page-questions",
					type: "POST",
					data: $('#page-questions').sortable('serialize') + '&page=' + $('#pageName').attr('rel') + '&pagename=' + $('#name').val() + '&clientid=' + $('#client').val() + '&clientname=' + clientName + '&lang=' + $('#pagelanguage').val() + '&surveytype=' + surveyType,
					dataType: "json"
				})
						.done(function (data) {
							if (data.meta.code == 200 && data.meta.error == '') {
								// Ok, good to submit the form as well.
								// $('#client-page-form').trigger('submit');

							} else {
								alert('Save page questions failed: ' + data.meta.error);
							}
						})
						.fail(function (jqXHR, textStatus) {
							alert("Save page questions request failed: " + textStatus);
						});
				break;
			default:
				// NOTE: client side > does not save page extra contents first
				savePageDetails();
				//$('#client-page-form').trigger('submit');
				break;
		}
	})

	function savePageDetails()
	{
		var selectedTemplates = [];
		$('.pagePdfTemplates .checkbox.checked').each(function (index, ele) {
			selectedTemplates.push($(ele).parents('tr').attr('rel'));
		});
		$.ajax({
			url: "/client/ajax/save-page-contents",
			type: "POST",
			data: {templates: selectedTemplates, pageId: $('#pageName').attr('rel')},
			dataType: "json"
		})
				.done(function (data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						// Ok, good to submit the form as well.
						// $('#client-page-form').trigger('submit');
					} else {
						alert('Save page contents failed: ' + data.meta.error);
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert("Save page contents request failed: " + textStatus);
				});
	}


	// Delete the page
	$('#page-delete').click(function (event) {
		var $button = $(event.target);
		// Change label and set id to empty
		$('#delete-modal').modal('show');
	});

	// Delete page request
	$('.delete-modal-submit').click(function (event) {
		var pageId = $('#pageName').attr('rel');

		// Submit
		$.ajax({
			url: "/client/ajax/delete-page",
			type: "POST",
			data: {id: pageId},
			dataType: "json"
		})
				.done(function (data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						window.location.reload();
					} else {
						alert('Delete page failed: ' + data.meta.error);
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert("Delete page request failed: " + textStatus);
				});
	});

	// Page navigation element: add but don't show
	$('#navigation').parent().append('<div id="navigation_ui" class="hide"></div>');
	$('#navigation_ui').pageNavigationUi({items: pageNavItems});

	// Audio file upload
	$('#audioUpload').fileupload({
		formData: {page: pageId},
		dataType: 'json',
		url: '/client/ajax/audio-upload',
		done: function (e, data) {
			if (data.result.meta.error != '') {
				alert('Audio file upload failed: ' + data.result.meta.error);
			} else {
				//data.result.meta
				alert(data.result.meta.code)
				$('#audio_url').val(data.result.audio)
			}
		}
	});

	// New file upload
	$('#iconUpload').fileupload({
		formData: {client: clientId}, //, allowType:'png,jpg,gif'
		dataType: 'json',
		url: '/client/ajax-media-asset/upload',
		done: function (e, data) {
			if (data.result.meta.error != '') {
				alert('Upload failed > error: ' + data.result.meta.error);
			} else {
				// Create the media asset directly
				$.ajax({
					url: "/client/ajax-media-asset/create",
					type: "POST",
					data: {client: clientId, name: data.result.fileName, filepath: data.result.filePath},
					dataType: "json"
				})
						.done(function (data, textStatus) {
							$('#icon_image').val(data.id)
							$('#filepath-icon-preview').html(data.preview);
						})
						.fail(function (jqXHR, textStatus) {
							alert("Create media asset request failed: " + textStatus);
						});
			}
		}
	});


	//page type filtering
	$("#type").change(function (event) {
		var cn = $('#client').val();

		var pageType = $("#type").val();
		if (pageType == "survey" || pageType == "question" || pageType == "cefeedback") {
			$("#question-management").removeClass('hide');
			showHideSurveyDropDown(true);
			filterSurveyDropDown(cn);
		} else {
			$("#question-management").addClass('hide');
			showHideSurveyDropDown(false);
		}
	});

	//shift the survey drop down to the right column
	$("#survey").insertAfter($("#questions_list_head"));
	$('label[for=survey]').insertAfter($("#questions_list_head"));

	//survey filtering questions
	$("#survey").change(function (event) {
		filterQuestions();
	});

	// Connected sortable
	$("#client-questions, #page-questions").sortable({
		connectWith: ".connectedSortable"
	}).disableSelection();

	//configure according to page type
	var pageType = $("#type").val();
	if (pageType == "survey" || pageType == "question" || pageType == "cefeedback") {
		$("#question-management").removeClass('hide');
	} else {
		showHideSurveyDropDown(false);
	}

	surveyDropDownEvent_FilterPage();

	hideClientSurveyQuestions();


	// -------- TAG MANAGEMENT ----------------

	$("#page-save-submit").click(function () {
		clearSearchInput();
		$checked = $.find(".checked");
		$selectedTags = [];
		updateMessage = "Saving...";
		if (updateMessage != "") {
			var message = updateMessage;
			$("#loader-info").html(message);
			//$("#overlay, #loader").addClass("completed");
			$("#overlay, #loader").show();
		}
		for ($item in $checked) {
			//$selectedTags.push($($checked[$item]).attr("tag-id"));
			$selectedTags.push($($checked[$item]).attr("tag-name"));
		}

		$('#form-page-tag-list').val(JSON.stringify($selectedTags));


		$.ajax({
			url: '',
			type: 'post',
			data: 'form-page-tag-list=' + JSON.stringify($selectedTags),
			success: function (e) {
				$("#overlay, #loader").hide();
				//$("#overlay, #loader").removeClass("completed");
				$('#client-page-form').trigger('submit');
			}
		});
	});

	var revertTable = $("#page-tags").html();
	$("#revert-tags").click(function () {
		$("#page-tags").html(revertTable);
		resetCheckboxDefaultSelection();
		initTagManagement();
	});

	//toggles just tags belonging to the page
	function setTagsIncludeFilter() {
		$("#tags-included-filter").on("click", function () {
			//$(this).toggleClass("active");
			var removing = ($(this).hasClass("active")) ? 1 : 0;
			$("#page-tags .checkbox").each(function () {
				if (removing == 1) {
					if (!$(this).hasClass("checked")) {
						$(this).parent().parent().fadeOut(200);
					}
				} else {
					$(this).parent().parent().fadeIn(200);
				}
			});
		});
	}

	// TAG SELECTION
	function resetCheckboxDefaultSelection() {
		$(".checkbox").click(function (event) {
			event.stopPropagation();
			$(this).toggleClass("checked");
		});
	}

	function setCheckboxTreeSelection() {
		$(".checkbox").each(function () {
			$(this).click(function () {
				if ($(this).attr("parent-id") !== "") {
					if ($(this).hasClass("checked")) {
						searchParents($(this).attr("parent-id"), true)
					} else {
						searchParents($(this).attr("parent-id"), false);
						deselectChildren($(this).attr("tag-id"));
					}
				} else {
					if (!$(this).hasClass("checked")) {
						deselectChildren($(this).attr("tag-id"));
					}
				}
			});
		});
	}

	function searchParents(id, enable) {
		$("#page-tags").find(".checkbox").each(function () {
			if ($(this).attr("tag-id") == id) {
				if (enable === true) {
					$(this).addClass("checked");
					searchParents($(this).attr("parent-id"), true);
				}
			}
		})
	}
	;

	function deselectChildren(id) {
		$("#page-tags").find(".checkbox").each(function () {
			if ($(this).attr("parent-id") === id && $(this).hasClass("checked")) {
				$(this).removeClass("checked");
				deselectChildren($(this).attr("tag-id"))
			}
		});
	}

	// SEARCH TAGS
	var tagList;
	function initTagSearching() {
		// list js search
		$("#page-tags tbody").addClass("list");
		var options = {
			valueNames: ['tagname']
		};
		tagList = new List('tag_list', options);
		// clear the search input
		$("#clear-search-page-tags").on("click", function () {
			/*var searchInput =  $('#search-page-tags');
			 searchInput.val('');
			 tagList.search();*/
			clearSearchInput();
		});
	}

	function clearSearchInput() {
		var searchInput = $('#search-page-tags');
		searchInput.val('');
		tagList.search();
	}

	function initTagManagement() {
		setCheckboxTreeSelection();
		setTagsIncludeFilter();
		initTagSearching();
	}

	initTagManagement();

	// -------- END TAG MANAGEMENT ----------------


	// ALERTS from posted updates

	if (updateMessage != "") {
		var message = updateMessage;
		$("#loader-info").html(message);
		$("#overlay, #loader").addClass("completed")
		$("#overlay, #loader").show();
		setTimeout(function () {
			$("#overlay, #loader").hide();
			$("#overlay, #loader").removeClass("completed");
		}, 1000)
	}

	if($('#client-page-form').find('#status').val()=="3")
	{
		$('#new-page-version').prop("disabled",false);
	}else
	{
		$('#new-page-version').prop("disabled",true);
	}

});


function hideClientSurveyQuestions() {
	$("#client-questions li").each(function (event) {
		$(this).hide();
	});
}

function filterQuestions() {
	var survey_id = $("#survey").val();
	$("#client-questions li").each(function (event) {
		if ($(this).find("#survey_id").val() == survey_id) {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}

function showHideSurveyDropDown(bool) {
	if (bool == true) {
		$("#survey").show();
		$('label[for=survey]').show();
	} else {
		$("#survey").hide();
		$('label[for=survey]').hide();
	}
}

function filterSurveys() {
	//show surveys by client

	setTimeout(function () {
		filterSurveyDropDown($('#client').val()), 250
	})
}
filterSurveys();

//Filter:Page on Client change
function surveyDropDownEvent_FilterPage() {
	$('#client').change(function (event) {
		var cn = $(this).val();
		filterSurveyDropDown(cn);
		if (cn != "") {
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
	$('#survey option').each(function (event) {
		var ref = $(this).attr("ref");
		if (client_n == ref) {
			$(this).show();
			foundpages = true;
			$("#survey option:first").text("Choose a survey");
		} else {
			if ($(this).val() != "")
				$(this).hide();
		}
	})
	if (foundpages == false) {
		$("#survey option:first").text("No surveys created for this client");
	}
}
