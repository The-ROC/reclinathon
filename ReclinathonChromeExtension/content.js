var destinationUrl = "";
var refreshTime = "";

function createXMLHttpRequest() 
{
	try { return new XMLHttpRequest(); } catch(e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	alert("XMLHttpRequest not supported");
	return null;
}

function GetNextDestination(url)
{
	var xhReq = createXMLHttpRequest();
	var apiUrl = "https://reclinathon.com/rtt/extensiondemo.php?sourceUrl=" + url;

	xhReq.open("GET", apiUrl, true);
	xhReq.onreadystatechange = function() {
		if (xhReq.readyState != 4) { return; }
		var xml = xhReq.responseXML;
		
		if (xml != null)
		{
			var result = xml.getElementsByTagName("next");
			if (result.length > 0)
			{
				destinationUrl = result[0].getAttribute("url");
				refreshTime = parseInt(result[0].getAttribute("time"));
				ProcessCurrentUrl();
			}
		}
	};
	xhReq.send(null);
}

function ProcessCurrentUrl()
{
	var width = window.getComputedStyle(document.body, null).getPropertyValue("width");
	var newWidth = width - 350;
	document.body.style.width = newWidth + "px";
	
	var div = document.createElement('div');
	div.style.position = 'fixed';
	div.style.top = 0;
	div.style.right = 0;
	div.style.width = "300px";
	div.style.height = "100%";
	div.style.zIndex = 999999;
	
	var iframe = document.createElement('iframe');
	iframe.src = "https://www.reclinathon.com/rtt/feed_mockup.php";
	iframe.style.width = "100%";
	iframe.style.height = "100%";
	div.appendChild(iframe);
	
	if (destinationUrl != "")
	{
		document.body.appendChild(div);
		setTimeout(function(){ window.location.href = destinationUrl; }, refreshTime);
	}
}

(function()
{
	GetNextDestination(window.location.href);
})();