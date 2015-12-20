CONTACT = function() {
}

/* 
var sample = [
	{
		"firstname"	: "Adam",
		"surname"	: "Little",
		"email"		: "alittle@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Amie",
		"surname"	: "Prsa",
		"email"		: "aprsa@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Angelica",
		"surname"	: "Ortiz",
		"email"		: "aortiz@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Angus",
		"surname"	: "Mellor",
		"email"		: "amellor@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Bob",
		"surname"	: "Beatty",
		"email"		: "bbeatty@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Bonnie",
		"surname"	: "Tang",
		"email"		: "btang@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: "Project Manager"
	},
	{
		"firstname"	: "Caitlin",
		"surname"	: "LaFlamme",
		"email"		: "claflamme@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Corrie",
		"surname"	: "Swaris",
		"email"		: "cswaris@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Cyndy",
		"surname"	: "Maltby",
		"email"		: "cmaltby@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Dale",
		"surname"	: "Beech",
		"email"		: "dbeech@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "David",
		"surname"	: "Ferguson",
		"email"		: "dferguson@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "David",
		"surname"	: "Sum",
		"email"		: "dsum@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Desmond",
		"surname"	: "Ballance",
		"email"		: "dbllance@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Diana",
		"surname"	: "Beliski",
		"email"		: "dbeliski@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Dina",
		"surname"	: "Lemieux",
		"email"		: "dlemieux@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Heather",
		"surname"	: "Fox",
		"email"		: "hfox@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Jackie",
		"surname"	: "Harris",
		"email"		: "jharris@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "James",
		"surname"	: "Carroll",
		"email"		: "jcarroll@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: ""
	},
	{
		"firstname"	: "Jamie",
		"surname"	: "Klapwyk",
		"email"		: "jklapwyk@lifelearn.com",
		"company"	: "LifeLearn",
		"title"		: "Developer"
	}
];
*/
var service = LLAPP_SERVICE;
var contacts = [];
var action = "add";
var contactID = 0;
var sortBy = "firstname";

/*
function sampleContacts() {
	
	for (var i in sample) {
		sample[i]["added"] = false;
		for (var j in contacts) {
			if (sample[i]["email"] == contacts[j]["email"]) {		
				sample[i]["added"] = true;
			}
		}
	}
	
	var total = 0;
	
	for (var i in sample) {
		if (sample[i]["added"] == false)
			total++;
	}
	
	if (total == 0) {
	
		sortContacts();
		showContacts();
		
	} else {
		// add sample contacts
		for (var i in sample) {
			if (sample[i]["added"] == false) {
				service.addContact({
					"firstname"	: sample[i]["firstname"],
					"surname"	: sample[i]["surname"],
					"email"		: sample[i]["email"],
					"company"	: sample[i]["company"],
					"title"		: sample[i]["title"]
				}, function(data) {
					total--;
					if (total == 0) {
						service.getContacts(function(response) {
							contacts = response.data.contacts;
							sortContacts();
							showContacts();
						});
					}
				});
			}
		}
	}	
}
*/

function sortContacts() {
	if (sortBy == "firstname") {
		contacts.sort(function(a,b) {
			var s1 = a.firstname;
			if (s1 == null || s1.length == 0)
				s1 = a.email;
			var s2 = b.firstname;
			if (s2 == null || s2.length == 0)
				s2 = b.email;
			return s1.localeCompare(s2);
		});
	} else if (sortBy == "surname") {
		contacts.sort(function(a,b) {
			var s1 = a.surname;
			if (s1 == null || s1.length == 0)
				s1 = a.email;
			var s2 = b.surname;
			if (s2 == null || s2.length == 0)
				s2 = b.email;
			return s1.localeCompare(s2);
		});
	} else if (sortBy == "company") {
		contacts.sort(function(a,b) {
			var s1 = a.company;
			if (s1 == null || s1.length == 0)
				s1 = " Unknown";
			var s2 = b.company;
			if (s2 == null || s2.length == 0)
				s2 = " Unknown";
			var order = s1.localeCompare(s2);
			// sort by first name in the same company
			if (order == 0) {
				s1 = a.firstname;
				if (s1 == null || s1.length == 0)
					s1 = a.email;
				s2 = b.firstname
				if (s2 == null || s2.length == 0)
					s2 = b.email;
				order = s1.localeCompare(s2);
				return order;
			} else {
				return order;
			}
		});
	}
}


function showContacts() {
	
	$("#contact-list").html("");
	
	// alphabetic order
	var order = "";
	
	for (var i=0; i<contacts.length; i++) {
		
		if (contacts[i]["email"].length == 0 || contacts[i]["email"] == null)
			continue;
		
		var group = "";
		
		if (sortBy == "firstname") {
			if (contacts[i]["firstname"] == null || contacts[i]["firstname"].length == 0) {
				if (contacts[i]["surname"] == null || contacts[i]["surname"].length == 0) {
					group = contacts[i]["email"].substr(0, 1).toUpperCase();
				} else {
					group = contacts[i]["surname"].substr(0, 1).toUpperCase();
				}
			} else {
				group = contacts[i]["firstname"].substr(0, 1).toUpperCase();
			}
		} else if (sortBy == "surname") {
			if (contacts[i]["surname"] == null || contacts[i]["surname"].length == 0) {
				group = contacts[i]["email"].substr(0, 1).toUpperCase();
			} else {
				group = contacts[i]["surname"].substr(0, 1).toUpperCase();
			}
		} else if (sortBy == "company") {
			if (contacts[i]["company"] == null || contacts[i]["company"].length == 0)
				contacts[i]["company"] = " Unknown";
			group = contacts[i]["company"].toUpperCase();
		}
		
		if (group != order) {
			order = group;
			var label = order;
			if (sortBy == "company") {
				label = order.substr(0, 1) + "<span class=\"contact-list-label-small\">" + order.substr(1) + "</span>";
			}
			$("#contact-list").append(
				"<div class=\"contact-list-label\">" + label + "</div>"
			);
		}
		
		var name = contacts[i]["email"];
		if (contacts[i]["firstname"] !== null && contacts[i]["firstname"].length > 0) {
			name = contacts[i]["firstname"];
			if (contacts[i]["surname"] !== null && contacts[i]["surname"].length > 0) {
				name += " " + contacts[i]["surname"];
			}
		}
		
		$("#contact-list").append(
			"<div class=\"contact-list-item\" cid=\"" + contacts[i]["id"] + "\">" + 
				"<div class=\"contact-list-item-del\">Delete</div>" +
				"<div class=\"contact-list-item-text\">" + name + "</div>" +
				"<div class=\"contact-list-item-del-icon ion-close llicon-button\"></div>" +
			"</div>"
		);
	}
}


service.getContacts(function(response) {
	contacts = response.data.contacts;
	sortContacts();
	showContacts();
});
