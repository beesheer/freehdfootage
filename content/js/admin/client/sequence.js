// Dimensions of sunburst.

var width = 1300;
var height = 600;
var radius = Math.min(width, height) / 2;

// Breadcrumb dimensions: width, height, spacing, width of tip/tail.
var b = {
	w: 370, h: 30, s: 3, t: 10
};

// Mapping of step names to colors.
var colors = [];

colors.push("#5687d1");
colors.push("#7b615c");
colors.push("#de783b");
colors.push("#6ab975");
colors.push("#a173d1");
colors.push("#bbbbbb");


$.ajax({
	url: '/admin/client/get-positive-negative-for-sunburst',
	dataType: "json",
	success: function (e) {
		console.log(e);
		drawPie([{name: "positive", data: e.results['positive']}, {name: "negative", data: e.results['negative']}]);
	}
});

// Total size of all segments; we set this later, after loading the data.
var totalSize = 0;

var vis = d3.select("#chart").append("svg:svg")
		.attr("width", width)
		.attr("height", height)
		.append("svg:g")
		.attr("id", "container")
		.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

var partition = d3.layout.partition()
		.size([2 * Math.PI, radius * radius])
		.value(function (d) {
			return d.size;
		});

var arc = d3.svg.arc()
		.startAngle(function (d) {
			return d.x;
		})
		.endAngle(function (d) {
			return d.x + d.dx;
		})
		.innerRadius(function (d) {
			return Math.sqrt(d.y);
		})
		.outerRadius(function (d) {
			return Math.sqrt(d.y + d.dy);
		});


// Use d3.text and d3.csv.parseRows so that we do not need to have a header
// row, and can receive the csv as an array of arrays.
var myJson;
var goodArray = [];
var goodConfidence = [];
var totalRecords = 0;
var myConfidence;
var start_date;
var end_date;
getChartData([0, 0]);

$("#date_from").val(min);
$("#date_to").val(max);

function getChartData(dates) {
	start_date = dates[0];
	end_date = dates[1];
	console.log(dates);
	$.ajax({
		url: "/admin/client/watson-chart",
		type: "get",
		async: false,
		timeout: 5000,
		data: "start_date=" + dates[0] + "&end_date=" + dates[1],
		success: function (e) {
			myJson = e.results;
			// createVisualization(myJson);
			for (var i = 0; i < myJson.length; i++) {
				goodArray.push([myJson[i].id, myJson[i].value['total']]);
				if (typeof myJson[i].value['average_confidence'] !== "undefined") {
					goodConfidence.push([myJson[i].id, myJson[i].value['average_confidence']]);
				}
				else
				{
					goodConfidence.push([myJson[i].id, myJson[i].value['confidence']]);
				}
			}
			localStorage.setItem("goodArray", JSON.stringify(goodArray));
			localStorage.setItem("goodConfidence", JSON.stringify(goodConfidence));
			totalRecords = e.count;
			//console.log(goodArray);
			$("#container").empty();
			var json = buildHierarchy(goodArray);
			//createVisualization(json);
			//createVisualization(myConfidence);
			rerender(json);
		},
		error: function (e) {
			alert(e.toSource());
		}
	});
}

function getConfidence() {
	$("#container").empty();
	goodConfidence = JSON.parse(localStorage.getItem("goodConfidence"));
	var json = buildHierarchy(goodConfidence);
	createVisualization(json);
	goodConfidence = null;
}

function getOccurences() {
	$("#container").empty();
	goodArray = JSON.parse(localStorage.getItem("goodArray"));
	var json = buildHierarchy(goodArray);
	createVisualization(json);
	goodArray = null;
}
/*
 d3.text("visit-sequences.csv", function(text) {
 var csv = d3.csv.parseRows(text);
 var json = buildHierarchy(csv);
 console.log(csv);
 createVisualization(json);
 });
 */
