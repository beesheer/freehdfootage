var responseContextChoice;
//for indicating the option order
var letters = "abcdefghijklmnopqrstuvwxyz".split("");
//option li index
var indexBefore = -1;
//whether answer is indicated
var correct_answer_set = false;

$(function(){

    // Survey Form submit.
    $('#question-save-submit').click(function(event){
		var valid = validateForm();
		if(valid) $('#add-question-form').trigger('submit');
    });

	//add a question option
	$('#btn-add-option').click(function(event){
		//check against true false
		if( $('#questiontype').val() == 1 && $("#question-options li").length == 2) {
			alert("True/False questions can only have two options.");
		} else {
			var newnodeid = $("#question-options").find("li").length + 1;
			$("#question-options").append(addOption(newnodeid));
			$("#response-input-each").find(".inner").append(addResponse(newnodeid));
		}
	});

	//main form response type
	$('#responsetype').change(function(event) {
		if( $('#responsetype').val() == 2) {
			$('#responseconfig').show();
		}
	});

	//reponse panel openers
	 $('.open-responseinput').each(function(event) {
		$(this).click(function(event) {
			var id = $(this).attr('id');
			var suffix = id.substring(id.lastIndexOf("-")+1);
			$("#response-input-"+suffix).fadeIn();
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
	$('#questiontype').change(function(event){
		if( $('#questiontype').val() == 1 ) {
			if( $("#question-options").find("li").length > 2 ) {
				var user = confirm("You have chosen true/false format. OK to reduce options to two (input will be lost)?");
				if(user==true) {
					//trim the options
					$("#question-options").find("li").each( function(event) {
						var index = $(this).index();
						if( index > 1 ) {
							$(this).remove();
							$("#response-input-each-content li").eq(index).remove();
						}
					});
				}
			}
		}
	});

	// Connected sortable: ties the responses to the options
    $("#question-options").sortable({
        connectWith: ".connectedSortable",
		 start: function(event, ui) {
			indexBefore = getIndex(ui.item, $('#question-options li'));
		},
		stop: function(event, ui) {
		   var indexAfter = getIndex(ui.item,$("#question-options li"));
		   if (indexBefore==indexAfter) return;
		   if (indexBefore<indexAfter) {
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


	hideTitles();

	clientDropDownEvent_FilterTitle();

	hidePages();

	titleDropDownEvent_FilterPage();

	configureAddQuestion();

	resetResponseContextChoice();

	toggleQuestionFrench(false);

	populateForm();

});


function reInit() {
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
}

function populateForm() {
	var opt = $("#options").val();
	if(opt != "") {
		var options_decode = JSON.parse(opt);
		var option_num = options_decode.length;
		if( option_num>1 ) {
			for( var i=2; i<=option_num; i++ ) {
				//append the needed option fields
				$("#question-options").append ( addOption(i) );
				$("#response-input-each").find(".inner").append(addResponse(i));
			}
		}
		//var alert( $('#question-options li').length );
		// options_decode[0].text
		for(var i=0; i<option_num; i++) {
			$("#question-options li").eq( i ).find('textarea').html( options_decode[i].text );
			$("#question-options li").eq( i ).find('#option_image_input').val( options_decode[i].image );
			$("#question-options li").eq( i ).find('#option_video_input').val( options_decode[i].video );
			$("#question-options li").eq( i ).find('#option_type_input').val( options_decode[i].type );
			$("#question-options li").eq( i ).find('#option_next_input').val( options_decode[i].next_question );
		}
	}
}
// Validate Form
function validateForm() {

	var datavalid = true;
	var postdata = new Object();
	var invalid_message = "";

	//client id

	//gather option text:
	var num_options = $('#question-options').find('li').length;
	var optiondata = [];
	//gather option text:
	var option_count=1;
	var optiondata = [];
	var optiontext = $('#question-options').find('li').each(function(event) {
		var obj = $(this).find('textarea');
		var id = $(obj).attr("id");
		var text = $(obj).val();
		var optionobject = Object();
		if(text=="") datavalid = false; invalid_message += "Some options are empty.\n";
		optionobject.order = option_count;
		optionobject.text = text;
		optionobject.image = $(this).find("#option_image").val();
		optionobject.video = $(this).find("#option_video").val();
		optionobject.optiontype = $(this).find("#option_type").val();
		optionobject.nextquestion = $(this).find("#option_nextquestionid").val();
		//postdata[id] = optionobject;
		optiondata.push(optionobject);
		option_count++;
	});
	$("#options").val( JSON.stringify(optiondata) );

	//gather response data
	//get kind of response
	var responsescope = $('#response-scope-filter').val();
	$("#responsescope").val( responsescope );

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
			if(response_all=="") datavalid = false; invalid_message += "Response is empty.\n";
			$("#response_all").val( response_all );
		break;
		case '3':
			//one right response, one wrong response
			var response_right = $("#response-input-right").find(".inner textarea").val();
			if(response_right=="") datavalid = false; invalid_message += "Response right is empty.\n";
			$("#response_right").val( response_right );
			var response_wrong = $("#response-input-wrong").find(".inner textarea").val();
			if(response_wrong=="") datavalid = false; invalid_message += "Response wrong is empty.\n";
			$("#response_wrong").val( response_wrong );

		break;
		case '4':
			//separate response for each option
			var responsedata = [];
			var response;
			for (var i=1; i<=num_options; i++) {
				var responseobject = Object();
				response = $('#response'+i).val();
				if(response=="") {
					invalid_message += "Responses for each option are incomplete.\n";
					datavalid = false;
					break;
				}
				responseobject.text = response;
				responseobject.order = i;
				responsedata.push( responseobject );
			}

			$("#response_array").val( JSON.stringify(response_array) );

		break;
	}

	if(datavalid==false) {
		alert (invalid_message);
		return false;
	}

	return datavalid;
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
	var currentanswer= $("#correct_answer").val();

	var qtype = $('#questiontype').val();
	var ischecked = $(cbox).is(':checked');

	if( ischecked && (qtype == 1 || qtype == 2) && currentanswer.indexOf("1") != -1 ) {
		//uncheck the checkbox
		$(cbox).prop('checked', false);
		alert("There can only be one answer for this question type.");
	} else {
		var newanswer = [];
		$("#question-options").find("li").each( function(event) {
			currentanswer = "";
			var value = ( $(this).find("input:checkbox").is(':checked') ) ? "1" : "0";
			newanswer.push(value);
		})
		currentanswer = newanswer.join("");
	}

	$("#correct_answer").val(currentanswer);
	correct_answer_set = (currentanswer.indexOf("1") == -1) ? false : true;
}

//tracks the option index order
function getIndex(itm, list) {
    var i;
    for (i = 0; i < list.length; i++) {
        if (itm[0] === list[i]) break;
    }
    return i >= list.length ? -1 : i;
}

//renumbers the options and responses after a sort
function renumberAfterOptionSort() {
	//renumber
	$("#question-options").find("li").each( function(event) {
		$(this).find("#option-letter").html( letters[ $(this).index() ] );
		//var id = $(this).find("textarea").attr('id').substring(6);
	});
	$("#response-input-each-content").find("li").each( function(event) {
		$(this).find("#response-letter").html( letters[ $(this).index() ] );
	});
}

//open option details
function showOptionOptions(n) {
	$("#option_saved_input_id").val(n);
	//clear the fields
	$("#option_image_input").val("");
	$("#option_video_input").val("");
	$("#option_next_input").val("");
	//transfer the saved hidden values for editing
	var image  = $("#optionblock"+n).find("#option_image").val();
	var video  = $("#optionblock"+n).find("#option_video").val();
	var nextid = $("#optionblock"+n).find("#option_nextquestionid").val();
	if(image!="") $("#option_image_input").val( image );
	if(video!="") $("#option_video_input").val( video );
	if(nextid!="") $("#option_next_input").val( nextid );

	$("#option-options").show();
}
function saveOptionOptions() {
	var optionid = $("#option_saved_input_id").val();

	//transfer the saved hidden values for editing
	var image  = $("#option_image_input").val();
	var video  = $("#option_video_input").val();
	var nextid = $("#option_next_input").val();
	if(image!="") $("#optionblock"+optionid).find("#option_image").val( image );
	if(video!="") $("#optionblock"+optionid).find("#option_video").val( video );
	if(nextid!="") $("#optionblock"+optionid).find("#option_nextquestionid").val( nextid );

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
	if($('#responseconfig').val()!=2) {
		toggleResponseConfigDropDown(false);
	}
}

function toggleResponseConfigDropDown(bool) {
	if(bool==true) {
		$('#responseconfig').show();
		$('label[for="responseconfig"]').show();
	} else {
		$('#responseconfig').hide();
		$('label[for="responseconfig"]').hide();
	}
}

function toggleQuestionFrench(bool) {
	if(bool==true) {
		$('#question_french').show();
		$('label[for="question_french"]').show();
	} else {
		$('#question_french').hide();
		$('label[for="question_french"]').hide();
	}
}

function configureAddQuestion() {
	$("#question-options").html( addOption(1) );
	$("#response-input-each-content").html( addResponse(1) );
}

function showResponseOpenerContext() {
	var choice = $('#response-scope-filter').val();
	if(responseContextChoice!=undefined) $(responseContextChoice).addClass("hide");
	switch(choice) {
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
	if(responseContextChoice!=null) $(responseContextChoice).addClass("hide");
	responseContextChoice = null;
}

function addOption(id) {
	var html =	'<li class="page-list-item list-group-item" id="optionblock' + id + '">';
		html	+=	'<table cols="5" class="fullWidth">';
		html	+=	'<tbody><tr>';
		html	+=	'<td id="option-letter">'+letters[id-1]+'.</td>';
		html	+=	'<td>';
		html	+=	'<div class="formElementBlock form-group">';
		html	+=	'<textarea rows="3" name="option' + id + '" id="option' + id + '" value="" class="form-control"></textarea>';
		html	+=	'</div>';
		html	+=	'</td>';
		html	+=	'<td align="right">';
		html	+=	'<input type="checkbox" onclick="setAnswer(this)" >';
		html	+=	'<input type="hidden" id="option_image" value="">';
		html	+=	'<input type="hidden" id="option_video" value="">';
		html	+=	'<input type="hidden" id="option_nextquestionid" value="">';
		html	+=	'<input type="hidden" id="option_type" value="1">';//1 is default
		html	+=	'</td>';
		html	+=	'<td align="right" class="btn-delete-option" onclick="optionDeleteAction(this);">';
		html	+=	'<span class="label label-danger" title="Delete">X</span>';
		html	+=	'</td>';
		html	+=	'<td align="right">';
		html	+=	'<span class="label label-success btn-open-optionoptions" ';
		html	+=	' onclick="showOptionOptions('+id+');" title="Options">+</span>';
		html	+=	'</td>';
		html	+=	'</tr>';
		html	+=	'</tbody></table>';
		html	+=	'</li>';
	return html;
}

function addResponse(id) {
	var html =	'<li class="page-list-item list-group-item">';
		html	+=	'<table cols="2" class="fullWidth">';
		html	+=	'<tbody><tr>';
		html	+=	'<td id="response-letter">'+letters[id-1]+'.</td>';
		html	+=	'<td>';
		html	+=	'<div class="formElementBlock form-group">';
		html	+=	'<textarea rows="3" name="response' + id + '" id="response' + id + '" value="" class="form-control"></textarea>';
		html	+=	'</div>';
		html	+=	'</td>';
		html	+=	'</tr>';
		html	+=	'</tbody></table>';
		html	+=	'</li>';
	return html;
}