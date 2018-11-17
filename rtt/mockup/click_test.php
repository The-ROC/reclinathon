<html>

<head>
<title>Click Test</title>
</head>

<body>
<div id="container" onclick="containerClick(event)">Container div</div>
</body>

<script>
var CLICK_TEST = {};
CLICK_TEST.counter = 0;

function containerClick(event) {
    if(event.target.id.includes("element-"))
        return;

    containerDiv = document.getElementById("container");
    containerDiv.innerHTML += "<div id='element-" + CLICK_TEST.counter + "'> Element div " + CLICK_TEST.counter + "</div>";
    var elementDivs = Array.from(containerDiv.childNodes);
    elementDivs.forEach(function(elementDiv) {
        elementDiv.onclick = function(e) {
            console.log("Clicked element: " + e.target.id);
        }
    });
    CLICK_TEST.counter++;
}
</script>

</html>