$(function(){
    // Get user email to access to API
    var apiUser = {};
    $.ajax({
        async: false,
        url: '/ajax/user-info',
        dataType: 'json',
        success: function(r) {
            apiUser = r.user;
        }
    });

    // Get API session
    $.ajax({
        async: false,
        url: '/api/login',
        data: {email: apiUser.email},
        dataType: 'json',
        success: function(r) {
            apiUser.clientId = r.clientId;
            apiUser.id = r.user;
            apiUser.session = r.session;
        }
    });

    // Get the title menu tree data;
    $.ajax({
        url: '/api/title/menu',
        data: {user: apiUser.id, session: apiUser.session, title: $('#titleName').attr('rel')},
        dataType: 'json',
        success: function(r) {
            console.log('title menu response', r, r.menu);
            $("#tree").fancytree({
                source: r.menu,
                activate: function(event, data) {
                    var node = data.node;
                    // console.log(node.data);
                    // Show the selected node page property

                    if (node.data.pageId !== null) {
                        // Details update with new page id
                        var pageItemInList = $('.title-pages-list .page-list-item[rel=' + node.data.pageId + ']');
                        $('#currentNodeDetails').html(pageItemInList.html());
                    } else {
                        $('#currentNodeDetails').html('No page is assigned to this node.');
                    }
                }
            });

            // Expand all by default
            $("#tree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(true);
            });
        }
    });

    function updateTreeActivateEvent()
    {
        $("#tree").fancytree('option', 'activate', function(event, data) {
            var node = data.node;
            console.log('activate event node data', node.data);
            // Show the selected node page property

            if (node.data.pageId !== null && node.data.pageId !== '0') {
                // Details update with new page id
                var pageItemInList = $('.title-pages-list .page-list-item[rel=' + node.data.pageId + ']');
                $('#currentNodeDetails').html(pageItemInList.html());
            } else {
                $('#currentNodeDetails').html('No page is assigned to this node.');
            }
        });
    }

    // Create or edit client request
    $('.form-modal-submit').click(function(event){
        var nodeTitle = $('#title').val();
        var pageId = $('#page_id').val();

        // Validation
        if (nodeTitle == '') {
            alert('Please enter node name');
            $('#title').parents('.form-group').addClass('has-error');
            return false;
        }
        /*if (pageId == '') {
            alert('Please select a page');
            $('#page_id').parents('.form-group').addClass('has-error');
            return false;
        }*/

        var nodeId = parseInt($('#node_id').val());
        if (nodeId > 0) {
            // Edit
            $.ajax({
                async: false,
                url: '/api/title/menu-update',
                data: {user: apiUser.id, session: apiUser.session, title: $('#titleName').attr('rel'), nodeId: nodeId, nodeTitle: nodeTitle, pageId: pageId},
                dataType: 'json',
                success: function(r) {
                    node = $("#tree").fancytree("getActiveNode");
                    node.title = nodeTitle;
                    node.data.pageId = pageId;
                    node.render(false);
                    updateTreeActivateEvent();

                    // Show current page detail
                    if (node.data.pageId !== null && node.data.pageId !== '0') {
                        // Details update with new page id
                        var pageItemInList = $('.title-pages-list .page-list-item[rel=' + node.data.pageId + ']');
                        $('#currentNodeDetails').html(pageItemInList.html());
                    } else {
                        $('#currentNodeDetails').html('No page is assigned to this node.');
                    }
                }
            });
        } else {
            // Add a new node to the current selected node (or as a root node if no actived node is selected)
            var parentNode = $("#tree").fancytree("getActiveNode");
            var parentNodeId = false;
            if (!parentNode) {
                parentNode = $("#tree").fancytree("getRootNode");
            } else {
                parentNodeId = parentNode.data.id;
            }
            var newNode = {
                title: nodeTitle,
                data: {
                    pageId: pageId
                }
            };

            // Create on backend
            $.ajax({
                async: false,
                url: '/api/title/menu-add',
                data: {user: apiUser.id, session: apiUser.session, title: $('#titleName').attr('rel'), parentNodeId: parentNodeId, nodeTitle: nodeTitle, pageId: pageId},
                dataType: 'json',
                success: function(r) {
                    newNode.data.id = r.node.id;
                }
            });

            parentNode.addChildren(newNode);

            // Expand the node
            parentNode.setExpanded(true);

            // Deactive so that we can add a root node
            $("#tree").fancytree("getTree").activateKey(false);

            updateTreeActivateEvent();
        }
        $('#form-modal').modal('hide');
    });

    // Control buttons
    // Add new
    $('#node-add').bind('click', function(e){
        $('#formModalLabel').text('Add New Page To Menu');
        $('#title').val('');
        $('#page_id').val('');
        $('#node_id').val(0);
        $('#form-modal').modal('show');
    });
    // Add new
    $('#node-add-root').bind('click', function(e){
        // Deactive so that we can add a root node
        $("#tree").fancytree("getTree").activateKey(false);
        $('#formModalLabel').text('Add New Page To Menu');
        $('#title').val('');
        $('#page_id').val('');
        $('#node_id').val(0);
        $('#form-modal').modal('show');
    });
    // Edit
    $('#node-edit').bind('click', function(e){
        var node = $("#tree").fancytree("getActiveNode");
        if (!node) {
            alert('Please select a node first.');
            return false;
        }
        $('#formModalLabel').text('Edit Menu Item');
        $('#title').val(node.title);
        $('#page_id').val(node.data.pageId);
        $('#node_id').val(node.data.id);
        $('#form-modal').modal('show');
    });

    // Remove
    $('#node-remove').bind('click', function(e){
        var node = $("#tree").fancytree("getActiveNode");
        if (!node) {
            alert('Please select a node first.');
            return false;
        }
        // Check whether the node has children
        if (node.hasChildren()) {
            alert('Please remove all children first.');
            return false;
        }

        // Delete the node and refresh the page
        $.ajax({
            url: '/api/title/menu-remove',
            data: {user: apiUser.id, session: apiUser.session, nodeId: node.data.id},
            dataType: 'json',
            success: function(r) {
                node.remove();
                $('#currentNodeDetails').html('');
            }
        });
    });
});