// Main function to draw and set up the visualization, once we have the data.
function createVisualization(json) {
	// Basic setup of page elements.
	initializeBreadcrumbTrail();
	//drawLegend();
	//d3.select("#togglelegend").on("click", toggleLegend);

	// Bounding circle underneath the sunburst, to make it easier to detect
	// when the mouse leaves the parent g.
	vis.append("svg:circle")
			.attr("r", radius)
			.style("opacity", 0);

	// For efficiency, filter nodes to keep only those large enough to see.
	var nodes = partition.nodes(json)
			.filter(function (d) {
				return (d.dx > 0.005); // 0.005 radians = 0.29 degrees
			});

	var path = vis.data([json]).selectAll("path")
			.data(nodes)
			.enter().append("svg:path")
			.attr("display", function (d) {
				return d.depth ? null : "none";
			})
			.attr("d", arc)
			.attr("fill-rule", "evenodd")
			.style("fill", function (d) {
				return colors[Math.floor((Math.random() * 5) + 1)];
			})
			.style("opacity", 1)
			.on("mouseover", mouseover)
			.on("click", clicker);

	// Add the mouseleave handler to the bounding circle.
	d3.select("#container").on("mouseleave", mouseleave);

	// Get total size of the tree = value of root node from partition.
	totalSize = path.node().__data__.value;
}
;

// Main function to draw and set up the visualization, once we have the data.
function rerender(json) {
	// Basic setup of page elements.
	initializeBreadcrumbTrail();
	//drawLegend();
	//d3.select("#togglelegend").on("click", toggleLegend);

	// Bounding circle underneath the sunburst, to make it easier to detect
	// when the mouse leaves the parent g.
	vis.append("svg:circle")
			.attr("r", radius)
			.style("opacity", 0);

	// For efficiency, filter nodes to keep only those large enough to see.
	var nodes = partition.nodes(json)
			.filter(function (d) {
				return (d.dx > 0.005); // 0.005 radians = 0.29 degrees
			});

	var path = vis.data([json]).selectAll("path")
			.data(nodes)
			.enter()
			.append("svg:path")
			.attr("display", function (d) {
				return d.depth ? null : "none";
			})
			.style("fill", "gray")
			.attr("d", arc)
			.attr("fill-rule", "evenodd")
			.transition()
			.duration(200)
			.style("fill", function (d) {
				return colors[Math.floor((Math.random() * 5) + 1)];
			})
			.style("opacity", 1);


	vis.selectAll("path").on("mouseover", mouseover);
	vis.selectAll("path").on("click", clicker);

	// Add the mouseleave handler to the bounding circle.
	d3.select("#container").on("mouseleave", mouseleave);

	// Get total size of the tree = value of root node from partition.
	totalSize = path.node().__data__.value;
}
;


function clicker(d) {
	var temp = getAncestors(d);
	var sequence = "";
	var counter = 0;
	for (var j = 0; j < goodArray.length; j++)
	{
		if (goodArray[j][0].match(new RegExp(".*" + d.name.replace(")", "").replace("(", "") + ".*", "gi")))
		{
			counter += goodArray[j][1];
		}
	}
	console.log(d.name + " " + (counter / totalSize) * 100);
	for (var i = 0; i < temp.length; i++)
	{
		temp[i].name = temp[i].name.replace("undefined", "");
		sequence += temp[i].name + ".*";
	}

	$("#preloader").show();

	$.ajax({
		url: '/admin/client/get-survey',
		data: "data=" + sequence + "&start_date=" + start_date + "&end_date=" + end_date,
		dataType: "json",
		type: "post",
		success: function (e) {
			$("#preloader").hide();
			$("#sub_top").empty();
			for (j in e.results) {
				data = Object.keys(e.results[j]);
				for (k in data) {
					temp = Object.keys(e.results[j][data[k]]);
					for (jj in temp) {
						if (typeof (e.results[j][data[k]][temp[jj]].positive) !== "undefined") {
							$("#sub_top").append(j + " " + data[k] + " " + temp[jj] + ":positive -->" + e.results[j][data[k]][temp[jj]].positive + "<br />");
						}
						else
						{
							$("#sub_top").append(j + " " + data[k] + " " + temp[jj] + ":positive -->0<br />");
						}
						if (typeof (e.results[j][data[k]][temp[jj]].negative) !== "undefined") {
							$("#sub_top").append(j + " " + data[k] + " " + temp[jj] + ":negative -->" + e.results[j][data[k]][temp[jj]].negative + "<br />");
						}
						else
						{
							$("#sub_top").append(j + " " + data[k] + " " + temp[jj] + ":negative -->0<br />");
						}
					}
				}
			}
		}
	});
}



