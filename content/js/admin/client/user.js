$(function () {
	// Create new user
	$('.form-modal-submit').click(function (event) {
		var clientName = $('#client_name').val();
		var clientType = $('#client_type').val();

		// Validation
		if ($('#email').val() == '') {
			alert('Please enter email');
			$('#email').parents('.form-group').addClass('has-error');
			return false;
		}
		if ($('#password').val() == '') {
			alert('Please enter password');
			$('#password').parents('.form-group').addClass('has-error');
			return false;
		}
		if ($('#surname').val() == '') {
			alert('Please enter surname');
			$('#surname').parents('.form-group').addClass('has-error');
			return false;
		}
		if ($('#firstname').val() == '') {
			alert('Please enter firstname');
			$('#firstname').parents('.form-group').addClass('has-error');
			return false;
		}
		if ($('#role').val() == '') {
			alert('Please select role');
			$('#role').parents('.form-group').addClass('has-error');
			return false;
		}
		if ($('#client').val() == '') {
			alert('Please select client');
			$('#client').parents('.form-group').addClass('has-error');
			return false;
		}

		// Submit
		$.ajax({
			url: "/admin/ajax/create-user",
			type: "POST",
			data: $('#create-new-user-form').serialize(),
			dataType: "json"
		})
				.done(function (data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						window.location.reload();
					} else {
						alert('Create user failed: ' + data.meta.error);
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert("Create user request failed: " + textStatus);
				});
	});

	// Create a new user
	$('#create-new-user').click(function (event) {
		// Change label and set id to empty
		$('#formModalLabel').text('Create New User');
		$('#form-modal').modal('show');
	});

	$(".table.table-striped.table-bordered tbody").addClass("list");

	$("thead tr th").off("mouseup");
	$("th").off("click");
	$("tr.off").off("click");

	var options = {
		valueNames: ['username','email','client','usertype'],
		page: 20,
		plugins: [
			ListPagination({})
		]
	};
	var pageList = new List('user_list', options);

	pageList.sort('username', { order: "asc" });
});