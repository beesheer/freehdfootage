function CLOCK() {
	this.none = true;
	this.hour = 0;
	this.minute = 0;
}


CLOCK.prototype.reset = function() {
	this.hour = 0;
	this.minute = 0;
	this.none = true;
}


CLOCK.prototype.time = function() {
	return new Date().getTime();
}


CLOCK.prototype.clear = function() {
	this.none = false;
	this.hour = 0;
	this.minute = 0;
	$("#clock-h").html(this.format(this.hour));
	$("#clock-m").html(this.format(this.minute));
}


CLOCK.prototype.now = function() {
	var date = new Date();
	this.hour = date.getHours();
	this.minute = date.getMinutes();
	$("#clock-h").html(this.format(this.hour));
	$("#clock-m").html(this.format(this.minute));
	this.none = false;
}


CLOCK.prototype.set = function(datetime) {
	this.hour = datetime.getHours();
	this.minute = datetime.getMinutes();
	this.changeHourTo(this.hour);
	this.changeMinuteTo(this.minute);
}


CLOCK.prototype.format = function(time) {
	if (time.toString().length < 2) {
		time = "0" + time;
	}
	return time;
}


CLOCK.prototype.changeHour = function(h) {
	this.hour += h;
	if (this.hour < 0) {
		this.hour = 23;
	} else if (this.hour > 23) {
		this.hour = 0;
	}
	$("#clock-h").html(this.format(this.hour));
}


CLOCK.prototype.changeHourTo = function(h) {
	if (isNaN(h))
		return;
	this.hour = h;
	if (this.hour < 0) {
		this.hour = 0;
	} else if (this.hour > 23) {
		this.hour = 23;
	}
	$("#clock-h").html(this.format(this.hour));
}


CLOCK.prototype.changeMinute = function(m) {
	this.minute += m;
	if (this.minute < 0) {
		this.minute = 59;
	} else if (this.minute > 59) {
		this.minute = 0;
	}
	$("#clock-m").html(this.format(this.minute));
}


CLOCK.prototype.changeMinuteTo = function(m) {
	if (isNaN(m))
		return;
	this.minute = m;
	if (this.minute < 0) {
		this.minute = 0;
	} else if (this.minute > 59) {
		this.minute = 59;
	}
	$("#clock-m").html(this.format(this.minute));
}

var clock = new CLOCK();