// Fade all but the current sequence, and show it in the breadcrumb trail.
function mouseover(d) {
	$("#details").html("<h3>Answer Sources & Survey Results</h3>");
	var percentage = (100 * d.value / totalSize).toPrecision(3);
	var percentageString = percentage + "%";
	if (percentage < 0.1) {
		percentageString = "< 0.1%";
	}

	d3.select("#percentage")
			.text(percentageString);

	d3.select("#explanation")
			.style("visibility", "visible");
	counter = 0;
	var sequenceArray = getAncestors(d);
	for (var j in sequenceArray) {
		if (sequenceArray[j].name !== "undefined") {
			$("#details").append("<b>" + (100 * sequenceArray[j].value / totalSize).toPrecision(3) + "%</b>    " + sequenceArray[j].name + " " + "<br />");
		}
	}
	updateBreadcrumbs(sequenceArray, percentageString);

	// Fade all the segments.
	d3.selectAll("path")
			.style("opacity", 0.3);

	// Then highlight only those that are an ancestor of the current segment.
	vis.selectAll("path")
			.filter(function (node) {
				return (sequenceArray.indexOf(node) >= 0);
			})
			.style("opacity", 1);
}

// Restore everything to full opacity when moving off the visualization.
function mouseleave(d) {

	// Hide the breadcrumb trail
	d3.select("#trail")
			.style("visibility", "hidden");

	// Deactivate all segments during transition.
	d3.selectAll("path").on("mouseover", null);

	// Transition each segment to full opacity and then reactivate it.
	d3.selectAll("path")
			.transition()
			.duration(1000)
			.style("opacity", 1)
			.each("end", function () {
				d3.select(this).on("mouseover", mouseover);
			});

	d3.select("#explanation")
			.style("visibility", "hidden");
}

// Given a node in a partition layout, return an array of all of its ancestor
// nodes, highest first, but excluding the root.
function getAncestors(node) {
	var path = [];
	var current = node;
	while (current.parent) {
		path.unshift(current);
		current = current.parent;
	}
	return path;
}

function initializeBreadcrumbTrail() {
	// Add the svg area.
	var trail = d3.select("#sequence").append("svg:svg")
			.attr("width", width * 2)
			.attr("height", 50)
			.attr("id", "trail");
	// Add the label at the end, for the percentage.
	trail.append("svg:text")
			.attr("id", "endlabel")
			.style("fill", "#000");
}

// Generate a string that describes the points of a breadcrumb polygon.
function breadcrumbPoints(d, i) {
	var points = [];
	points.push("0,0");
	points.push(b.w + ",0");
	points.push(b.w + b.t + "," + (b.h / 2));
	points.push(b.w + "," + b.h);
	points.push("0," + b.h);
	if (i > 0) { // Leftmost breadcrumb; don't include 6th vertex.
		points.push(b.t + "," + (b.h / 2));
	}
	return points.join(" ");
}

//------------------------------------------------------------------------------
// Prepare Data for slider

var sliderValues = [];
var min;
var max;

$.ajax({
	url: '/admin/client/get-max-min-date-for-sunburst-chart',
	cache: false,
	dataType: "json",
	async: false,
	success: function (e) {
		console.log(e);
		sliderValues.push(e.results['min']);
		max = e.results['max'];
		sliderValues.push(e.results['max']);
		min = e.results['min'];
		console.log("here are the slider values");
		console.log(sliderValues);
		console.log("end of slider values");
		createSlider();
	}
});
function createSlider() {
	$("#slider").slider({
		range: true,
		min: min,
		max: max,
		values: sliderValues,
		change: function (e, v) {
			//console.log(new Date(v.value * 1000));
			var dates = $("#slider").slider("option", "values");
			console.log(new Date(dates[0] * 1000));
			console.log(new Date(dates[1] * 1000));
			myJson = null;
			goodArray = [];
			goodConfidence = [];
			totalRecords = 0;
			myConfidence = null;
			getChartData([dates[0], dates[1]]);
			$("#date_from").val(new Date(dates[0] * 1000).toLocaleString());
			$("#date_to").val(new Date(dates[1] * 1000).toLocaleString());
		}
	});
}
//------------------------------------------------------------------------------
// Update the breadcrumb trail to show the current sequence and percentage.
function updateBreadcrumbs(nodeArray, percentageString) {

	// Data join; key function combines name and depth (= position in sequence).
	var g = d3.select("#trail")
			.selectAll("g")
			.data(nodeArray, function (d) {
				return d.name + d.depth;
			});

	// Add breadcrumb and label for entering nodes.
	var entering = g.enter().append("svg:g");

	entering.append("svg:polygon")
			.attr("points", breadcrumbPoints)
			.style("fill", function (d) {
				return colors[d.name];
			});

	entering.append("svg:text")
			.attr("x", (b.w + b.t) / 2)
			.attr("y", b.h / 2)
			.attr("dy", "0.35em")
			.attr("text-anchor", "middle")
			.text(function (d) {
				return d.name;
			});

	// Set position for entering and updating nodes.
	g.attr("transform", function (d, i) {
		return "translate(" + i * (b.w + b.s) + ", 0)";
	});

	// Remove exiting nodes.
	g.exit().remove();

	// Now move and update the percentage at the end.
	d3.select("#trail").select("#endlabel")
			.attr("x", (nodeArray.length + 0.5) * (b.w + b.s))
			.attr("y", b.h / 2)
			.attr("dy", "0.35em")
			.attr("text-anchor", "middle")
			.text(percentageString);

	// Make the breadcrumb trail visible, if it's hidden.
	d3.select("#trail")
			.style("visibility", "");

}

