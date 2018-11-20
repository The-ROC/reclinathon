var destinationUrl = "";
var refreshTime = "";
var sidebarUrl = "";

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
	var apiUrl = "https://reclinathon.com/rtt/mockup/extensiondata.php?sourceUrl=" + url;

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
				sidebarUrl = result[0].getAttribute("sidebar");
				ProcessCurrentUrl();
			}
		}
	};
	xhReq.send(null);
}

function ProcessCurrentUrl()
{
	if (sidebarUrl != "")
	{
	    var width = window.getComputedStyle(document.body, null).getPropertyValue("width");
	    var newWidth = width - 450;
	    document.body.style.width = newWidth + "px";
	
	    var div = document.createElement('div');
	    div.style.position = 'fixed';
	    div.style.top = 0;
	    div.style.right = 0;
	    div.style.width = "400px";
	    div.style.height = "100%";
	    div.style.zIndex = 999999;
	
	    var iframe = document.createElement('iframe');
	    iframe.src = sidebarUrl;
	    iframe.style.width = "100%";
	    iframe.style.height = "100%";
	    div.appendChild(iframe);
	
	    document.body.appendChild(div);
	}
	
	if (destinationUrl != "")
	{	
		setTimeout(function(){ window.location.href = destinationUrl; }, refreshTime);
	}
}

(function()
{
	GetNextDestination(window.location.href);
})();