<head>
<title>Reclinathon Home</title>
<link rel="stylesheet" type="text/css" href="css/index_new.css" />
<script type="text/javascript"> <!--

var enabled = false;

function turnOnSign() {
  var sign = document.getElementById("sign");
  sign.src = "images/sign_on.png";
}

function turnOffSign() {
  var sign = document.getElementById("sign");
  sign.src = "images/sign_off.png";
}

function showCta() {
  var cta = document.getElementById("cta");
  cta.style.display = "block";
}

function hideCta() {
  var cta = document.getElementById("cta");
  cta.style.display = "none";
}

function init() {
  turnOffSign();
  hideCta();
}

function enable() {
  turnOnSign();
  showCta();
  enabled = true;
}

function disable() {
  turnOffSign();
  enabled = false;
}

function toggle() {
  if(enabled) {
    disable();
  } else {
    enable();
  }
}

function hint(element) {
  element.style.cursor = "pointer";
}

function unhint(element) {
  element.style.cursor = "default";
}

function preloadImage(url, width, height) {
  image = new Image(width, height);
  image.src = url;
}

preloadImage("images/arena_photo.png", 604, 345);
preloadImage("images/election_day.png", 199, 124);
preloadImage("images/sign_off.png", 761, 105);
preloadImage("images/sign_on.png", 761, 105);
preloadImage("images/teaser_quote.png", 668, 29);
preloadImage("images/teaser_cta.png", 96, 26);
preloadImage("images/teaser_cta_over.png", 96, 34);

--></script>
</head>

<body class="noborder">

<?php include("header.php"); ?>

<div class="main">
<br />
<center>
<div width="80px" onClick="toggle()" onMouseOver="hint(this);" onMouseOut="unhint(this);">
  <img id="sign" src="images/sign_off.png" /><br />
  <img src="images/arena_photo.png" /><br /><br />
  <img id="quote"  src="images/teaser_quote.png" /><br /><br />
</div>
  <img id="cta" onMouseOver="this.style.cursor='pointer'; this.src='images/teaser_cta_over.png';" onMouseOut="this.style.cursor='default'; this.src='images/teaser_cta.png';" onClick="window.location='register.php'" src="images/teaser_cta.png" /><br />
</center>
</div>

<script type="text/javascript"> <!--

init();

--></script>

</body>
</html>
