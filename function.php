<?php
include "simple_html_dom.php";
function scrapWebsite($url) {	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	curl_close($ch);
	$html = new simple_html_dom();
	$html->load($response);
	return $html;	
}

function getPostDetails ($html) {
	$titles = array();
	$i = 0 ;
	foreach($html->find('h2[class=entry-title] a') as $post) {		
		$titles[$i]['title'] = $post->plaintext;  
		$titles[$i]['link'] = $post->href;			
		$i++;
	}
	$i = 0 ;
	foreach($html->find('div[class=entry-content] img') as $img) {		
		$titles[$i]['img'] = $img->src;  			
		$i++;
	}
	return $titles;	
}	

function writeToCSV ($postDetail) {
	ob_clean();
	ob_start();
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="output.csv";');
	$f = fopen('php://memory', 'w'); 
	fputcsv($f, array('Title', 'Link', 'image')); 
	foreach ($postDetail as $key => $value){	
		fputcsv($f, $value);
	}
	fseek($f, 0);		
	fpassthru($f);
	fclose($f);
	exit();
}

function getLinks($html) {
	$links = array();
	foreach($html->find('a[href^=]') as $link) {
		if (strpos($link->href, "webcache.google") === false) {			
			$links[] = $link->href;  
		}
	}
	return $links;	
}	
?>


