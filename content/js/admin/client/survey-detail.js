var responseContextChoice;

//for indicating the option order
var letters = "abcdefghijklmnopqrstuvwxyz".split("");

//option li index
var indexBefore = -1;

//whether answer is indicated
var correct_answer_set = false;

var num_surveyQuestions = 0;

var files;

var surveyType;

var addMedia = true;

var optionInstructions_default = "Add option text. Use checkboxes to indicate correct answer(s).";

var optionInstructions_ce = "Add option text.";

var conditionalFieldsCE = [["#passscore", "fade"],
	["#ce", "fade"],
	["#maxtime", "fade"],
	["#maxquestions", "fade"],
	["#responsetype", "fade"],
	["#random", "fade"],
	["#userfeedback", "fade"],
	["#questiontype", "hide"],
	[".set-correct-answer", "hide"],
	["#feedback_questiontype_filter", "show"],
	["#option-instructions", "changetext", [optionInstructions_default, optionInstructions_ce]]
];

$(function() {

	//page type filtering
	$("#client-survey-form #type").change(function(event) {
		filterSurveyTypeDropDown();
	});

	$("#option_prefix").change(function(event) {
		setOptionPrefixes();
	});

	$("#feedback_questiontype_filter").change(function(event) {
		filterFeedbackQuestionTypeDropDown();
	});

	// Page status filter
	$('.survey-status-filter').click(function(event) {
		event.preventDefault();

		var clientId = parseInt($('#client-filter-default').attr('rel'));
		var status = $(event.target).attr('rel');
		var link = '/admin/client/survey';
		if (clientId > 0) {
			link += '/client/' + clientId;
		}
		if (status > 0) {
			link += '/status/' + status;
		}
		window.location = link;
	});

	// Client filter
	$('.survey-client-filter').click(function(event) {
		event.preventDefault();

		var clientId = $(event.target).attr('rel');
		var status = $('#status-filter-default').attr('rel');
		var link = '/admin/client/survey';
		if (clientId > 0) {
			link += '/client/' + clientId;
		}
		if (status > 0) {
			link += '/status/' + status;
		}
		window.location = link;
	});

	// Survey Form submit.
	$('#survey-save-submit').click(function(event) {
        saveSurvey();
	});

    // Save survey
    function saveSurvey()
    {
        // Presave access
        var selectedAccessClients = [];
        $('.accessTree input:checked').each(function(index, ele){
            selectedAccessClients.push($(ele).parents('li').attr('rel'));
        });
        $.ajax({
            url: "/admin/ajax-resource-access/save",
            type: "POST",
            data: {resourceType: 'survey', resourceId: $('#surveyName').attr('rel'), accessClients: selectedAccessClients},
            dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
                // Ok, good to submit the form as well.
                $('#client-survey-form').trigger('submit');
            } else {
                alert('Save access control data failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus) {
            alert("Save access control data failed: " + textStatus);
        });
    }

	// Question Form submit.
	$('.form-modal-submit').click(function(event) {
		//$('#add-question-form').submit();

		var submitting_new = ($(this).html() == "Submit");
		var postdata = new FormData();

		var datavalid = true;
		var invalid_message = "";

		uploadFiles("add-question-form", postdata);

		//gather question data:
		var question_english = $("#add-question-form").find('#question_english').val();
		if (question_english == "") {
			datavalid = false;
			invalid_message += "Please add question text.\n";
		}
		postdata.append('question_english', question_english);
		var qid = (submitting_new == true) ? "" : $("#add-question-form").find("#id").val();
		postdata.append('questionid', qid);
		//the user-given id for the question
		postdata.append('question_id', $("#form-modal").find("#question_id").val());
		postdata.append('clientid', $("#form-modal").find("#client").val());
		postdata.append('surveyid', $('#surveyid').val());
		postdata.append('orderid', $("#form-modal").find("#order").val());
		postdata.append('image', filterFileUploadName($("#add-question-form").find('#image').val()));
		postdata.append('video', filterFileUploadName($("#add-question-form").find('#video').val()));
		postdata.append('correctanswer', $("#correct_answer").val());


		//get type of question
		var questiontype;
		if (surveyType == "cefeedback") {
			questiontype = $("#add-question-form").find('#feedback_questiontype_filter').val();
		} else {
			questiontype = $("#add-question-form").find('#questiontype').val();
		}
		postdata.append('questiontype', questiontype);

		//gather option text:
		var num_options = $('#question-options').find('li').length;
		var optiondata = [];
		//gather option text:
		var option_count = 1;
		var optiondata = [];
		var optiontext = $('#question-options').find('li').each(function(event) {
			var obj = $(this).find('textarea');
			var id = $(obj).attr("id");
			var text = $(obj).val();
			var optionobject = Object();
			if (text == "") {
				datavalid = false;
				invalid_message += "Some options are empty.\n";
			}
			optionobject.order = option_count;
			optionobject.text = text;
			optionobject.image = filterFileUploadName($(this).find("#option_image").val());
			optionobject.video = $(this).find("#option_video").val();
			optionobject.optiontype = $(this).find("#option_type").val();
			optionobject.nextquestion = $(this).find("#option_nextquestionid").val();
			optionobject.prefix = $("#option_prefix option:selected").text();
			optiondata.push(optionobject);
			option_count++;
		});

		postdata.append('options', JSON.stringify(optiondata));

		//gather response data
		//get kind of response
		var responsescope = $('#response-scope-filter').val();
		postdata.append('responsescope', responsescope);


		var are_responses = true;
		var responsedata = [];
		//gather response text based on response type
		switch (responsescope) {
			case '1':
				//no responses
				are_responses = false;
				break;
			case '2':
				//one response for all
				var response_all = $("#response-input-all").find(".inner textarea").val();
				postdata.append('response_all', response_all);
				break;
			case '3':
				//one right response, one wrong response
				var response_right = $("#response-input-right").find(".inner textarea").val();
				postdata.append('response_right', response_right);
				var response_wrong = $("#response-input-wrong").find(".inner textarea").val();
				postdata.append('response_wrong', response_wrong);

				break;
			case '4':
				//separate response for each option
				var responsedata = [];
				var response;
				for (var i = 1; i <= num_options; i++) {
					var responseobject = Object();
					response = $('#response' + i).val();
					if (response == "") {
						invalid_message += "Responses for each option are incomplete.\n";
						datavalid = false;
						break;
					}
					responseobject.text = response;
					responseobject.order = i;
					responsedata.push(responseobject);
				}
				postdata.append('response_array', JSON.stringify(responsedata));
				break;
		}
		//postdata.append('visual_assets',imageandvideo_array);

		if (datavalid == false) {
			alert(invalid_message);
			return false;
		}

		var action = (submitting_new == true) ? "add-question" : "update-question";

		// Submit
		$.ajax({
			url: "/admin/ajax/" + action,
			type: "POST",
			cache: false,
			contentType: false,
			processData: false,
			data: postdata,
			dataType: "json"
		})
				.done(function(data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						alert('Question saved');
						//refresh page:
						window.location.reload();
					} else {
						alert('Add/update question failed: ' + data.meta.error);
					}
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					alert("Add/update question request failed: " + textStatus + "( " + errorThrown + " )");//JSON.stringify(jqXHR));
				});

	});

	// Delete the survey
	$('#survey-delete').click(function(event) {
		var button = $(event.target);
		// Change label and set id to empty
		$('#delete-modal').modal('show');
	});

	// Delete a question
	$('.delete-question-item-button').click(function(event) {

		var questionItemId = $(this).attr('rel');
		var confirmation = confirm("Delete this question?");

		if (confirmation == true) {
			$.ajax({
				url: "/admin/ajax/delete-question-item",
				type: "POST",
				data: {id: questionItemId},
				dataType: "json"
			})
					.done(function(data) {
						if (data.meta.code == 200 && data.meta.error == '') {
							window.location.reload();
						} else {
							alert('Delete question item failed: ' + data.meta.error);
						}
					})
					.fail(function(jqXHR, textStatus) {
						alert("Delete question item request failed: " + textStatus);
					});
		}

	});

	// Delete user request
	$('.delete-modal-submit').click(function(event) {
		var surveyId = $('#surveyName').attr('rel');
		// Submit
		$.ajax({
			url: "/admin/ajax/delete-survey",
			type: "POST",
			data: {id: surveyId},
			dataType: "json"
		})
				.done(function(data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						window.location.reload();
					} else {
						alert('Delete survey failed: ' + data.meta.error);
					}
				})
				.fail(function(jqXHR, textStatus) {
					alert("Delete survey request failed: " + textStatus);
				});
	});

	//show question detail dialog
	$('#btn-add-question').click(function(event) {
		reInit();
		refreshPageAndClientId();
		showQuestionFormModal();
		writeSuggestedSlug();
	});

	//add a question option
	$('#btn-add-option').click(function(event) {
		//check against true false
		if ($('#questiontype option:selected').text() == "truefalse" && $("#question-options li").length >= 2) {
			alert("True/False questions can only have two options.");
		} else {
			var newnodeid = $("#question-options").find("li").length + 1;
			$("#question-options").append(addOption(newnodeid));
			$("#optionimages").append(getOptionMediaInputHTML(newnodeid, "image"));
			$("#optionvideos").append(getOptionMediaInputHTML(newnodeid, "video"));
			$("#response-input-each").find(".inner").append(addResponse(newnodeid));
		}
	});

	//main form response type
	$('#responsetype').change(function(event) {
		if ($('#responsetype').val() == 2) {
			$('#responseconfig').show();
		}
	});

	//reponse panel openers
	$('.open-responseinput').each(function(event) {
		$(this).click(function(event) {
			var id = $(this).attr('id');
			var suffix = id.substring(id.lastIndexOf("-") + 1);
			$("#response-input-" + suffix).fadeIn();
		});
	});

	//response panel closers
	$('.response.close').each(function(event) {
		$(this).click(function(event) {
			$(this).parent().fadeOut();
		});
	});

	//question form response type filter
	$('#response-scope-filter').change(function(event) {
		showResponseOpenerContext();
	});

	//handle question select changes
	$('#questiontype').change(function(event) {
		if ($('#questiontype').val() == 1) {
			if ($("#question-options").find("li").length > 2) {
				var user = confirm("You have chosen true/false format. OK to reduce options to two (input will be lost)?");
				if (user == true) {
					//trim the options
					$("#question-options").find("li").each(function(event) {
						var index = $(this).index();
						if (index > 1) {
							$(this).remove();
							$("#response-input-each-content li").eq(index).remove();
						}
					});
				}
			}
		}
	});

	// Delete client request
	$('.survey-question-submit').click(function(event) {
		// Submit

		$.ajax({
			url: "/admin/ajax/add-question",
			type: "POST",
			data: {id: clientId},
			dataType: "json"
		})
				.done(function(data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						window.location.reload();
					} else {
						alert('Add question failed: ' + data.meta.error);
					}
				})
				.fail(function(jqXHR, textStatus) {
					alert("Add question request failed: " + textStatus);
				});

	});

	// Connected sortable: ties the responses to the options
	$("#question-options").sortable({
		connectWith: ".connectedSortable",
		helper:"clone",
		start: function(event, ui) {
			indexBefore = getIndex(ui.item, $('#question-options li'));
		},
		stop: function(event, ui) {
			var indexAfter = getIndex(ui.item, $("#question-options li"));
			if (indexBefore == indexAfter)
				return;
			if (indexBefore < indexAfter) {
				$($("#response-input-each-content li")[indexBefore]).insertAfter(
						$($("#response-input-each-content li")[indexAfter]));
			}
			else {
				$($("#response-input-each-content li")[indexBefore]).insertBefore(
						$($("#response-input-each-content li")[indexAfter]));
			}
			renumberAfterOptionSort();
		}
	}).disableSelection();

	$("#question-options").enableSelection();

	$('input[type=file]').on('change', prepareUpload);

	$("#formModalLabel").html("Add Question");

	//hideTitles();
	//clientDropDownEvent_FilterTitle();

	//hidePages();
	//titleDropDownEvent_FilterPage();

	hidePages();

	clientDropDownEvent_FilterPage();

	filterSurveyTypeDropDown();

	configureAddQuestion();

	resetResponseContextChoice();

	toggleQuestionFrench(false);

	setUpQuestionNavigation();

	questionListView('edit');

	setOptionPrefixes();

});


