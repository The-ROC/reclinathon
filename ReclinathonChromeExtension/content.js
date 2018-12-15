var destinationUrl = "";
var refreshTime = "";
var sidebarUrl = "";
var isFeedPage = false;

function createXMLHttpRequest() 
{
	try { return new XMLHttpRequest(); } catch(e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	alert("XMLHttpRequest not supported");
	return null;
}

function sleep(ms)
{
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function GetNextDestination(url)
{
	//
	// First, determine whether or not we're in dev mode.  If the last feed page navigation was from localhost, we're in dev mode.
	// If the last feed page navigation was from reclinathon.com, we're not in dev mode.  This allows the same extension to use a
	// local database or the official Reclinathon data, as necessary.
	//
	var isLocalHost = false;
	
	if (url.includes("rtt/mockup/feed.php"))
	{
		console.log("feed page navigated");
		
		isFeedPage = true;
		
		if (url.includes("local"))
		{
			console.log("feed page is localhost");
			isLocalHost = true;
		}
	}
	
	var storageComplete = false;
	
	if (!isFeedPage)
	{
		console.log("Load isLocalHost from storage.");
		chrome.storage.local.get(['localhostmode'], function(result) { isLocalHost = result.localhostmode; storageComplete = true;});
	}
	else
	{
		console.log("Save isLocalHost to storage.");
		chrome.storage.local.set({'localhostmode': isLocalHost}, function() { storageComplete = true;});
	}
	
	while (!storageComplete)
	{
		await sleep(10);
	}
	
	console.log('localhostmode is ' + isLocalHost);
	
	var xhReq = createXMLHttpRequest();
	var apiUrl = (isLocalHost ? "http://localhost/rtt/mockup/extensiondata.php?sourceUrl=" : "https://reclinathon.com/rtt/mockup/extensiondata.php?sourceUrl=") + url;
	
	console.log("API URL is " + apiUrl);

	// Send a request to the Reclinathon API to determine whether or not the current URL should show the sidebar or auto-redirect after some time.
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
				console.log('destinationUrl: ' + destinationUrl);
				refreshTime = parseInt(result[0].getAttribute("time"));
				console.log('refreshTime: ' + refreshTime);
				sidebarUrl = result[0].getAttribute("sidebar");
				console.log('sidebarUrl: ' + sidebarUrl);
				ProcessCurrentUrl();
			}
		}
	};
	xhReq.send(null);
}

function ProcessCurrentUrl()
{
	if (isFeedPage)
	{
		var extensionActiveInput = document.getElementById("extensionActiveInput");
		if (extensionActiveInput != null)
		{
			extensionActiveInput.value = "true";
		}
	}
	
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