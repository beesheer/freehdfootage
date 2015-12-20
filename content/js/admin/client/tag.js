/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
	$location = "/admin/client/tag";

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

        // Validation
        var tagName = $("#myModal").find("#name").val();
        if (tagName == '') {
            alert('Please enter a tag name');
            $("#myModal").find("#name").parents('.form-group').addClass('has-error');
            return false;
        } else {

            //$("#myModal").find("form").submit();
            // NEW Submit

            $.ajax({
                url: "/admin/client/create-tag",
                type: "POST",
                data: $("#myModal").find("form").serialize(),
                dataType: "json",
                success: function(e) {
                    if (e.success === true) {
                        window.location.reload();
                        //window.location.href = $location;
                    } else {
                        alert('Create/edit tag failed: ' + e.error );
                    }
                }
            })


        }

	});

	$("#delete-tag").click(function() {
		$checked = $.find(".checked");
        var total = $checked.length;
        var tags = "";
        if( total === 0 ) {
            alert("No tags are selected to delete")
        } else {
            var modalInput = $("#delete-modal #delete-tag-names");
            modalInput.html('');
            for ($j in $checked) {
                tags += $($checked[$j]).parent("td").parent("tr").children("td")[0].innerHTML + "<br>";
            }
            modalInput.html(tags);
            $('#delete-modal').modal('show');
        }

	});

    // Delete clients request
    $('.delete-modal-submit').click(function(event){

        $checked = $.find(".checked");

        var total = $checked.length;
        var done = 0;
        var attempts = 0;

        for ($j in $checked) {
             $($checked[$j]).attr("tag-id");
             $.ajax({
                 url: "/admin/client/delete-tag",
                 method: "POST",
                 data: "id=" + $($checked[$j]).attr("tag-id"),
                 success: function(e) {
                     attempts++;
                     if( JSON.parse(e).success == true ) {
                        done++;
                        if(done === total) {
                            alert("Tags deleted")
                            window.location.href = $location;
                        }
                        if(attempts === total && done < total) {
                             alert("Failed to delete some tags");
                             window.location.href = $location;
                        }
                     } else {
                         if(attempts === total) {
                            alert("Failed to delete some tags")
                            window.location.href = $location;
                         }
                     }
                 }
             });

         }

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
            $("#updateExistingTag #client_id").val(parseInt($($checked).attr("client-id")));
            $("#updateExistingTag #parent_id").val(parseInt($($checked).attr("parent-id")));

            $("#updateExistingTag").find(".btn-primary").click(function() {
                //alert( $("#updateExistingTag #parent_id").val() );

                if(window.console) console.log( "tag: " + $($checked).attr("tag-id") + " vs. parent " + $("#updateExistingTag #parent_id").val() );
                if( $($checked).attr("tag-id") === $($checked).attr("parent-id") ) {
                    alert("A tag may not be a child of itself.");
                }

                $.ajax({
                    url: "/admin/client/update-tag",
                    method: "POST",
                    data: "id=" + $($checked).attr("tag-id") +
                    "&name=" + encodeURIComponent($("#updateExistingTag").find("#name").val()) +
                    "&client_id=" + $("#updateExistingTag #client_id").val()+
                    "&parent_id=" + $("#updateExistingTag #parent_id").val(),
                    contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",
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

