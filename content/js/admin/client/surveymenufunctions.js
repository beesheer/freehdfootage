// shared functions for admin pages

/* title drop down configuration */
function hideTitles() {
	$('#title option').each(function(event) {
		$(this).hide();
	});
}
function hidePages() {
	$('#page option').each(function(event) {
		$(this).hide();
	});
}

//Filter:Page on Client change
function clientDropDownEvent_FilterPage() {
	$('#client').change(function(event){
		var cn = $('#client').val();
		if(cn!="") {
			$("#page option:first").text("Select a page");
			filterPageOnClient(cn);
		} else {
			$("#page option:first").text("Choose a client first");
			$("#page").val("");
			hidePages();
		}
	});
	var cn = $('#client').val();
	if(cn!="") initPage($('#client').val());
}
function filterPageOnClient(n) {
	$('#page').val("0");
	initPage(n);
}
//Filter:Title on Client change
function clientDropDownEvent_FilterTitle() {
	$('#client').change(function(event){
		var cn = $('#client').val();
		if(cn!="") {
			$("#title option:first").text("Select a title");
			filterTitleOnClient(cn);
		} else {
			$("#title option:first").text("Choose a client first");
			$("#title").val("");
			hideTitles();
		}
	});
	initTitle($('#client').val());
}
function filterTitleOnClient(n) {
	$('#title').val("0");
	initTitle(n);
}
function initTitle(n) {
	var foundtitles = false;
	$('#title option').each(function(event) {
		var ref = $(this).attr("ref");
		if(n==ref) {
			$(this).show();
			foundtitles = true;
		} else {
			if( $(this).val()!="") $(this).hide();
		}
	})
	if(foundtitles==false) {
		$("#title option:first").text("No titles to attach survey to");
	}
}

//Filter:Page on Title change
function titleDropDownEvent_FilterPage() {
	$('#title').change(function(event){
		var tn = $('#title').val();
		if(tn!="") {
			$("#page option:first").text("Select a page");
			filterPageOnTitle(tn);
		} else {
			$("#page option:first").text("Choose a title first");
			$("#page").val("");
			hidePages();
		}
	});
	initPage($('#title').val());
}
function filterPageOnTitle(n) {
	$('#page').val("0");
	initPage(n);
}
function initPage(n) {
	var foundpages = false;
	$('#page option').each(function(event) {
		var ref = $(this).attr("ref");
		if(n==ref) {
			$(this).show();
			foundpages = true;
		} else {
			if( $(this).val()!="") $(this).hide();
		}
	})
	if(foundpages==false) {
		$("#page option:first").text("No survey template pages created");
	}
}



function getModal(id) {
	var html = '';
	return tml;
}