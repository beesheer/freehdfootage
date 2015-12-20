$(function() {
	
	$("body").on("click", ".contact-list-item", function() {
		if (parseInt($(this).children(".contact-list-item-del").css("margin-left")) < 0) {
			action = "update";
			contactID = $(this).attr("cid");


			for (var i in contacts) {

				if (contacts[i]["id"] == contactID) {




					$("#contact-detail-firstname-input").val(contacts[i]["firstname"]);
					$("#contact-detail-surname-input").val(contacts[i]["surname"]);
					$("#contact-detail-email-input").val(contacts[i]["email"]);
					$("#contact-detail-company-input").val(contacts[i]["company"] == " Unknown" ? "" : contacts[i]["company"]);
					$("#contact-detail-title-input").val(contacts[i]["title"]);
				}
			}

			$("#contact-detail-label-email").removeClass("has-error");
			$("#contact-detail-email-input-error-message").hide();

			$("#contact-detail-title").html("Edit Contact");
			$("#contact-detail").css({
				"top": $(window).scrollTop() + "px"
			});

			$("#contact-detail-submit").html("Update");
			//$("#overlay, #contact-detail").fadeIn();

			$('#form-modal').modal('show');
		} else {
			$(this).children(".contact-list-item-del-icon").animate({
				"margin-right": "0px"
			});
			$(this).children(".contact-list-item-del").animate({
				"margin-left": "-100px"
			});
			$(this).children(".contact-list-item-text").animate({
				"margin-left": "0px"
			});
		}
	});

	$('.delete-modal-submit').click(function(event){

        var that = this;
		service.removeContact(
			contactID,
			function(data) {
				if (data.data.meta.code == 200) {
					
					service.getContacts(function(response) {
						contacts = response.data.contacts;
						sortContacts();
						showContacts();

						$('#delete-modal').modal('hide');

					});

				} else {
					alert("Failed to delete contact.");
				}
			}
		);

    });
	
	$("body").on("click", ".contact-list-item-del-icon", function(event) {
		event.stopPropagation();

		console.log("CONTACt ID = "+contactID );

		contactID = $(this).parent().attr("cid");

		$('#delete-modal').modal('show');

		/*
		$(this).animate({
			"margin-right": "-50px"
		});
		$(this).parent().children(".contact-list-item-del").animate({
			"margin-left": "0px"
		});
		$(this).parent().children(".contact-list-item-text").animate({
			"margin-left": "100px"
		});
		*/
	});
	
	

	$("#contact-detail-email-input").keyup( function( event ){
		$("#contact-detail-label-email").removeClass("has-error");
		$("#contact-detail-email-input-error-message").hide();
	});
	
	
	$("#contact-add").click(function() {
		action = "add";

		$("#contact-detail-firstname-input").val("");
		$("#contact-detail-surname-input").val("");
		$("#contact-detail-email-input").val("");
		$("#contact-detail-company-input").val("");
		$("#contact-detail-title-input").val("");
		$("#contact-detail-title").html("Add A New Contact");
		$("#contact-detail").css({
			"top": $(window).scrollTop() + "px"
		});

		$("#contact-detail-submit").html("Add");

		$("#contact-detail-label-email").removeClass("has-error");
		$("#contact-detail-email-input-error-message").hide();
		//$("#overlay, #contact-detail").fadeIn();

		$('#form-modal').modal('show');
	});
	
	$("#contact-sort").change(function() {
		sortBy = $("#contact-sort").val();
		sortContacts();
		showContacts();
	});
	
	$("#contact-detail-submit").click(function() {

		var re = /\S+@\S+\.\S+/;

		var email = $("#contact-detail-email-input").val();
		var firstname = $("#contact-detail-firstname-input").val();
		var surname = $("#contact-detail-surname-input").val();
		var company = $("#contact-detail-company-input").val();
		var title = $("#contact-detail-title-input").val();

		if( email.length == 0 ){
			$("#contact-detail-label-email").addClass("has-error");
			$("#contact-detail-email-input-error-message").show();
			$("#contact-detail-email-input-error-message").html("Please add an email address.");
			return;
		}

		if( !checkIfEmailIsUnique( email ) && action == "add" ){
			$("#contact-detail-label-email").addClass("has-error");
			$("#contact-detail-email-input-error-message").show();
			$("#contact-detail-email-input-error-message").html("You already have a contact with the same email address.");
			return;
		}

        if( re.test( email ) ){

        	$("#contact-detail-label-email").removeClass("has-error");

            if (action == "add") {


			

				service.addContact({
						"firstname"	: firstname,
						"surname"	: surname,
						"email"		: email,
						"company"	: company,
						"title"		: title
					}, function(data) {
						if (data.data.meta.code == 200) {
							$('#form-modal').modal('hide');
							service.getContacts(function(response) {
								contacts = response.data.contacts;
								sortContacts();
								showContacts();
							});


						} else
							alert("Failed to add new contact.");
				});
			} else if (action == "update") {
				service.updateContact({
						"id"		: contactID,
						"firstname"	: firstname,
						"surname"	: surname,
						"email"		: email,
						"company"	: company,
						"title"		: title
					}, function(data) {
						if (data.data.meta.code == 200) {
							$('#form-modal').modal('hide');
							service.getContacts(function(response) {
								contacts = response.data.contacts;
								sortContacts();
								showContacts();
							});
						} else
							alert("Update failed.");
				});
			}

        } else {
        	$("#contact-detail-label-email").addClass("has-error");
        	$("#contact-detail-email-input-error-message").show();
			$("#contact-detail-email-input-error-message").html("Email address is invalid.");
        }

		
	});

	function checkIfEmailIsUnique( email ){

		for (var i in contacts) {

				if (contacts[i]["email"] == email) {
					return false;
				}
			}

		return true;
	}
	
	/*
	$(".popup-cancel").click(function() {
		$("#overlay, .popup").fadeOut();
	});
	
	$(".ion-close").click(function() {
		$("#overlay, .popup").fadeOut();
	});
	*/

    // Simulate user scroll to fix menu positioning after ios keyboard is closed
    $(document).on('blur', 'input, textarea', function () {
        setTimeout(function () {
            window.scrollTo(document.body.scrollLeft, document.body.scrollTop);
        }, 0);
    });
	
});