$(function () {

	// Click through the document items
	$('tr').bind('click', function (event) {
		event.preventDefault();
		var url = '/client/page/detail/id/' + $(this).attr('rel');
		window.location.href = url;
	});

	//list js search
	$(".table.table-striped.table-bordered tbody").addClass("list");

	$("tr.off").off("click");


	var options = {
		valueNames: ['pagename','created_date','modified_date','status'],
		page: 20,
		plugins: [
			ListPagination({})
		]
	};
	var pageList = new List('page_list', options);

});