<?php
if(isset($_GET['s'])) {
    $s = $_GET['s'];

    $url = "https://www.netflix.com/api/shakti/va91c86c0/pathEvaluator?drmSystem=widevine&isWatchlistEnabled=false&isShortformEnabled=false&isVolatileBillboardsEnabled=false&falcor_server=0.1.0&withSize=true&materialize=true";
    $data = '{"paths":[["search","byTerm","|' . $s . '","titles",48,["id","length","listId","name","referenceId","requestId","trackIds"]],["search","byTerm","|' . $s . '","titles",48,{"from":0,"to":48},"summary"],["search","byTerm","|' . $s . '","titles",48,{"from":0,"to":48},"reference",["promoVideo","summary","title","titleMaturity","userRating","userRatingRequestId","runtime"]],["search","byTerm","|' . $s . '","titles",48,{"from":0,"to":48},"reference","boxarts","_342x192","jpg"]],"authURL":"1536265599850.Nw0CQSLoth8eNtVv81hYyQNOT8U="}';

    $headers = array(
        'Content-Type: application/json',
        'Cookie: memclid=c258d1f5-41f6-424b-9bd1-3b8eeacdd3c0; pas=%7B%22supplementals%22%3A%7B%22muted%22%3Atrue%7D%7D; _ga=GA1.2.357791807.1533583894; VisitorId=002~c258d1f5-41f6-424b-9bd1-3b8eeacdd3c0~1533583893452~true~1533583894505~; fbm_163114453728333=base_domain=.netflix.com; clSharedContext=a4a8cdce-5079-43de-b86f-0120d4838de8; fbsr_163114453728333=8CBkccDHE6wUbBWQycmZFZhWAQhAZ8p_ZXPku3j088o.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImNvZGUiOiJBUUF2UTFFWE56eWlhVTdma1RYV0k2NkpJM25Sa2pCS3pDX0JhNFdOWnJGeTJPMkdjbHJiY3BxUUcxbVdDcHAxV29Sal9NTVhnRkhQeEtwRzZTR3VGOUJzWWtrSzFwd1d5QldxNXhLelNaNUZHLXVNRnFsTUtYaDZVV20zWnBlYlVUcVRUOXNNLVhseTVNbFRfVTB2T0NUaUJXb2ZxV1ZqZkRkYVloaE1lWFdYX3VlS2U0NjA0Ukw4OWdBbU9nRGNJdGh2OXpuRnZIOTVhRUZpQlZkNXZQcUZwSGJzM3J6cHotRnREbUlVXy1RNExCLVoxVlFGYkdtNE16U2lia3ctdElxaXlrblRMU1pRcG9fcjNRYVZLRGdoalo4ZG1pVE5sYmZmSXRGUHoyZ2J0Q05OOV9iSHhXeGhER09ENTNFcHZqYUVfMjRqeXUwejVreFc1aGNCWEE5MCIsImlzc3VlZF9hdCI6MTUzNjIwMjE3NSwidXNlcl9pZCI6IjQ4MDU0MzEifQ; nfvdid=BQFmAAEBEK23KnyK2MUQbFC2jYmcCRVgB7KpDdLrRazjPGbuizgt5lDksLzkF2BEmson9J%2FWFCNNgq7edgKb25jiJCG9NUemS9XsHiZxHCyK1wn98uj4a%2Bf0wec43S2R3Eq86JsOTBBk1qetAxC0l7jycUT%2BTIBD; cL=1536202332554%7C153620217844003280%7C15362021781745376%7C%7C10%7CTZONSL62FNDF3AWJRDGLY7ZKZE; SecureNetflixId=v%3D2%26mac%3DAQEAEQABABT9OUTk3tD9l4tqQjyBlGznWlYb03opzi0.%26dt%3D1536250316238; NetflixId=ct%3DBQAOAAEBEDrOCEsPB68yAjc5c4civ16B8H8E2pJPdLEe1pNPhbYImcZQpJqmrDIijykZhXNHG5eYG8VaYoGrB3eou87GvXoslvQI2OGJXJDRCvjhzq8qXIymuQojPvvcrw02nStYTnHhxs6n825kQNft8_gfmLFlSQ0X4p5uhNTufBtb_ygByod_Xp37fRANDJtIrSjFN_qrE9pBqpxkRCNaFqn4zQdO9mCVwNpoLFSyziW4T4IDuHSoU0ESW9tWKITzJEWRLLazF0QBwldCnHGY8QJPX3Vj9_UM-Nwv8n8YZLX7PXfWi-7HRgvopVWoB2x7yyPr-sfwjVCUQiDZ4mfRJsa0ojT_rIk4RrduSNFDzAOCZ6T0mgOzRIT5ASxE7-xCVRbiRpZish7T95KxOSrMzx2-8Z1PSwn54zhyDFxVL4GFpUYFL-TDX2gFQG1EGXaK7BGhU3JlLsuMUKhDvOczOpNlCwcj-9-74HT91-j0u2CadxtZsaD1UyyF3CupH_u3sSAoKymMKquVBjKt4yIuZKBIAynjvgvmr9WmLECNooRuGFavCKOnbzic2fv8WSF1O757j9MCQLb-SWDoyAVDMYDQoi7zQaR3orTRoJqxaXnU0D4CiVZJdm3gnpXFe8vRfAlM_-SDO3EJxREPrhOQS-_iWljSr9WRkyKT-CFAsaV5k4JQTlU.%26bt%3Ddbl%26ch%3DAQEAEAABABTsvmjJOn4m4ez0BNISWm6UsGeVTe59RSk.%26v%3D2%26mac%3DAQEAEAABABTig08UufJpmozR3FMJv3ncOfri_iyWu24.; profilesNewSession=0; lhpuuidh-browse-TZONSL62FNDF3AWJRDGLY7ZKZE=US%3AEN-US%3A96e55b49-1f27-46f5-b61b-09fe1b3fb1d1_ROOT; lhpuuidh-browse-TZONSL62FNDF3AWJRDGLY7ZKZE-T=1536265600916; playerPerfMetrics=%7B%22uiValue%22%3A%7B%22throughput%22%3A24760%2C%22throughputNiqr%22%3A0.28497777956803544%7D%2C%22mostRecentValue%22%3A%7B%22throughput%22%3A24760%2C%22throughputNiqr%22%3A0.28497777956803544%7D%7D'
    );
	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    echo(curl_exec($ch));
    curl_close($ch);
    exit();
}

