$(function () {
    // Remove the default unclick
    $(".checkbox").unbind();

	// Form submit.
	$('#title-save-submit').click(function (event) {
		// First save title pages
		// Submit
		//alert($('#title-pages').sortable('serialize'));
		var title_pages_serialize_str="";
		$('#title-pages').find("li").each(function( ele ) {
			if(title_pages_serialize_str=="")
			{
				title_pages_serialize_str="";
			}else
			{
				title_pages_serialize_str= title_pages_serialize_str + "&";
			}
		  	title_pages_serialize_str = title_pages_serialize_str + "page[]="+$(this).attr("rel");		  	
		});
		controlSaveIndicator(true);
		$.ajax({
			url: "/client/ajax/save-title-pages",
			type: "POST",
			data: title_pages_serialize_str + '&title=' + $('#titleName').attr('rel'),
			dataType: "json"
		})
        .done(function(data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						// Ok, good to submit the form as well.
						$('#client-title-form').trigger('submit');
					} else {
						alert('Save title page failed: ' + data.meta.error);
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert("Save title page request failed: " + textStatus);
				});
	});

	// Delete the page
	$('#title-delete').click(function (event) {
		var $button = $(event.target);
		// Change label and set id to empty
		$('#delete-modal').modal('show');
	});

	// Delete user request
	$('.delete-modal-submit').click(function (event) {
		var titleId = $('#titleName').attr('rel');
		// Submit
		$.ajax({
			url: "/client/ajax/delete-title",
			type: "POST",
			data: {id: titleId},
			dataType: "json"
		})
				.done(function (data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						window.location.reload();
					} else {
						alert('Delete title failed: ' + data.meta.error);
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert("Delete title request failed: " + textStatus);
				});
	});

	var clientPages = $("#client-pages").children();

	var counter = 1;
	var maxLength = 3;
	var currentPage = 1;
	var totalPages = Math.ceil(clientPages.length / 5);
	var aPage = 1;

	function renderPages(aPage) {
		var result = clientPages.slice((aPage - 1) * 5, 5 + 5 * (aPage - 1));
		$("#client-pages").html("");
		for (var p = 0; p < result.length; p++) {
			$("#client-pages").append(result[p]);
		}
	}

    /*var resortedFlag = false;
    $("#client-pages, #title-pages").on("sortreceive", function (event, ui) {
        resortedFlag = true;
    });
    // save each time a column receives a page
    $("#client-pages, #title-pages").on("sortstop", function (event, ui) {
        //sortreceive is premature for saving the list, need to wait for stop event
        if (event.handled !== true && resortedFlag === true ) {
            resortedFlag = false;
            controlSaveIndicator(true);
            $('#title-save-submit').trigger("click");
        }
    });*/

    // Form modal preparation
	var clientId = $('#client').val();

	$("#fileUpload").bind('fileuploadstart', function (e) {
		var message = 'uploading new title image... ';
		$("#loader-info").html(message);
		$("#overlay, #loader").show();
	});

	// New file upload
	$('#fileUpload').fileupload({
		formData: {client: clientId}, //, allowType: 'png,jpg'
		dataType: 'json',
		url: '/client/ajax-media-asset/upload',
		done: function (e, data) {
			if (data.result.meta.error != '') {
				alert('Upload failed: ' + data.result.meta.error);
			} else {

				// Create the media asset directly
				$.ajax({
					url: "/client/ajax-media-asset/create",
					type: "POST",
					data: {client: clientId, name: data.result.fileName, filepath: data.result.filePath},
					dataType: "json"
				})
						.done(function (data) {
							$('#media_asset_id').val(data.id);
							$('#filepath-image-preview').html(data.preview);
							controlSaveIndicator(false);
						})
						.fail(function (jqXHR, textStatus) {
							alert("Create media asset request failed: " + textStatus);
							controlSaveIndicator(false);
						});
			}
		}
	});

	// Clone the title
	$('#title-clone').click(function (event) {
		var titleId = $('#titleName').attr('rel');
		// Submit
		$.ajax({
			url: "/client/ajax/clone-title",
			type: "POST",
			data: {id: titleId},
			dataType: "json"
		})
				.done(function (data) {
					if (data.meta.code == 200 && data.meta.error == '') {
						var newTitleId = data.title.id;
						window.location.href = '/client/title/detail/id/' + newTitleId;
					} else {
						alert('Clone title failed: ' + data.meta.error);
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert("Clone title request failed: " + textStatus);
				});
	});

	//list js search
	var options = {
		valueNames: ['pagename'],
		page: 20,
		plugins: [
			ListPagination({})
		]
	};
	var pageList = new List('page_list', options);
	//activate the listener initially
	activateListener();


	$("#page_list").on("click",function(){
		// have to activate listener on each click;
		activateListener();
	});

	//sort buttons
	$('#pages-alpha-sort').find('button').each(function () {
		$(this).click(function (event) {
			$(this).addClass('hide');
			$(this).siblings().removeClass('hide');
		})
	})

	//selection deseleciton
	$("#btn-select-all").on('click', function (event) {
		event.preventDefault();
		$(".list.list-group.client-pages-list li").each(function () {
			$(this).addClass('selected');
		});
		$('#pages-out .checkbox').addClass('checked');
	});

	//$(".checkbox.portal-page-list").css({ pointerEvents:"none" })

	$("#btn-deselect").on('click', function (event) {
		event.preventDefault();
		$('.selected').removeClass('selected');
		$('#pages-out .checkbox').removeClass('checked');
	});

	//drag and drop ( http://jsfiddle.net/tilwinjoy/shLQE/ )
	function activateListener()
    {
	    $('.list-group li, .checkbox.portal-page-list').on('click', function (e) {
		    e.stopPropagation();
		    switch ($(e.target).attr('class')) {
			    case 'page-list-item list-group-item selected':
				    $(this).toggleClass("selected");
				    $(this).find(".checkbox").toggleClass('checked');
				    break;
			    case 'page-list-item list-group-item':
				    $(this).toggleClass('selected');
				    $(this).find(".checkbox").toggleClass('checked');
				    break;
			    case 'checkbox portal-page-list':
				    $(this).parent().toggleClass('selected');
				    break;
			    case 'checkbox portal-page-list checked':
				    $(this).parent().toggleClass('selected');
				    break;
		    }


	    });

        // checkbox
        $(".checkbox").click(function(event) {
            event.stopPropagation();
            $(this).toggleClass("checked");
            $(this).parent().parent().toggleClass('selected');
        });
	}

    // Changed to use: http://jsfiddle.net/hQnWG/614/
	$("ul.list-group").sortable({
		connectWith: 'ul.list-group',
		opacity: 0.6,
		revert: false,
        delay: 150,
		/*helper: function (e, item) {
			console.log('parent-helper');
			console.log(item);
			if (!item.hasClass('selected'))
				item.addClass('selected');
			var elements = $('.selected').not('.ui-sortable-placeholder').clone();
			var helper = $('<ul/>');
			item.siblings('.selected').addClass('hidden');
			return helper.append(elements);
		},
		start: function (e, ui) {
			var elements = ui.item.siblings('.selected.hidden').not('.ui-sortable-placeholder');
			ui.item.data('items', elements);
		},
		receive: function (e, ui) {
			ui.item.before(ui.item.data('items'));
		},
		stop: function (e, ui) {
			ui.item.siblings('.selected').removeClass('hidden');
			$('.selected').removeClass('selected');
			$('.checkbox').removeClass('checked');
		}*/
        helper: function (e, item) {
            //Basically, if you grab an unhighlighted item to drag, it will deselect (unhighlight) everything else
            if (!item.hasClass('selected')) {
                item.addClass('selected').siblings().removeClass('selected');
            }

            //////////////////////////////////////////////////////////////////////
            //HERE'S HOW TO PASS THE SELECTED ITEMS TO THE `stop()` FUNCTION:

            //Clone the selected items into an array
            var elements = item.parent().children('.selected').clone();

            //Add a property to `item` called 'multidrag` that contains the
            //  selected items, then remove the selected items from the source list
            item.data('multidrag', elements).siblings('.selected').remove();

            //Now the selected items exist in memory, attached to the `item`,
            //  so we can access them later when we get to the `stop()` callback

            //Create the helper
            var helper = $('<li/>');
            return helper.append(elements);
        },
        stop: function (e, ui) {
            //Now we access those items that we stored in `item`s data!
            var elements = ui.item.data('multidrag');

            //`elements` now contains the originally selected items from the source list (the dragged items)!!

            //Finally I insert the selected items after the `item`, then remove the `item`, since
            //  item is a duplicate of one of the selected items.
            ui.item.after(elements).remove();
            
            //$('.selected').removeClass('selected');
            //$('.checkbox').removeClass('checked');

            if (state !== $('#title-pages').sortable('serialize')) {
                //controlSaveIndicator(true);
                $('#title-save-submit').trigger("click");
            } else {
                activateListener();
            }
        }
	});

	$("#client-pages, #title-pages").disableSelection();
	$("#client-pages, #title-pages").css('minHeight', $("#client-pages").height() + "px");

	//saving message
	function controlSaveIndicator(bool) {
		switch (bool) {
			case false:
				$("#loader-info").html('');
				$("#overlay, #loader").hide();
				break;
			case true:
				$("#loader-info").html('saving title...');
				$("#overlay, #loader").show();
				break;
		}
	}

	/**
	 * Filter out default option.
	 *
	 * @see STRAT-661
	 * @author Bin Xu
	 */
	$('#type option[value=default]').remove();
    var state = $('#title-pages').sortable('serialize');
});