function prepareUpload(event) {
	files = event.target.files;
}

function reInit() {
	$(".btn.btn-primary.form-modal-submit").html("Submit");
	$("label[for='question_english']").html('Question');
	$("#formModalLabel").html("Add Question");
	clearQuestionInput();
	clearReponseInput();
	indexBefore = -1;
	correct_answer_set = false;
	configureAddQuestion();
	resetResponseContextChoice();
	$("#correct_answer").val("");
	$('#response-scope-filter').val("1");
	$('#questiontype').val("1");
	$(".response-text-panel").each(function(event) {
		$(this).hide();
	});
	$("#optionimages").html();
	$("#optionimages").append(getOptionMediaInputHTML(1, "image"));
	$("#optionvideos").append(getOptionMediaInputHTML(1, "video"));
	$("#fieldset-question-options").css({display: "block"});
	surveyType = filterSurveyTypeDropDown();
	if (surveyType == "cefeedback")
		filterFeedbackQuestionTypeDropDown();
	setOptionPrefixes();
}

function refreshPageAndClientId() {
	var csf = $("#client-survey-form");
	var pagenum = Number($("#survey-questions li").length + 1);
	$("#form-modal").find("#client").val($(csf).find("#client").val());
	$("#form-modal").find("#page").val($(csf).find("#page").val());
	$("#form-modal").find("#order").val(pagenum);
}
function clearQuestionInput() {
	$("#add-question-form").find("#image").val("");
	$("#add-question-form").find("#video").val("");
	$("#add-question-form").find("#question_english").val("");
	$("#add-question-form").find("#question_french").val("");
}
function clearOptionInput() {
	$("#question-options").html("");
}
function clearReponseInput() {
	$("#response-input-all").find(".inner textarea").val("");
	$("#response-input-right").find(".inner textarea").val("");
	$("#response-input-wrong").find(".inner textarea").val("");
	$("#response-input-each-content").html("");
}
//tracks the correct answer checkboxes
function setAnswer(cbox) {

	//the answer expressed as boolean for each selected option e.g. "0100"
	var currentanswer = $("#correct_answer").val();

	var qtype = $('#questiontype').val();
	var ischecked = $(cbox).is(':checked');

	//if true/false or single selection
	if (ischecked && (qtype == 1 || qtype == 2) && currentanswer.indexOf("1") != -1) {
		//uncheck the checkbox
		$(cbox).prop('checked', false);
		alert("There can only be one answer for this question type.");
	} else {
		var newanswer = [];
		$("#question-options").find("li").each(function(event) {
			currentanswer = "";
			var value = ($(this).find("input:checkbox").is(':checked')) ? "1" : "0";
			newanswer.push(value);
		});
		currentanswer = newanswer.join("");
	}

	$("#correct_answer").val(currentanswer);
	correct_answer_set = (currentanswer.indexOf("1") == -1) ? false : true;
}

