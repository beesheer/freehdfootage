/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
	$location = "/client/document";

	function resetTag() {
		$("#myModal").find("#name").prev("label").html("Tag");
	}
	$("#addtag").click(function() {
		resetTag();
		$("#myModal").modal('show');
	});

	$("#myModal").find("#name").keypress(function() {
		resetTag();
	});

	$("#addChildTag").click(function() {
		$("#newChildTag").modal('show');
	});

	$("#newChildTag").find(".btn-primary").click(function(e) {
		$("#newChildTag").find("form").submit();
	});

	$("#myModal").find(".btn-primary").click(function(e) {
		if ($("#myModal").find("#name").val() !== "") {
			$("#myModal").find("form").submit();
		}
		else
		{
			$("#myModal").find("#name").css({"border-style": "solid", "border-width": "2px", "border-color": "red"});
			$("#myModal").find("#name").prev("label").html("Tag  - <h3 style='color:red'>Please enter a value for tag name!");
		}
	});

	$("#delete-tag").click(function() {
		$checked = $.find(".checked");
		for ($j in $checked) {
			$($checked[$j]).attr("tag-id");
			$.ajax({
				url: "/admin/client/delete-tag",
				method: "POST",
				data: "id=" + $($checked[$j]).attr("tag-id"),
				success: function(e) {
					alert(e);
					window.location.href = $location;
				}
			});
		}
	});

	$("#delete-child-tag").click(function() {
		$checked = $.find(".checked");
		for ($j in $checked) {
			$($checked[$j]).attr("tag-id");
			$.ajax({
				url: "/admin/client/delete-child-tag",
				method: "POST",
				data: "id=" + $($checked[$j]).attr("tag-id"),
				success: function(e) {
					alert(e);
					window.location.href = $location;
				}
			});
		}
	});

	$("#update-child-tag").click(function() {
		$checked = $.find(".checked");
		if ($checked.length > 1) {
			alert("You may update only one item at a time");
		}
		else if ($checked.length === 1)
		{
			$("#updateExistingChildTag").modal("show");
			$("#updateExistingChildTag").find("input").val($($checked).parent("td").parent("tr").children("td")[0].innerHTML);
			$("#updateExistingChildTag").find(".btn-primary").click(function() {
				$.ajax({
					url: "/admin/client/update-child-tag",
					method: "POST",
					data: "id=" + $($checked).attr("tag-id") + "&name=" + $("#do-child-update").val(),
					dataType: "json",
					success: function(e) {
						if (e.success === true) {
							$("#updateExistingChildTag").modal("hide");
							window.location.href = $location;
						}
					}
				});
				return false;
			});
		}
		return false;
	});


	$("#update-tag").click(function() {
		$checked = $.find(".checked");
		if ($checked.length > 1) {
			alert("You may update only one item at a time");
		}
		else if ($checked.length === 1)
		{
			$("#updateExistingTag").modal("show");
			$("#updateExistingTag").find("input").val($($checked).parent("td").parent("tr").children("td")[0].innerHTML);
			$("#updateExistingTag").find("select").val(parseInt($($checked).attr("client-id")));

			$("#updateExistingTag").find(".btn-primary").click(function() {
				$.ajax({
					url: "/admin/client/update-tag",
					method: "POST",
					data: "id=" + $($checked).attr("tag-id") +
							"&name=" + $("#updateExistingTag").find("#name").val() +
							"&client_id=" + $("#updateExistingTag").find("select").val(),
					dataType: "json",
					success: function(e) {
						if (e.success === true) {
							$("#updateExistingTag").modal("hide");
							window.location.href = $location;
						}
					}
				});
				return false;
			});
		}
		return false;
	});

	$("td").unbind("click");
	$("tr").unbind("click");
	$("table").unbind("click");


});

