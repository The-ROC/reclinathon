function Datapoint(x, y, color)
{
	this.x = x;
	this.y = y;
	this.color = color;
}

function Plot(color)
{
	this.datapoints = new Array();
	this.color = color;
}

Plot.prototype.addDatapoint = function(x, y, color)
{
	this.datapoints.push(new Datapoint(x, y, color));
};

Plot.prototype.getValueAtPosition = function(x)
{
	var value = 0;
	for (var i = 0; i < this.datapoints.length; i++)
	{
		if (x < this.datapoints[i].x)
		{
			if (i > 0)
			{
				var x1 = this.datapoints[i-1].x;
				var y1 = this.datapoints[i-1].y;
				var x2 = this.datapoints[i].x;
				var y2 = this.datapoints[i].y;
				var slope = ((y2 - y1) / (x2 - x1));
				var delta = ((x - x1) * slope);
				value = y1 + delta;
			}
			
			break;
		}
	}
	
	return value;
};

function Plotter(id, topLeft, topRight, width, height, border, horizontalMin, horizontalMax, verticalMin, verticalMax)
{
	var canvas = document.createElement('canvas');
	canvas.id = id;
	canvas.width = width;
	canvas.height = height;
	canvas.style.border = "1px dotted";
	
	this.canvas = canvas;
	this.id = id;
	this.width = width;
	this.height = height;
	this.border = border;	
	this.font = "bold 12px sans-serif";
	
	this.setPlotAreaScale(horizontalMin, horizontalMax, verticalMin, verticalMax);
	
	this.plots = new Array();
}

Plotter.prototype.clearCanvas = function()
{
	this.canvas.width = this.width;
};

Plotter.prototype.setPlotAreaScale = function(horizontalMin, horizontalMax, verticalMin, verticalMax)
{
	this.horizontalMin = horizontalMin;
	this.horizontalMax = horizontalMax;
	this.horizontalScale = ((this.width - (2 * this.border)) / (horizontalMax - horizontalMin));
	this.verticalMin = verticalMin;
	this.verticalMax = verticalMax;
	this.verticalScale = ((this.height - (2 * this.border)) / (verticalMax - verticalMin));
};

Plotter.prototype.addPlot = function(plot)
{
	this.plots.push(plot);
};

Plotter.prototype.onInterval = function()
{
    var recliningSeconds = (this.currentX * this.currentY) + 1;
	this.currentX += 1;
	this.currentY = recliningSeconds / this.currentX;
	this.addLineToPlot(this.currentX, this.currentY);
};

Plotter.prototype.addToDocument = function() 
{
	document.body.appendChild(this.canvas);
};

Plotter.prototype.addMouseMoveEventHandler = function(f)
{
	this.canvas.addEventListener('mousemove', f, false);
}

Plotter.prototype.getX = function(x)
{
	return (((x - this.horizontalMin) * this.horizontalScale) + this.border);
};

Plotter.prototype.getY = function(y)
{
	return (((this.verticalMax - y) * this.verticalScale) + this.border);
};

Plotter.prototype.showGrid = function()
{
	var context = this.canvas.getContext("2d");
		
	for (var x = this.border; x <= (this.width - this.border); x += 10) 
	{
		context.moveTo(x, this.border);
		context.lineTo(x, this.height - this.border);
	}

	for (var y = this.border; y <= (this.height - this.border); y += 10) 
	{
		context.moveTo(this.border, y);
		context.lineTo(this.width - this.border, y);
	}
		
	context.strokeStyle = "#eee";
	context.stroke();
};

Plotter.prototype.drawPoint = function(x, y, color)
{
	var context = this.canvas.getContext("2d");
	var trueX = this.getX(x);
	var trueY = this.getY(y);

	context.beginPath();
	context.arc(trueX, trueY, 3, 0, Math.PI * 2, false);
	context.closePath();

	context.strokeStyle = color;
	context.stroke();	
	
	context.fillStyle = color;
	context.fill();
};

Plotter.prototype.drawLine = function(x1, y1, x2, y2, color)
{
	var context = this.canvas.getContext("2d");
	
	context.beginPath();
	context.moveTo(this.getX(x1), this.getY(y1));
	context.lineTo(this.getX(x2), this.getY(y2));
	
	context.strokeStyle = color;
	context.stroke();
};

