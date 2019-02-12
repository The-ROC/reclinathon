var destinationUrl = "";
var refreshTime = "";
var sidebarUrl = "";
var isFeedPage = false;
var storageCount = 0;
var baseUrl = "https://reclinathon.com";
var mode = 'subtitle';

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
	var localPrefix = "http://localhost";
	
	if (url.includes("rtt/mockup/feed.php"))
	{
		console.log("feed page navigated");
		
		isFeedPage = true;
		
		if (url.includes("local"))
		{
			console.log("feed page is localhost");
			// Split url up to /rtt/ and save as variable for apiUrl prefix
			isLocalHost = true;
			localPrefix = url.split("/rtt")[0];
		}
	}
	
	storageCount = 0;
	
	if (!isFeedPage)
	{
		console.log("Load isLocalHost from storage.");
		chrome.storage.local.get(['localhostmode'], function(result) { isLocalHost = result.localhostmode; storageCount++;});
		chrome.storage.local.get(['localPrefix'], function(result) { localPrefix = result.localPrefix; storageCount++;});
	}
	else
	{
		console.log("Save isLocalHost to storage.");
		chrome.storage.local.set({'localhostmode': isLocalHost}, function() { storageCount++;});
		chrome.storage.local.set({'localPrefix': localPrefix}, function() { storageCount++;});
	}
	
	while (!storageComplete())
	{
		await sleep(10);
	}
	
	console.log('localhostmode is ' + isLocalHost);
	console.log('local prefix is ' + localPrefix);
	
	var xhReq = createXMLHttpRequest();
	baseUrl = isLocalHost ? localPrefix : "https://reclinathon.com";
	var apiUrl = baseUrl + "/rtt/mockup/extensiondata.php?sourceUrl=" + url + "&baseUrl=" + baseUrl;
	
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
				refreshTime = parseInt(result[0].getAttribute("time")) + 2000;	// 10ms delay to prevent refresh loop
				console.log('refreshTime: ' + refreshTime);
				sidebarUrl = result[0].getAttribute("sidebar");
				console.log('sidebarUrl: ' + sidebarUrl);
				ProcessCurrentUrl();
			}
		}
	};
	xhReq.send(null);
}

function storageComplete()
{
	return storageCount == 2;
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
		if(document.getElementById('subtitle'))
			document.getElementById('subtitle').remove();
		if(document.getElementById('sidebar'))
			document.getElementById('sidebar').remove();
		if(document.getElementById('toggle'))
			document.getElementById('toggle').remove();

			var switchDiv = document.createElement('div');
			switchDiv.id = 'toggle';
			switchDiv.style.position = 'fixed';
			switchDiv.style.zIndex = 9999999;
			switchDiv.style.height = '100%';
			switchDiv.style.width = '25px';
			//switchDiv.style.border = '1px solid white';
			switchDiv.onclick = toggleChatMode;

			// Sidebar mode
			if(mode == 'sidebar') {
				var width = window.getComputedStyle(document.body, null).getPropertyValue("width");
				var newWidth = width - 450;
				document.body.style.width = newWidth + "px";
		
				var div = document.createElement('div');
				div.id = 'sidebar';
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

				switchDiv.style.right = '375px';

			// Subtitle mode
			} else if(mode == 'subtitle') {
				var div = document.createElement('div');
				div.id = 'subtitle';
				div.style.position = 'fixed';
				div.style.bottom = '25px';
				div.style.width = '100%';
				//div.style.border = '1px solid white';
				div.style['text-align'] = 'center';
				div.style.zIndex = 999999;
				div.style.color = 'white';
				div.style['text-shadow'] = '-2px 0 black, 0 2px black, 2px 0 black, 0 -2px black';
				div.style['font-family'] = 'Arial, Verdana, Sans-serif';
				div.style['font-size'] = '40px';

				chatDiv = document.createElement('div');
				chatDiv.style.position = 'relative';
				chatDiv.style.width = '60%';
				chatDiv.style.margin = 'auto';
				//chatDiv.style.border = '1px solid white';
				chatDiv.style['text-align'] = 'center';
				chatDiv.style.zIndex = 9999999;
				chatDiv.style.color = 'white';
				chatDiv.style['text-shadow'] = '-2px 0 black, 0 2px black, 2px 0 black, 0 -2px black';
				chatDiv.style['font-family'] = 'Arial, Verdana, Sans-serif';
				chatDiv.style['font-size'] = '40px';

				document.body.appendChild(div);
				div.appendChild(chatDiv);

				//chatDiv.innerHTML = "Schmidt: How many times are we going to watch this stupid Meridian movie? Oh hi, Mark!";

				this.initSubtitleChat();
				setInterval(this.updateSubtitleChat, 3000);

				switchDiv.style.right = 0;
			}
	}

	document.body.appendChild(switchDiv);

	if (destinationUrl != "")
	{	
		setTimeout(function(){ window.location.href = destinationUrl; }, refreshTime);
	}
}

function toggleChatMode()
{
	if(mode == 'sidebar') {
		mode = 'subtitle';
	} else {
		mode = 'sidebar';
	}
	ProcessCurrentUrl();
}

var lastEventId = 0;
var currentChat = null;
var chatQueue = [];
var chatDiv;
function addSubtitleChat(chat)
{
	chatQueue.push(chat);
	if(currentChat == null)
		showSubtitleChat();
}

function showSubtitleChat()
{
	currentChat = chatQueue.shift();
	chatDiv.innerHTML = currentChat.displayName + ": " + currentChat.message;
	setTimeout(hideSubtitleChat, 10000);
}

function hideSubtitleChat()
{
	chatDiv.innerHTML = "";
	currentChat = null;
	if(chatQueue.length)
		showSubtitleChat();
}

function initSubtitleChat()
{
	updateSubtitleChat(true);
}

function updateSubtitleChat(init=false)
{
		var xhReq = createXMLHttpRequest();
		var params = "";

		xhReq.open("GET", baseUrl + "/rtt/mockup/feedpost.php?lastEventID=" + lastEventId);
		xhReq.overrideMimeType('text/xml');
		xhReq.onreadystatechange = function() {
				if (xhReq.readyState != 4) return;

				var xml = xhReq.responseXML;

				var feedEvents = xml.getElementsByTagName("FeedEvent");
				for(var i = 0; i < feedEvents.length; i++)
				{
						var icon = feedEvents[i].getElementsByTagName("Icon")[0].childNodes[0].nodeValue;
						var displayName = feedEvents[i].getElementsByTagName("DisplayName")[0].childNodes[0].nodeValue;
						var message = feedEvents[i].getElementsByTagName("Message")[0].childNodes[0].nodeValue;
						var timestamp = feedEvents[i].getElementsByTagName("Timestamp")[0].childNodes[0].nodeValue;
						var eventId = parseInt(feedEvents[i].getElementsByTagName("EventID")[0].childNodes[0].nodeValue);

						if(eventId > lastEventId)
								lastEventId = eventId;
								
						if(!init) {
							addSubtitleChat({icon: icon, message: message, displayName: displayName});
						}
				}
		}
		xhReq.send(params);
}

(function()
{
	GetNextDestination(window.location.href);
})();