function drawLegend() {

	// Dimensions of legend item: width, height, spacing, radius of rounded rect.
	var li = {
		w: 375, h: 30, s: 3, r: 3
	};

	var legend = d3.select("#legend").append("svg:svg")
			.attr("width", li.w)
			.attr("height", d3.keys(colors).length * (li.h + li.s));

	var g = legend.selectAll("g")
			.data(d3.entries(colors))
			.enter().append("svg:g")
			.attr("transform", function (d, i) {
				return "translate(0," + i * (li.h + li.s) + ")";
			});

	g.append("svg:rect")
			.attr("rx", li.r)
			.attr("ry", li.r)
			.attr("width", li.w)
			.attr("height", li.h)
			.style("fill", function (d) {
				return d.value;
			});

	g.append("svg:text")
			.attr("x", li.w / 2)
			.attr("y", li.h / 2)
			.attr("dy", "0.35em")
			.attr("text-anchor", "middle")
			.text(function (d) {
				return d.key;
			});
}

function toggleLegend() {
	var legend = d3.select("#legend");
	if (legend.style("visibility") == "hidden") {
		legend.style("visibility", "");
	} else {
		legend.style("visibility", "hidden");
	}
}


function drawPie(thedata) {
	var width = 120,
			height = 120,
			radius = Math.min(width, height) / 2;

	var color = d3.scale.ordinal()
			.range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

	var arc = d3.svg.arc()
			.outerRadius(radius - 10)
			.innerRadius(0);

	var pie = d3.layout.pie()
			.sort(null)
			.value(function (d) {
				return d.data;
			});

	var svg = d3.select("#sub_top").append("svg")
			.attr("width", width)
			.attr("height", height)
			.append("g")
			.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");


	data = thedata;


	var g = svg.selectAll(".arc")
			.data(pie(data))
			.enter().append("g")
			.attr("class", "arc");

	g.append("path")
			.attr("d", arc)
			.style("fill", function (d) {
				return color(d.data.name);
			});

	g.append("text")
			.attr("transform", function (d) {
				return "translate(" + arc.centroid(d) + ")";
			})
			.attr("dy", ".35em")
			.style("text-anchor", "middle")
			.text(function (d) {
				return d.data.name;
			});



}

// Take a 2-column CSV and transform it into a hierarchical structure suitable
// for a partition layout. The first column is a sequence of step names, from
// root to leaf, separated by hyphens. The second column is a count of how
// often that sequence occurred.
function buildHierarchy(csv) {
	var root = {"name": "root", "children": []};
	for (var i = 0; i < csv.length; i++) {
		var sequence = csv[i][0];
		var size = +csv[i][1];
		if (isNaN(size)) { // e.g. if this is a header row
			continue;
		}
		var parts = sequence.split("-");
		var currentNode = root;
		for (var j = 0; j < parts.length; j++) {
			var children = currentNode["children"];
			var nodeName = parts[j];
			var childNode;
			if (j + 1 < parts.length) {
				// Not yet at the end of the sequence; move down the tree.
				var foundChild = false;
				for (var k = 0; k < children.length; k++) {
					if (children[k]["name"] == nodeName) {
						childNode = children[k];
						foundChild = true;
						break;
					}
				}
				// If we don't already have a child node for this branch, create it.
				if (!foundChild) {
					childNode = {"name": nodeName, "children": []};
					children.push(childNode);
				}
				currentNode = childNode;
			} else {
				// Reached the end of the sequence; create a leaf node.
				childNode = {"name": nodeName, "size": size};
				children.push(childNode);
			}
		}
	}
	return root;
}
;
