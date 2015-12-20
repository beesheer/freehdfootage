function CALENDAR() {
	this.Y = 2014;
	this.M = 0;
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
	this.year = 2014;
	this.month = 0;
	this.date = 1;
	this.day = 0;
}


CALENDAR.prototype.now = function() {
	var datetime = new Date();
	this.Y = datetime.getFullYear();
	this.M = datetime.getMonth();
	this.year = datetime.getFullYear();
	this.month = datetime.getMonth();
	this.date = datetime.getDate();
	this.day = datetime.getDay();
	$("#calendar-month").html(this.monthName[this.month]);
	$("#calendar-date").html(this.date);
	$("#calendar-day").html(this.dayName[this.day]);
}


CALENDAR.prototype.set = function(datetime) {
	this.Y = datetime.getFullYear();
	this.M = datetime.getMonth();
	this.year = datetime.getFullYear();
	this.month = datetime.getMonth();
	this.date = datetime.getDate();
	if (this.year == this.Y)
	    this.changeMonthTo(12 + this.month);
	else if (this.year > this.Y)
	    this.changeMonthTo(24 + this.month);
	else if (this.year < this.Y)
	    this.changeMonthTo(this.month);
	this.changeDateTo(this.date);
}


CALENDAR.prototype.range = function() {
    
    $(".calendar.range").removeClass("active");
    $("#calendar-from").addClass("active");
    
    if (chart.range["from"] != null) {
        this.set(chart.range["from"]);
	    $("#calendar-from").html(this.monthName[chart.range["from"].getMonth()] + " " + chart.range["from"].getDate());
	} else {
	    $("#calendar-from").html("From");
	}
	
	if (chart.range["to"] != null) {
	    $("#calendar-to").html(this.monthName[chart.range["to"].getMonth()] + " " + chart.range["to"].getDate());
	} else {
	    $("#calendar-to").html("To");
	}
	
	$("#calendar-month").html(this.monthName[chart.range["from"].getMonth()]);
	$("#calendar-date").html(chart.range["from"].getDate());
	$("#calendar-day").html(this.dayName[chart.range["from"].getDay()]);
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
	
	// control it to be in the past year, this year, and next year
	if (this.year == this.Y + 1 && this.month + m > 11)
		return;
	if (this.year == this.Y - 1 && this.month + m < 0)
		return;
	
	this.month += m;
	if (this.month > 11) {
		this.month = 0;
		this.year++;
	} else if (this.month < 0) {
		this.month = 11;
		this.year--;
	}
	
	if (this.date > this.monthDays()) {
		this.date = this.monthDays();
		$("#calendar-date").html(this.date);
	}
	
	this.getWeek();
	
	if (this.year == this.Y) {
		$("#calendar-month").html(this.monthName[this.month]);
		if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date);
	} else {
		$("#calendar-month").html(this.monthName[this.month] + ", " + this.year);
		if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
	}
	
	if ($("#calendar-from").hasClass("active"))
	    chart.range["from"] = this.object();
	else
	    chart.range["to"] = this.object();
}


CALENDAR.prototype.changeMonthTo = function(val) {
	
	if (val < 0 || val > 35)
		return;
	
	// val [00, 11] - prev year months
	// val [12, 23] - this year months
	// val [24, 35] - next year months
	
	if (val <= 11) {
		if (this.year >= this.Y)
			this.year = this.Y - 1;
	} else if (val < 24) {
		val -= 12;
		if (this.year != this.Y)
			this.year = this.Y;
	} else if (val >= 24) {
		val -= 24;
		if (this.year <= this.Y)
			this.year = this.Y + 1;
	}
	
	this.month = val;
	
	if (this.year == this.Y) {
		$("#calendar-month").html(this.monthName[this.month]);
		if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date);
	} else {
		$("#calendar-month").html(this.monthName[this.month] + ", " + this.year);
		if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
	}
	
	if (this.date > this.monthDays()) {
		this.date = this.monthDays();
		$("#calendar-date").html(this.date);
	}
	
	this.getWeek();
	
	if ($("#calendar-from").hasClass("active"))
	    chart.range["from"] = this.object();
	else
	    chart.range["to"] = this.object();
}


CALENDAR.prototype.changeDate = function(d) {
	
	this.date += d;
	
	if (this.date < 1) {
		this.date = this.monthDays();
	} else if (this.date > this.monthDays()) {
		this.date = 1;
	}
	
	$("#calendar-date").html(this.date);
	
	this.getWeek();
	
	if (this.year == this.Y) {
	    if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date);
	} else {
	    if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
	}
	
	if ($("#calendar-from").hasClass("active"))
	    chart.range["from"] = this.object();
	else
	    chart.range["to"] = this.object();
}


CALENDAR.prototype.changeDateTo = function(d) {
	
	this.date = d;
	
	if (this.date < 1) {
		this.date = 1;
	} else if (this.date > this.monthDays()) {
		this.date = this.monthDays();
	}
	
	$("#calendar-date").html(this.date);
	
	this.getWeek();
	
	if (this.year == this.Y) {
	    if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date);
	} else {
	    if ($("#calendar-from").hasClass("active"))
		    $("#calendar-from").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
		else
		    $("#calendar-to").html(this.monthName[this.month] + " " + this.date + ", " + this.year);
	}
	
	if ($("#calendar-from").hasClass("active"))
	    chart.range["from"] = this.object();
	else
	    chart.range["to"] = this.object();
}


CALENDAR.prototype.getWeek = function() {
	var date = new Date();
	date.setFullYear(this.year);
	date.setMonth(this.month);
	date.setDate(this.date);
	this.day = date.getDay();
	$("#calendar-day").html(this.dayName[this.day]);
}


CALENDAR.prototype.object = function() {
    var newDate =  new Date(this.year, this.month, this.date, 0, 0, 0, 0);
    return newDate;
}


var calendar = new CALENDAR();