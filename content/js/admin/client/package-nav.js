$(function () {
	var folderKey = 1;
	function saveData()
	{
		var data = JSON.stringify($('#tree').fancytree('getTree').toDict());
		// Delete the node and refresh the page
		$.ajax({
			url: saveDataUrl,
			data: {id: $('#packageName').attr('rel'), nav: data},
			dataType: 'json',
			type: 'POST',
			success: function (r) {
				console.log(r);
			}
		});
	}

	$("#tree").fancytree({
		extensions: ["dnd", "filter"],
		generateIds: true,
		filter: {
			autoApply: true, // Re-apply last filter if lazy data is loaded
			counter: true, // Show a badge with number of matching child nodes near parent icons
			hideExpandedCounter: true, // Hide counter badge, when parent is expanded
		},
		dnd: {
			autoExpandMS: 400,
			focusOnClick: true,
			preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
			preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
            smartRevert: false,
			dragStart: function (node, data) {
				return true;
			},
			dragEnter: function (node, data) {
				if (!data.otherNode) {
					// Drag a title from title source, we can't allow to drop to a non folder in tree
					if (!node.folder) {
						return false;
					} else {
						return true;
					}
				} else {
					if (node.folder) {
						return true;
					} else {
						return false;
					}
				}
			},
			dragDrop: function (node, data) {
				if (!data.otherNode) {
					// It's a non-tree draggable
					// Make a new leaf node
					var newNode = {
						title: data.draggable.element.text(),
						folder: false,
						data: {titleId: data.draggable.element.attr('rel')}
					};
					node.addChildren(newNode);
					return;
				}
				data.otherNode.moveTo(node, data.hitMode);
			}
		},
		source: nav
	});

	// Set up the folderKey and expand all tree nodes
	$("#tree").fancytree("getTree").visit(function (node) {
		if (node.folder) {
			if (typeof node.data == 'undefined' || typeof node.data.folderKey == 'undefined') {
				node.data.folderKey = folderKey++;
			} else {
				folderKey = folderKey <= node.data.folderKey ? node.data.folderKey + 1 : folderKey;
			}
		}
		node.setExpanded(true);
	});

	function applyFilter(){
		$("#tree").fancytree("getTree").filterNodes(function (node) {
			if (node.data.isHidden === true) {
				if(typeof(node.extraClasses)!=="undefined"){
					delete node.extraClasses;
				}
				$(node.span).find('.fancytree-icon').removeClass("fancytree-icon").addClass("ion-eye-disabled");
			}
			return node;
		});
		$("#tree").removeClass("fancytree-ext-filter-dimm");
	}

	applyFilter();


	$(".draggable").draggable({
		revert: true, //"invalid",
		cursorAt: {top: -5, left: -5},
		connectToFancytree: true
	});

	// Create or edit client request
	$('.form-modal-submit').click(function (event) {
		var folderName = $('#name').val();

		// Validation
		if (folderName == '') {
			alert('Please enter name');
			$('#name').parents('.form-group').addClass('has-error');
			return false;
		}

		if ($('#formModalLabel').text() == 'Edit Folder') {
			// Edit a folder
			var node = $("#tree").fancytree("getActiveNode");
			node.setTitle(folderName);
			node.data.isHidden = $('#is_hidden').is(':checked') ? true : false;
			node.render();
			applyFilter();
		} else {
			// Add a new node to the current selected node (or as a root node if no actived node is selected)
			var parentNode = $("#tree").fancytree("getActiveNode");
			if (!parentNode) {
				parentNode = $("#tree").fancytree("getRootNode");
			}
			var newNode = {
				title: folderName,
				folder: true,
			};

			parentNode.addChildren(newNode);

			// Expand the node
			parentNode.setExpanded(true);
		}

		// Deactive so that we can add a root node
		$("#tree").fancytree("getTree").activateKey(false);

		$('#form-modal').modal('hide');
	});

	// Control buttons
	// Add new
	$('#node-add').bind('click', function (e) {
		$('#formModalLabel').text('Add Folder');
		$('#name').val('');
		$(".formElementBlock.checkbox").css('display','none');
		$('#form-modal').modal('show');
	});
	// Add new
	$('#node-add-root').bind('click', function (e) {
		// Deactive so that we can add a root node
		$("#tree").fancytree("getTree").activateKey(false);
		$('#formModalLabel').text('Add Root Folder');
		$('#name').val('');
		$(".formElementBlock.checkbox").css('display','none');
		$('#form-modal').modal('show');
	});
	// Edit
	$('#node-edit').bind('click', function (e) {
		var node = $("#tree").fancytree("getActiveNode");
		if (!node || !node.folder) {
			alert('Please select a Folder node first.');
			return false;
		}
		$('#formModalLabel').text('Edit Folder');
		$(".formElementBlock.checkbox").css('display','block');
		$('#name').val(node.title);
		$('#is_hidden').prop('checked', node.data.isHidden ? true : false);
		$('#form-modal').modal('show');
	});

	// Remove
	$('#node-remove').bind('click', function (e) {
		var node = $("#tree").fancytree("getActiveNode");
		if (!node) {
			alert('Please select a node first.');
			return false;
		}
		node.remove();
	});

	$('#save').bind('click', function (e) {
		saveData();
		alert("Saved navigation successfully!");
	});
});