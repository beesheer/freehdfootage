function CALENDAR() {
    this.label = "from";
	this.year = 2014;
	this.month = 0;
	this.date = 1;
	this.day = 0;
	this.monthName = {
		0	: "Janurary",
		1	: "Feburary",
		2	: "March",
		3	: "April",
		4	: "May",
		5	: "June",
		6	: "July",
		7	: "August",
		8	: "September",
		9	: "October",
		10	: "Novermber",
		11	: "December"
	};
	this.dayName = {
		0	: "Sunday",
		1	: "Monday",
		2	: "Tuesday",
		3	: "Wednesday",
		4	: "Thursday",
		5	: "Friday",
		6	: "Saturday"
	}
}


CALENDAR.prototype.reset = function() {
    var d = new Date();
	this.year = d.getFullYear();
	this.month = d.getMonth();
	this.date = d.getDate();
	this.day = d.getDay();
}


CALENDAR.prototype.now = function() {
	var datetime = new Date();
	this.year = datetime.getFullYear();
	this.month = datetime.getMonth();
	this.date = datetime.getDate();
	this.day = datetime.getDay();
}


CALENDAR.prototype.set = function(datetime) {
	this.year = datetime.getFullYear();
	this.month = datetime.getMonth();
	this.date = datetime.getDate();
	this.day = datetime.getDay();
}


CALENDAR.prototype.show = function() {
    
    var datetime;
    if( typeof chart != "undefined" ){
    	if (this.label == "from") 
	        datetime = chart.range["from"];
	    else
	        datetime = chart.range["to"];

	    this.set(datetime);
    }
    
        
    
	
	$("#calendar-month").html(this.monthName[this.month] + " " + this.year);
	this.getDays();
}

CALENDAR.prototype.getDays = function() {
    $("#calendar-day").html("");
    var firstWeekDay = this.day;
    for (var i=this.date; i>0; i--) {
        firstWeekDay--;
        if (firstWeekDay < 0)
            firstWeekDay = 6;
    }
    var left = 4 + (firstWeekDay + 1) * 36;
    if (firstWeekDay == 6)
        left = 4;
    $("#calendar-day").append(
        "<div class=\"calendar-day " + (this.date == 1 ? "selected" : "") + "\" style=\"margin-left:" + left + "px;\">1</div>"
    );
    for (var i=2; i<=this.monthDays(); i++) {
        $("#calendar-day").append(
            "<div class=\"calendar-day " + (i == this.date ? "selected" : "") + "\">" + i + "</div>"
        );
    }
}

CALENDAR.prototype.monthDays = function() {
	
	var end = 31; 
	
	if (this.month == 3 || this.month == 5 || this.month == 8 || this.month == 10) {
		end = 30;
	} else if (this.month == 1) {
		if (this.year % 400 == 0)
			end = 29;
		else if (this.year % 100 == 0)
			end = 28;
		else if (this.year % 4 == 0)
			end = 29;
		else
			end = 28;
	}
	
	return end;
}


CALENDAR.prototype.changeMonth = function(m) {
	
	this.month += m;
	if (this.month > 11) {
		this.month = 0;
		this.year++;
	} else if (this.month < 0) {
		this.month = 11;
		this.year--;
	}
	
	if (this.date > this.monthDays())
	    this.date = 1;
	this.day = this.getWeekDay();
	$("#calendar-month").html(this.monthName[this.month] + " " + this.year);
	$("#calendar-day").html("");
	this.getDays();
}


CALENDAR.prototype.changeDate = function(date) {
    
    this.date = parseInt(date);

    if( typeof chart != "undefined" ){
    	if (this.label == "from")  {
		    chart.range["from"] = this.object();
	        if (this.object().getTime() > chart.range["to"].getTime())
	            chart.range["to"] = this.object();
		} else {
		    chart.range["to"] = this.object();
		    if (this.object().getTime() < chart.range["from"].getTime())
	            chart.range["from"] = this.object();
		}
    }
    
    
}


CALENDAR.prototype.getWeekDay = function() {
	var date = new Date();
	date.setFullYear(this.year);
	date.setMonth(this.month);
	date.setDate(this.date);
	return date.getDay();
}


CALENDAR.prototype.object = function() {
    var newDate =  new Date(this.year, this.month, this.date, 0, 0, 0, 0);
    return newDate;
}


var calendar = new CALENDAR();
calendar.reset();