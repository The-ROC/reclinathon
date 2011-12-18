<?php
set_include_path("/kunden/homepages/6/d95429370/htdocs/reclinathon");
require_once("config.php");
?>

<html>

<head>
<link rel="stylesheet" type="text/css" href=<?php echo BASE_URL . "css/index_new.css"?> />
</head>

<?php
  require_once "config.php";

  function showLinks($leftArray, $rightArray) {
    $result = "<table width='100%' bgcolor=#222222><tr>";

    $result .= "<td align='left'><table><tr>";
    $count = 0;
    foreach($leftArray as $link) {
      if($count > 0) {
        $result .= "<td>" . showPipe() . "</td>";
      }
      $result .= "<td>" . showLink($link['name'], $link['url']) . "</td>";
      $count++;
    }
    $result .= "</tr></table></td>";

    $result .= "<td align='right'><table><tr>";
    $count = 0;
    foreach($rightArray as $link) {
      if($count > 0) {
        $result .= "<td>" . showPipe() . "</td>";
      }
      $result .= "<td>" . showLink($link['name'], $link['url']) . "</td>";
      $count++;
    }
    $result .= "</tr></table></td>";

    $result .= "</tr></table>";

    return $result;
  }

  function showLink($name, $url) {
    global $currentPage;
    $result = "";
    if(strcmp($currentPage, $name) == 0) {
      $result .= "<font color=#888888>" . $name . "</font>";
    } else {
      $result .= "<a target='_top' href='" . $url . "'>" . $name . "</a>";
    }
    return $result;
  }

  function showPipe() {
    return "<font color=#888888>|</font>";
  }
?>

<body class="noborder">
<div class="nav">
  <?php 
  $leftAligned = array(array("name"=>"home", "url"=>BASE_URL . "index.php"),
		       array("name"=>"about", "url"=>BASE_URL . "about.php"),
		       array("name"=>"register", "url"=>BASE_URL . "register.php"),
		       array("name"=>"tracking", "url"=>BASE_URL . "tracking_wrapper.php"));
  $rightAligned = array(array("name"=>"login", "url"=>BASE_URL . "login.php"));
  
  echo showLinks($leftAligned, $rightAligned);
  ?>
</div>

<div class="divider"></div>
</body>
</html>