//get question for editing
function getQuestion(id) {
	populateQuestionForm(id);

}

function getOptionMediaInputHTML(n, type) {
	var id = (type == "image") ? "option_image_input" + n : "option_video_input" + n;
	return '<input type="file" name="file" class="form-control" id="' + id + '" value="" >';
}

function configOptionMediaInputs(n) {
	$("#optionimages").find("input[type=file]").each(function(evt) {
		$(this).hide();
	});
	$("#optionvideos").find("input[type=file]").each(function(evt) {
		$(this).hide();
	});
	$("#option_image_input" + n).show();
	$("#option_video_input" + n).show();
}

function populateQuestionForm(id) {

	// for options and responses, sql has returned in ascending order

	reInit();

	$(".btn.btn-primary.form-modal-submit").html("SAVE EDITS");
	$("#formModalLabel").html("Edit Question");

	var question_list_view = $("#question_" + id);
	var questionId = $(question_list_view).find("#id").val();
	//configure main area
	$("#add-question-form").find("#id").val(questionId);
	$("#add-question-form").find("#order").val($(question_list_view).find("#order").val());
	$("#add-question-form").find("#client").val($(question_list_view).find("#client_id").val());

	$("#add-question-form").find("#surveyid").val($(question_list_view).find("#survey_id").val());
	var imageval = ($(question_list_view).find("#image").val() == "") ? "no image" : $(question_list_view).find("#image").val();
	$("#add-question-form").find("#image").parent().next("p").html(imageval);
	if (imageval !== "no image") {
		$("#add-question-form").find("#image").parent().parent().next("td").html(getFileDeleteBtn("question", "image", questionId));
	}
	var videoval = ($(question_list_view).find("#video").val() == "") ? "no video" : $(question_list_view).find("#video").val();
	$("#add-question-form").find("#video").parent().next("p").html(videoval);
	if (videoval != "no video") {
		$("#add-question-form").find("#video").parent().parent().next("td").html(getFileDeleteBtn("question", "video", questionId));
	}
	$("#add-question-form").find("#question_english").val($(question_list_view).find("#question_english").val());
	$("#add-question-form").find("#question_french").val($(question_list_view).find("#question_french").val());

	$("#add-question-form").find("#question_id").val($(question_list_view).find("#question_id").val());

	$("#add-question-form").find("#questiontype").val($(question_list_view).find("#type").val());
	//is_random not implemented yet on questions
	//$(question_list_view).find("#israndom");


	//configure option area
	var option_num = $(question_list_view).find("#num_options").val();
	if (option_num > 1) {
		for (var i = 2; i <= option_num; i++) {
			//append the needed option fields
			$("#question-options").append(addOption(i));
			$("#optionimages").append(getOptionMediaInputHTML(i, "image"));
			$("#optionvideos").append(getOptionMediaInputHTML(i, "video"));
			$("#response-input-each").find(".inner").append(addResponse(i));
		}
	}

	for (var i = 0; i < option_num; i++) {
		var option = $(question_list_view).find("#options" + (i + 1)).val();
		if (option != "") {

			option = option.replace(/\\\\'/g, "&#39;");
			option = option.replace(/\|\|/g, "&#34;");
			option = singleToDouble(option);
			var option_decode = JSON.parse(option);
			if (i == 0)
				var optionPrefix = option_decode.prefix;
			$("#question-options li").eq(i).find('textarea').html(option_decode.option_english);
			$("#question-options li").eq(i).find('#option_image').val(option_decode.image);
			$("#question-options li").eq(i).find('#option_video').val(option_decode.video);
			$("#question-options li").eq(i).find('#option_type').val(option_decode.type);
			$("#question-options li").eq(i).find('#option_nextquestionid').val(option_decode.next_question);
		}
	}

	//set the option prefix drop down
	$("#option_prefix option").each(function() {
		if ($(this).text() == optionPrefix) {
			$(this).attr('selected', 'selected');
		}
	});
	setOptionPrefixes();

	//configure response area
	var response_select = $(question_list_view).find("#responsescope");
	var responsescope = $(response_select).val();
	$("#response-scope-filter").val(responsescope);
	var responsescope_verbose = $("#response-scope-filter option:selected").text();
	showResponseOpenerContext();

	//populate response inputs
	var response_num = $(question_list_view).find("#num_responses").val();

	if (responsescope_verbose == 'One standard response' || responsescope == '2') {
		var response_all = $(question_list_view).find("#responses1").val();
		response_all = processQuotes(response_all);
		response_all = singleToDouble(response_all);
		var response_all_decode = JSON.parse(response_all);
		var response_all_e = reapplyQuotes(response_all_decode.response_english);
		$("#response-input-all").find(".inner").find("textarea").val(response_all_e);
	}

	if (responsescope_verbose == 'Right and wrong responses' || responsescope == '3') {
		//response order will be wrong=1(0 in database), right=2(1 in database)
		var response_wrong = $(question_list_view).find("#responses1").val();
		response_wrong = processQuotes(response_wrong);
		response_wrong = singleToDouble(response_wrong);
		var response_wrong_decode = JSON.parse(response_wrong);
		var response_we = reapplyQuotes(response_wrong_decode.response_english);
		$("#response-input-wrong").find(".inner").find("textarea").val(response_we);

		var response_right = $(question_list_view).find("#responses2").val();
		response_right = processQuotes(response_right);
		response_right = singleToDouble(response_right);
		var response_right_decode = JSON.parse(response_right);
		var response_re = reapplyQuotes(response_right_decode.response_english);
		$("#response-input-right").find(".inner").find("textarea").val(response_re);
	}

	if (responsescope_verbose == 'One response per option' || responsescope == '4') {
		var response_panel_each = $("#response-input-each").find(".inner");
		for (var i = 1; i <= option_num; i++) {
			var response = $(question_list_view).find("#responses" + i).val();
			response = processQuotes(response);
			response = singleToDouble(response);
			var response_decode = JSON.parse(response);
			var response_option_e = reapplyQuotes(response_decode.response_english);
			$(response_panel_each).find("#response" + i).html(response_option_e);
		}
	}

	//configure correct answer
	var correctanswer = $(question_list_view).find("#correctanswer").val();
	$("#add-question-form").find("#correct_answer").val(correctanswer);

	//configure the checkboxes per the answer
	var correctanswer_parts = correctanswer.split("");
	var answer_value;

	for (var p = 0; p < correctanswer_parts.length; p++) {
		answer_value = correctanswer_parts[p];
		if (answer_value == "1") {
			$("#question-options").find("#optionblock" + (p + 1)).find("input:checkbox").prop('checked', true);
		}
	}


	showQuestionFormModal();
}

function modifyConfigBySurveyType() {
	surveyType = filterSurveyTypeDropDown();
	//$("#client-survey-form").find("#type option:selected").text();
	var hideshowSet = [];
	$("#fieldset-question-media").hide();
	switch (surveyType) {
		case "cefeedback":
			hideshowSet = [
				["#fieldset-question-media", false],
				["#fieldset-question-responses", false],
				[".btn-open-optionoptions", false]
			];
			addMedia = false;
			break;
		default:
			hideshowSet = [
				["#fieldset-question-media", true],
				["#fieldset-question-responses", true],
				[".btn-open-optionoptions", true]
			];
			addMedia = true;
			break;
	}
	var formItem;
	for (form in hideshowSet) {
		formItem = hideshowSet[form][0];
		if (formItem.substring(0, 1) == ".") {
			//manage all class elements
			displayElementByClass(formItem, hideshowSet[form][1]);
		}
		if (hideshowSet[form][1] == true) {
			$(formItem).show();
		} else {
			$(formItem).hide();
		}
	}
}

function filterSurveyTypeDropDown(  ) {
	var surveyType = $("#client-survey-form #type option:selected").text();
	var pointerEventValue;
	var opacityValue;
	var hideDisplayValue;
	var showDisplayValue;
	var optionInstruction;
	if (surveyType == "cefeedback") {
		pointerEventValue = "none";
		opacityValue = 0.4;
		hideDisplayValue = "none";
		showDisplayValue = "initial";
		optionInstructions = optionInstructions_ce;
	} else {
		pointerEventValue = "auto";
		opacityValue = 1;
		hideDisplayValue = "initial";
		showDisplayValue = "none";
		optionInstructions = optionInstructions_default;
	}
	for (form in conditionalFieldsCE) {
		switch (conditionalFieldsCE[form][1]) {
			case "hide":
				$(conditionalFieldsCE[form][0]).parent().css({display: hideDisplayValue});
				break;
			case "show":
				$(conditionalFieldsCE[form][0]).parent().css({display: showDisplayValue});
				break;
			case "fade":
				$(conditionalFieldsCE[form][0]).parent().css({pointerEvents: pointerEventValue, opacity: opacityValue});
				break;
			case "changetext":
				$(conditionalFieldsCE[form][0]).html(optionInstructions);
				break;
		}
	}
	return surveyType;
}

function filterFeedbackQuestionTypeDropDown(  ) {
	var selected = $("#feedback_questiontype_filter option:selected").text();
	var id = Number($("#feedback_questiontype_filter option:selected").val());
	var order = $("#feedback_questiontype_filter")[0].selectedIndex;
	var labelData = feedbackQuestionTypeLabels[order].data.split("|");
	clearOptionInput();
	for (var i = 1; i <= labelData.length; i++) {
		$("#question-options").append(addOption(i));
		$("#optionblock" + i).find("textarea").html(labelData[i - 1]);
	}
	if (labelData[0] == "Comment") {
		$("label[for='question_english']").html('Comments heading:');
		optionDisplay = "none";
	} else {
		$("label[for='question_english']").html('Question');
		optionDisplay = "block";
	}
	$("#fieldset-question-options").css({display: optionDisplay});
	setOptionPrefixes();
}

function displayElementByClass(element, bool) {
	var displayValue = (bool == false) ? "none" : "initial";
	$(element).each(function() {
		$(this).css({display: displayValue});
	});
}

function showQuestionFormModal() {
	modifyConfigBySurveyType();
	$('#form-modal').modal('show');
}
//tracks the option index order
function getIndex(itm, list) {
	var i;
	for (i = 0; i < list.length; i++) {
		if (itm[0] == list[i])
			break;
	}
	return i >= list.length ? -1 : i;
}

//renumbers the options and responses after a sort
function renumberAfterOptionSort() {

	setOptionPrefixes();

}

function setOptionPrefixes() {

	var prefix = $("#option_prefix option:selected").text();

	switch (prefix) {
		case "A":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html(letters[ $(this).index() ].toUpperCase());
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html(letters[ $(this).index() ].toUpperCase());
			});
			break;
		case "A.":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html(letters[ $(this).index() ].toUpperCase() + ".");
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html(letters[ $(this).index() ].toUpperCase() + ".");
			});
			break;
		case "a":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html(letters[ $(this).index() ]);
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html(letters[ $(this).index() ]);
			});
			break;
		case "a.":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html(letters[ $(this).index() ] + ".");
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html(letters[ $(this).index() ] + ".");
			});
			break;
		case "#":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html(Number($(this).index() + 1));
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html(Number($(this).index() + 1));
			});
			break;
		case "#.":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html(Number($(this).index() + 1) + ".");
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html(Number($(this).index() + 1) + ".");
			});
			break;
		case "none":
			$("#question-options").find("li").each(function(event) {
				$(this).find("#option-letter").html("");
			});
			$("#response-input-each-content").find("li").each(function(event) {
				$(this).find("#response-letter").html("");
			});
			break;
	}
}
//open option details
function showOptionOptions(n) {
	$("#option_saved_input_id").val(n);
	//clear the fields
	$("#option_image_input").val("");
	$("#option_video_input").val("");
	$("#option_next_input").val("");
	$("#optionimages").next('button').remove();
	$("#optionvideos").next('button').remove();
	//transfer the saved hidden values for editing
	var image = $("#optionblock" + n).find("#option_image").val();
	var video = $("#optionblock" + n).find("#option_video").val();
	var nextid = $("#optionblock" + n).find("#option_nextquestionid").val();

	//if this is being edited, get saved values
	var optiondata = $("#options" + n).val();
	if (optiondata != "" && optiondata != undefined) {
		optiondata = optiondata.replace(/\\\\'/g, "&#39;");
		optiondata = optiondata.replace(/\|\|/g, "&#34;");
		optiondata = singleToDouble(optiondata);
		var option_decode = JSON.parse(optiondata);
		var order = option_decode.order;
	}

	$("#optionimages").next("p").html(filterFileUploadName(image));
	if (image != "")
		$("#optionimages").after(getFileDeleteBtn("option", "image", order));
	$("#optionvideos").next("p").html(filterFileUploadName(video));
	if (video != "")
		$("#optionvideos").next("p").before(getFileDeleteBtn("option", "video", order));
	configOptionMediaInputs(n);

	if (nextid != "")
		$("#option_next_input").val(nextid);

	$("#option-options").show();
}

function saveOptionOptions() {
	var optionid = $("#option_saved_input_id").val();
	//transfer the saved hidden values for editing
	var image = $("#option_image_input" + optionid).val();
	var video = $("#option_video_input" + optionid).val();
	var nextid = $("#option_next_input").val();
	if (image != "")
		$("#optionblock" + optionid).find("#option_image").val(filterFileUploadName(image));
	if (video != "")
		$("#optionblock" + optionid).find("#option_video").val(filterFileUploadName(video));
	if (nextid != "")
		$("#optionblock" + optionid).find("#option_nextquestionid").val(nextid);

	$("#option-options").hide();
}

//deletes an option and its response
function optionDeleteAction(btn) {
	var index = $(btn).closest('li').index();
	$(btn).closest('li').remove();
	$("#response-input-each-content li").eq(index).remove();
	renumberAfterOptionSort();
}

function configureResponseConfigDropDown() {
	if ($('#responseconfig').val() != 2) {
		toggleResponseConfigDropDown(false);
	}
}

function toggleResponseConfigDropDown(bool) {
	if (bool == true) {
		$('#responseconfig').show();
		$('label[for="responseconfig"]').show();
	} else {
		$('#responseconfig').hide();
		$('label[for="responseconfig"]').hide();
	}
}

function toggleQuestionFrench(bool) {
	var form = $("#add-question-form");
	if (bool == true) {
		$(form).find('#question_french').show();
		$(form).find('label[for="question_french"]').show();
	} else {
		$(form).find('#question_french').hide();
		$(form).find('label[for="question_french"]').hide();
	}
}

function configureAddQuestion() {
	$("#question-options").html(addOption(1));
	$("#response-input-each-content").html(addResponse(1));
}

function showResponseOpenerContext() {
	var choice = $('#response-scope-filter').val();
	if (responseContextChoice != undefined)
		$(responseContextChoice).addClass("hide");
	switch (choice) {
		case '1':
			resetResponseContextChoice();
			break;
		case '2':
			responseContextChoice = "#btn-input-single-response";
			break;
		case '3':
			responseContextChoice = "#btn-input-rightwrong-response";
			break;
		case '4':
			responseContextChoice = "#btn-input-peroption-response";
			break;
	}
	$(responseContextChoice).removeClass("hide");
}

function resetResponseContextChoice() {
	if (responseContextChoice != null)
		$(responseContextChoice).addClass("hide");
	responseContextChoice = null;
}

function addOption(id) {

	var surveyType = $("#client-survey-form #type option:selected").text();

	var mediaButtonHTML = '<td align="right">';
	mediaButtonHTML += '<span class="label label-success btn-open-optionoptions" ';
	mediaButtonHTML += ' onclick="showOptionOptions(' + id + ');" title="Options">+</span>';
	mediaButtonHTML += '</td>';
	var mediaButton = (addMedia == true) ? mediaButtonHTML : "";

	var correctCheckHTML = '<input class="set-correct-answer" type="checkbox" onclick="setAnswer(this)" >';
	var correctAnswerCheck = (surveyType == "cefeedback") ? "" : correctCheckHTML;
	var tagButton = '<span class="label label-success btn-open-optionoptions" title="Add tag to this option" onClick="showTagBox('+id+')">+</span>';
	var optionType = '<span class="label label-success btn-open-optionoptions" title="Set option type" onClick="setOptionType('+id+')">+</span>';
	var html = '<li class="page-list-item list-group-item" id="optionblock' + id + '" rel="">';
	html += '<table cols="5" class="fullWidth">';
	html += '<tbody><tr>';
	html += '<td id="option-letter">' + letters[id - 1] + '.</td>';
	html += '<td>';
	html += '<div class="formElementBlock form-group">';
	html += '<textarea rows="3" name="option' + id + '" id="option' + id + '" value="" class="form-control"></textarea>';
	html += '</div>';
	html += '</td>';
	html += '<td align="right">';
	html += correctAnswerCheck;
	html += '<input type="hidden" id="option_image" value="">';
	html += '<input type="hidden" id="option_video" value="">';
	html += '<input type="hidden" id="option_nextquestionid" value="">';
	html += '<input type="hidden" id="option_type" value="1">';//1 is default
	html += '</td>';
	html += '<td align="right" class="btn-delete-option" onclick="optionDeleteAction(this);">';
	html += '<span class="label label-danger" title="Delete">X</span>';
	html += '</td>';
	html += mediaButton;
	html	+=	'<td align="right">';
	html	+=	 tagButton;
	html	+=	'</td>';
	html +=	'<td align="right">';
	html += optionType;
	html += '</td>';
	html += '</tr>';
	html += '</tbody></table>';
	html += '</li>';
	return html;
}

function addResponse(id) {
	var html = '<li class="page-list-item list-group-item">';
	html += '<table cols="2" class="fullWidth">';
	html += '<tbody><tr>';
	html += '<td id="response-letter">' + letters[id - 1] + '.</td>';
	html += '<td>';
	html += '<div class="formElementBlock form-group">';
	html += '<textarea rows="3" name="response' + id + '" id="response' + id + '" value="" class="form-control"></textarea>';
	html += '</div>';
	html += '</td>';
	html += '</tr>';
	html += '</tbody></table>';
	html += '</li>';
	return html;
}

function setUpQuestionNavigation() {
	try {
		if (num_surveyQuestions > PAGINATOR.rangeLimit) {
			paginatorHandler(1);
		}
	}
	catch (error) {
	}
}

function showTagBox(id){
	$("#addTagToQuestion").modal();
	console.log($("#option"+id).val());
	$("#addTagToQuestion").find("#question_id").val($("#option"+id).val());
	console.log($("#addTagToQuestion").find("form").attr("action"));
	$("#addTagToQuestion").find("form").attr("action","/admin/client/option-tag");
}

function setOptionType(id){

	$("#addOptionType").modal();
	$("#option_id").val(id);
	console.log($("#add-question-form").find("#id").val());
	$("#addOptionType").find("#question_id").val($("#add-question-form").find("#id").val());
	$("#addOptionType").find("#option_value").val($("#option"+id).val());
	$("#path_name").val(window.location.pathname);
}

function paginatorHandler(param) {
	$('#survey-questions li').show();
	var topLimit = (param * PAGINATOR.rangeLimit) - 1;
	var bottomLimit = (topLimit - PAGINATOR.rangeLimit) + 1;
	$('#survey-questions li:lt(' + bottomLimit + ')').hide();
	$('#survey-questions li:gt(' + topLimit + ')').hide();
}

function questionListView(mode) {
	switch (mode) {
		case 'edit':
			if ($('#survey-questions').hasClass('ui-sortable'))
				$('#survey-questions').sortable('destroy');
			$("#btn-sort-mode").show();
			$("#btn-edit-mode").hide();
			$('#survey-questions li').find(":button").show();
			$("#btn-add-question").show();
			$('.pagination').removeClass('show-no-events');
			try {
				if (PAGINATOR)
					PAGINATOR.handleClick(1);
			}
			catch (e) { /*no paginator*/
			}
			break;
		case 'sort':
			$('#survey-questions').sortable({
				update: function(event, ui) {
					reorderHandler();
				}
			});
			$("#btn-sort-mode").hide();
			$("#btn-edit-mode").show();
			$('#survey-questions li').find(":button").hide();
			$('#survey-questions li').show();
			$("#btn-add-question").hide();
			$('.pagination').addClass('show-no-events');
			break;
	}
}

function reorderHandler() {
	var li_counter = 1;
	var postdata = new Object();
	var question_reorder_list = [];
	var question_pair = new Object();
	$("#survey-questions li").each(function(event) {
		question_pair = new Object();
		var question = $(this);
		$(this).find("#order").val(li_counter);
		var e_text = $("#question_english", question).val();
		var id = $("#id", question).val();
		var order = $("#order", question).val();
		question_pair.id = id;
		question_pair.order = order;
		question_reorder_list.push(question_pair);
		li_counter++;
	});
	postdata.question_list = question_reorder_list;

	$.ajax({
		url: "/admin/ajax/update-question-order",
		type: "POST",
		data: postdata,
		dataType: "json"
	})
			.done(function(data) {
				if (data.meta.code == 200 && data.meta.error == '') {
					alert('Questions reordered');
					//refresh page:
					window.location.reload();
				} else {
					//alert('Add question failed: ' + JSON.stringify(data) );
					alert('Question reordering failed: ' + data.meta.error);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				alert("Question reordering request failed: " + textStatus);
			});
}

function writeSuggestedSlug() {
	var getSlug = filterSurveyNameToSlug();
	$("#add-question-form #question_id").val(getSlug);

}
//filtering
function singleToDouble(str) {
	return str.replace(/'/g, '"');
}

function processQuotes(str) {
	str = str.replace(/\\\\'/g, "&#39;");
	str = str.replace(/\|\|/g, "&#34;");
	return str;
}

function reapplyQuotes(str) {
	var newstr = str.replace(/&#39;/g, "'");
	newstr = newstr.replace(/&#34;/g, "\"");
	return newstr;
}
function filterSurveyNameToSlug() {
	var quizTitle = $("#client-survey-form #name").val().split("");
	var quizSlug = "";
	for (letter in quizTitle) {
		if (/[a-zA-Z]/.test(quizTitle[letter])) {
			if (quizTitle[letter] === quizTitle[letter].toUpperCase())
				quizSlug += quizTitle[letter].toUpperCase();
		}
	}

	return quizSlug + "_" + $("#add-question-form #order").val();
}

$(document).ready(function() {

	$("button[class='close']").click(function(){
		$("#form-modal").modal('hide');
	});

	$("#btn-add-tag").click(function() {
		$("#addTagToSurvey").modal('show');
	});

	$("#addTagToSurvey").find(".btn-primary").click(function() {
		$("#addTagToSurvey").find("form").submit();
	});

	$("#addOptionType").find(".btn-primary").click(function(){
		$("#addOptionType").find("form").submit();
	});

	$("#add-tag-to-question").click(function(e) {
		$("#addTagToQuestion").find("form").attr("action","/admin/client/question-tag");
		var myVal = $("#add-question-form").find("#id").val();
		$(this).attr("question_id", myVal);
		$("#addTagToQuestion").find("#question_id").val(myVal);
		$("#addTagToQuestion").modal();
	});

	$("#addTagToQuestion").find(".btn-primary").click(function() {
		//alert("this button was clicked");
		$(this).parent().parent().find("form").submit();
	});

	$("#btn-delete-tag").click(function() {
		$('#tag-listings').find('.checkbox').each(function() {
			if ($(this).hasClass('checked')) {
				console.log($.ajax({}));
				$.ajax({
					url: '/admin/client/delete-survey-tag/id/' + $(this).attr('tag_id'),
					success: function() {
						window.location.reload();
					}
				});

			}
		});
	});

	$("#delete-tag-from-question").click(function(){
		var myVal = $("#add-question-form").find("#id").val();
		$(this).attr("question_id", myVal);
		$(this).parent().find(".to_delete").each(function(e){
			if($(this).is(":checked")){
			$.ajax({
				url:'/admin/client/delete-question-tag/id/'+$(this).attr("tag_id"),
				success:function(){
					window.location.reload();
				}
			});
		}
		});
	});
});