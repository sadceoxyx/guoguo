<?php

$buyurl=base64_decode($_GET['buyurl']); /* Get the "fake" url passed from the affiliate link and decode from base64*/
$buy=$_GET['buy'];
$cheap=$_GET['cheap'];
$buycheap=array($buy, $cheap); /* Get the two (in this example) parameters indicating which dummy terms to replace */
$newterms=array('rover', 'ebay'); /* Define which terms are to be replaced in the dummy affiliate link with the terms in $buycheap */
$newbuyurl=str_replace($buycheap, $newterms, $buyurl); /* do the replacement */
header("HTTP/1.1 301 Moved Permanently");
header("Location: $newbuyurl");   /* Redirect browser to the new address */
exit;

?>