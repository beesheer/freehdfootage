// Bootstrap Paginator: Lifelearn TF 2013
// 
/* 
	usage:
	var Paginator = new Paginator();
	Paginator.getPaginator();
*/
function Paginator(parameters) {
	this.currentPage = 1;
	this.pages = parameters.numberPages;
	this.selection;
	this.clickPaginatorCallback = parameters.onclickCallback;
	this.instance = parameters.paginatorName;
	this.rangeLimit = (parameters.range==undefined) ? 1 : parameters.range;
}

Paginator.prototype.getPaginator = function() {
	var html = '';
	html += '<ul class="pagination" style="margin:0 0 10px 0;">';
	html += '<li class="disabled" id="paginator_prev"><a href="#" onclick="'+this.instance+'.handleClick(\'prev\')">&laquo;</a></li>';
	html += '<li class="active" id="paginator_1"><a href="#" onclick="'+this.instance+'.handleClick(1)">1</a></li>';
	for(var f=2; f<=this.pages; f++) {
		html += ' <li id="paginator_'+f+'"><a href="#" onclick="'+this.instance+'.handleClick('+f+')">'+f+'</a></li>';
	}
	html += '<li id="paginator_next"><a href="#" onclick="'+this.instance+'.handleClick(\'next\')">&raquo;</a></li></ul>';
	html += '';
	document.write( html );
	
	this.selection = document.getElementById("paginator_1");
}

Paginator.prototype.checkPaginators = function(){
}
	
Paginator.prototype.handleClick = function(param) {
	var callback = true;
	switch(param) {
		case "prev":
			if(this.currentPage==1) {
				callback = false;
			} else {
				this.currentPage--;
			}
		break;
		case "next":
			if(this.currentPage ==  this.pages) {
				callback = false;
			} else {
				this.currentPage++;
			}
		break;
		default:
			this.currentPage = param;
		break;
	}
	
	
	if(callback==true) {
		this.reconfigure();
		eval(this.clickPaginatorCallback)(this.currentPage);
	}
}
Paginator.prototype.reconfigure = function () {
	$(this.selection).removeClass('active');
	$("#paginator_prev").removeClass('disabled');
	$("#paginator_next").removeClass('disabled');
	if(this.currentPage==1) {
		//beginning
		$("#paginator_prev").addClass('disabled');
	} else if(this.currentPage ==  this.pages) {
		//end
		$("#paginator_next").addClass('disabled');
	} 
	this.selection = $("#paginator_"+this.currentPage);
	$(this.selection).addClass('active');
}
	
Paginator.prototype.isOdd = function (num) { return num % 2;}