Plotter.prototype.setFont = function(font)
{
	this.font = font;
};

Plotter.prototype.drawText = function(string, x, y, align, baseline, color)
{
	var context = this.canvas.getContext("2d");
	
	context.font = this.font;
	context.textAlign = align;
	context.textBaseline = baseline;
	context.fillStyle = color;
	context.fillText(string, this.getX(x), this.getY(y));
};

Plotter.prototype.startPlot = function(x, y)
{
	var context = this.canvas.getContext("2d");
	context.moveTo(this.getX(x), this.getY(y));
};

Plotter.prototype.addLineToPlot = function(x, y, color)
{
	var context = this.canvas.getContext("2d");
	
	context.lineTo(this.getX(x), this.getY(y));
	
	context.strokeStyle = color;
	context.stroke();
};

Plotter.prototype.drawPlots = function()
{
	for (var i = 0; i < this.plots.length; i++)
	{
		var plot = this.plots[i];
		
		if (plot.datapoints.length < 2)
		{
			continue;
		}
		
		this.startPlot(plot.datapoints[0].x, plot.datapoints[0].y);
		this.drawPoint(plot.datapoints[0].x, plot.datapoints[0].y, plot.color);
		
		for (var j = 1; j < plot.datapoints.length; j++)
		{
			var datapoint = plot.datapoints[j];
			this.addLineToPlot(datapoint.x, datapoint.y, plot.color);
			this.drawPoint(datapoint.x, datapoint.y, plot.color);
		}
	}
};

Plotter.prototype.getClosestValueAtPosition = function(x, y)
{
	var closestDatapoint = null;
	var smallestDistance = 0;
	
    if (this.plots.length > 0)
	{
		var canvasRect = this.canvas.getBoundingClientRect();
		var adjustedX = (((x - canvasRect.left) - this.border) / this.horizontalScale) + this.horizontalMin;
		var adjustedY = (((((y - canvasRect.top) - this.border) / this.verticalScale) - this.verticalMax) * -1);
		
		for (var i = 0; i < this.plots.length; i++)
		{
			var value = this.plots[i].getValueAtPosition(adjustedX);
			if (value > 0)
			{
				var currentDistance = Math.abs(adjustedY - value);
				if (closestDatapoint == null || currentDistance < smallestDistance)
				{
					closestDatapoint =  new Datapoint(adjustedX, value, this.plots[i].color);
					smallestDistance = currentDistance;
				}
			}
		}
	}
	return closestDatapoint;
};

