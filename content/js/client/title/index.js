$(function(){

    $('#filter-meta').change(function () {
        var selection = this.value;
        if (selection) {
            titleList.filter(function(item) {
                return (item.values().meta == selection);
            });
        } else {
            titleList.filter();
        }
    });

    // Click through the document items
    $('tr').bind('click', function(event){
        event.preventDefault();
        var url = '/client/title/detail/id/' + $(this).attr('rel');
        window.location.href = url;
    });

	$('tr.off').off('click');

    $('.title-package-filter').click(function(event) {
        //overrides the href in the drop down
        event.preventDefault();
        var packageId = $(event.target).attr('rel');
        cookie.create("lifelearn-stratus-package", packageId );
        cookie.create("lifelearn-stratus-package-title", $(event.target).text());
        var link = '/client/title';
        window.location = link;
    });

	var options = {
		valueNames: ['name','type','created_date','modified_date'],
		page: 20,
		plugins: [
			ListPagination({})
		]
	};
	var pageList = new List('title_list', options);

});