if(isset($_GET['img'])) {
    $url = $_GET['img'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    echo(curl_exec($ch));
    curl_close($ch);
    exit();
}
?>

<html>

<head>
    <title>Create a Reclinathon</title>
    <link rel="stylesheet" type="text/css" href="mockup.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body class="noborder" onload="populateTimezoneOffset()">
    <div style="text-align:center">
        <img src="images/sign.png" width=360/><br />
        <br />
        Complete the form below to create a Reclinathon for millions of dudes to attend from all over the world!<br />
        <br />
        <form action="../processform.php" method="post">
            Start time<br />
            <input type="datetime-local" name="startTime" /><br />
            <br />
            <div id="moviesHeader" style="width:360px; margin:0 auto">Movies<br /></div>
            <div id="moviesInput" class="dropdown">
            <input type="text" id="movieInput" name="movieInput" style="width:360px" oninput="getSearchResults(this.value)" onclick="" />
                <div id="moviesDropdown" class="dropdown-content"></div>
            </div>
            <div id="hiddenInputs">
                <input type="hidden" name="class" value="REMOTE_RECLINATHON" />
				<input type="hidden" name="timezoneOffset" id="timezoneOffset" />
            </div>
            <br />
            <br />
            <button class="button" type="submit">Create Reclinathon</button>
        </form>
    </div>
</body>

<script>
function getSearchResults(s) {
    var xhReq = createXMLHttpRequest();
    xhReq.open("GET", "create_reclinathon.php?s=" + s);
    xhReq.onreadystatechange = function() {
        if (xhReq.readyState != 4) { return; }

        var myArr = JSON.parse(this.responseText);

        var dropdown = document.getElementById('moviesDropdown');
        if(!dropdown.classList.contains("show"))
            dropdown.classList.toggle("show");

        dropdown.innerHTML = "";

        if('byReference' in myArr.jsonGraph.search) {

            var movieList = getMovieList(myArr);
            var movieDatas = [];

            var count = 0;
            for(var i in movieList) {
                if(!Number.isNaN(Number(i))) {
                    var movieId = getMovieId(i, movieList);
                    var artUrl = getBoxArtUrl(movieId, myArr);
                    var runtime = getRuntime(movieId, myArr);
                    if(getMovieType(movieId, myArr) == 'movie') {
                        dropdown.innerHTML += "<div id='dropdownElement-" + i + "' class='movielist-element'>" +
                            "<img style='float:left' src='create_reclinathon.php?img=" + encodeURIComponent(artUrl) + 
                            "' height=50/>" + movieList[i].summary.value.name + "</div>";


                        movieDatas[count] = {};
                        movieDatas[count].movieId = movieId;
                        movieDatas[count].runtime = runtime;
                        movieDatas[count].movieName = movieList[i].summary.value.name;
                        movieDatas[count].json = myArr;
                        movieDatas[count].clickHandler = function(event) {
                            addSelectedMovie(this);
                            clearMovieInput();
                        };

                        count++;
                        if(count >= 6)
                            break;
                    }
                }
            }

            var movieDivs = Array.from(dropdown.childNodes);
            var i = 0;
            movieDivs.forEach(function(movieDiv) {
                if(movieDiv.id.includes("dropdownElement")) {
                    movieDiv.onclick = movieDatas[i].clickHandler.bind(movieDatas[i]);
                    i++;
                }
            });
        }
    };
    xhReq.send();
}

var selectedMovieCount = 0;
function addSelectedMovie(movieData) {
    var moviesHeader = document.getElementById("moviesHeader");
    var artUrl = getBoxArtUrl(movieData.movieId, movieData.json);
    moviesHeader.innerHTML += "<div class='movielist-element'><img style='float:left' src='create_reclinathon.php?img=" + encodeURIComponent(artUrl) + 
        "' height=50/>" + movieData.movieName + "</div>";

    var hiddenInputs = document.getElementById("hiddenInputs");

    // Fill in hidden input fields for title, runtime, image, id, url
    hiddenInputs.innerHTML += "<input type='hidden' name='movies[" + selectedMovieCount + "][title]' value='" + movieData.movieName + "' />";
    hiddenInputs.innerHTML += "<input type='hidden' name='movies[" + selectedMovieCount + "][runtime]' value='" + movieData.runtime + "' />";
    hiddenInputs.innerHTML += "<input type='hidden' name='movies[" + selectedMovieCount + "][image]' value='" + encodeURIComponent(artUrl) + "' />";
    hiddenInputs.innerHTML += "<input type='hidden' name='movies[" + selectedMovieCount + "][netflixId]' value='" + movieData.movieId + "' />";
    hiddenInputs.innerHTML += "<input type='hidden' name='movies[" + selectedMovieCount + "][netflixURL]' value='" + getMovieUrl(movieData.movieId) + "' />";

    selectedMovieCount++;
}

function clearMovieInput() {
    document.getElementById("movieInput").value = "";
}

function getMovieList(json) {
    var ref = json.jsonGraph.search.byReference;
    var refKey = Object.keys(ref)[0];
    return ref[refKey];
}

function getMovieId(i, movieList) {
    return movieList[i].summary.value.id;
}

function getMovieType(movieId, json) {
    return json.jsonGraph.videos[movieId] ? json.jsonGraph.videos[movieId].summary.value.type : "";
}

function getRuntime(i, json) {
    return json.jsonGraph.videos[i].runtime.value;
}

function getBoxArtUrl(movieId, json) {
    return json.jsonGraph.videos[movieId].boxarts["_342x192"].jpg.value.url;
}

function getMovieUrl(movieId) {
    return "https://www.netflix.com/watch/" + movieId;
}

function createXMLHttpRequest() {
	try { return new XMLHttpRequest(); } catch(e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	alert("XMLHttpRequest not supported");
	return null;
}

function populateTimezoneOffset() {
	var d = new Date();
	document.getElementById('timezoneOffset').value = d.getTimezoneOffset();
}

window.onclick = function(event) {
    if(event.target.id === "moviesDropdown")
        return;

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for(i=0; i<dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if(openDropdown.classList.contains("show")) {
            openDropdown.classList.remove("show");
        }
    }
}
</script>