function createXMLHttpRequest() 
{
	try { return new XMLHttpRequest(); } catch(e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	alert("XMLHttpRequest not supported");
	return null;
}

function PlotRecliningRatioCallback(xhReq, plotter, color)
{
	if (xhReq.readyState != 4) 
	{ 
		return; 
	}
		
	var plot = new Plot(color);
	plot.addDatapoint(0, 1, color);

	var xml = xhReq.responseXML;

	var recliningRatios = xml.getElementsByTagName("RecliningRatio");
	
	for(var i = 0; i < recliningRatios.length; i++)
	{
		var timestamp = recliningRatios[i].getAttribute("timestamp");
		var ratio = recliningRatios[i].getAttribute("ratio");
		
		plot.addDatapoint(parseFloat(timestamp), parseFloat(ratio), color);
	}
	
	plotter.addPlot(plot);
	plotter.drawPlots();
}

function PlotRecliningRatio(plotter, season, color)
{
	var xhReq = createXMLHttpRequest();
	xhReq.open("GET", "GetRecliningRatio.php?season=" + season, true);
	xhReq.onreadystatechange = function() 
	{
		PlotRecliningRatioCallback(xhReq, plotter, color);
	};
	xhReq.send(null);
 }
 
 function PopulateSeasonBox()
{
	var xhReq = createXMLHttpRequest();
	xhReq.open("GET", "GetSeasons.php", true);
	xhReq.onreadystatechange = function() 
	{
		PopulateSeasonBoxCallback(xhReq, document.getElementById("seasonBox"));
	};
	xhReq.send(null);
 }
 
 function PopulateSeasonBoxCallback(xhReq, seasonBox)
{
	if (xhReq.readyState != 4) 
	{ 
		return; 
	}
		
	var xml = xhReq.responseXML;

	var seasons = xml.getElementsByTagName("Season");
	
	for(var i = 0; i < seasons.length; i++)
	{
		var seasonName = seasons[i].getAttribute("name");
		var option = document.createElement("option");
		
		option.text = seasonName;
		option.value = seasonName;
		seasonBox.add(option);
	}
}
 
 var plotter = null;
 
 function changeScale()
 {
	var verticalMin = parseFloat(document.getElementById("verticalMinBox").value);
	var verticalMax = parseFloat(document.getElementById("verticalMaxBox").value);
	var horizontalMin = parseFloat(document.getElementById("horizontalMinBox").value);
	var horizontalMax = parseFloat(document.getElementById("horizontalMaxBox").value);
	
	plotter.clearCanvas();
	plotter.setPlotAreaScale(horizontalMin, horizontalMax, verticalMin, verticalMax);
	plotter.showGrid();
	plotter.drawLine(94320, verticalMin, 94320, verticalMax, "green");
	plotter.drawLine(horizontalMin, verticalMin, horizontalMax, verticalMin, "black");
	plotter.drawLine(horizontalMin, verticalMin, horizontalMin, verticalMax, "black");
	plotter.drawText(verticalMax + " ", horizontalMin, verticalMax, "right", "top", "black");
	plotter.drawText(verticalMin + " ", horizontalMin, verticalMin, "right", "bottom", "black");
	plotter.drawText(horizontalMin, horizontalMin, verticalMin, "left", "top", "black");
	plotter.drawText(horizontalMax, horizontalMax, verticalMin, "right", "top", "black");
	plotter.drawText("time (s)", horizontalMin + ((horizontalMax - horizontalMin) / 2), verticalMin, "center", "top", "black");
	plotter.drawText("ratio ", horizontalMin, verticalMin + ((verticalMax - verticalMin) / 2), "right", "middle", "black");
	plotter.drawText("26.2 hours", 94320, verticalMax, "center", "bottom", "green");
	plotter.drawPlots();
}

function addPlotInput()
{
	var seasonBox = document.getElementById("seasonBox");
	var colorBox = document.getElementById("colorBox");
    var newSeasonBox = document.createElement("input");
	var newSpace = document.createTextNode(" ");
	var newColorBox = document.createElement("input");
	var newBreak = document.createElement("br");
	
	newSeasonBox.type = "text";
	newSeasonBox.value = seasonBox.options[seasonBox.selectedIndex].value;
	newSeasonBox.style.border = "1px solid white";
	newSeasonBox.readOnly = true;
	
	newColorBox.type = "text";
	newColorBox.value = colorBox.value;
	newColorBox.style.border = "1px solid white";
	newColorBox.readOnly = true;
	
	seasonBox.parentNode.insertBefore(newSeasonBox, seasonBox);
	seasonBox.parentNode.insertBefore(newSpace, seasonBox);
	seasonBox.parentNode.insertBefore(newColorBox, seasonBox);
	seasonBox.parentNode.insertBefore(newBreak, seasonBox);
	
	seasonBox.selectedIndex = 0;
	colorBox.value = "";
}
 
 function createPlotter()
 {
	PopulateSeasonBox();
	
	var canvas = document.getElementById("RecliningRatioPlotter");
	
	if (null != canvas)
	{
		canvas.parentNode.removeChild(canvas);
	}
	
	if (null == plotter)
	{
	    plotter = new Plotter("RecliningRatioPlotter", 0, 0, 1200, 600, 40, 0, 112000, 0, 1);
	}
	
	plotter.addToDocument();
	
	changeScale();
	
	plotter.addMouseMoveEventHandler(onPlotMouseMove);
}

function onPlotMouseMove(evt)
{
	var closestDatapoint = plotter.getClosestValueAtPosition(evt.clientX, evt.clientY);
	var valueBox = document.getElementById("valueBox");
	if (null != closestDatapoint)
	{
		valueBox.value = closestDatapoint.y;
		changeScale();
		plotter.drawPoint(closestDatapoint.x, closestDatapoint.y, closestDatapoint.color);
	}
}

function draw()
{
    var seasonBox = document.getElementById("seasonBox");
	var season = seasonBox.options[seasonBox.selectedIndex].value;
	var color = document.getElementById("colorBox").value;
	
	PlotRecliningRatio(plotter, season, color);
	
	addPlotInput();
		
	//setInterval(function(){plotter.onInterval()},1000);
}
	
	