<?php
// check if they've been here, if they haven't set
// a cookie for subsequent visits

if($_COOKIE['FreeReport']==null) { 
    setcookie("FreeReport", 1);
	header('Location: http://www.scratchsmarter.com/scraper/weekly_pdf/download.php?state='.$_GET["state"].'&free=1');
	}
 elseif($_COOKIE['FreeReport']<3) {
	setcookie("FreeReport", $_COOKIE['FreeReport']+1);
    header('Location: http://scratchsmarter.com/promo/thank-interest-best-games-play-free-report');
	 
}
else {
    header('Location: http://scratchsmarter.com/promo/free-report-states-list/');
}
?>