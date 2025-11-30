<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="generator" content="HTML Tidy for HTML5 for Linux version 5.6.0" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="keywords" lang="en" content="Greek, language, inflect, inflection, conjugate, conjugation, decline, declension, Catholic, Catholicism" xml:lang="en" />
  <meta name="keywords" lang="la" content="Lingua, Graeca, Greca, Græca" xml:lang="la" />
  <meta name="description" lang="en" content="Inflect (conjugate or decline) words in Greek phrases." xml:lang="en" />
  <style type="text/css">
  /*<![CDATA[*/
  #box{float:left;text-align:center;margin:10px;}#lemma{font-size:150%;}select{text-align:center;background:white;}a:link{color:black;text-decoration:none}a:visited{text-decoration:none}a:hover{color:blue;text-decoration:underline}
  /*]]>*/
  </style>
  <title>Greek Inflector Output</title>
</head>
<body>
  <script type="text/javascript">
  //<![CDATA[
  function definePopup(word) {
        void window.open("http://www.perseus.tufts.edu/hopper/morph?la=greek&l="+word,"LSJ","width="+screen.width*0.75+",height="+screen.height*0.75+",screenX="+screen.width*0.125+"px,screenY="+screen.height*0.125+"px,scrollbars=1,resizable=1,toolbar=0");
  }
  //]]>
  </script>
  <h3><span id="status"></span></h3><?php
function generateStrArray($b) {
	$infl = $b->rest->entry->infl;
	if (gettype($infl) == 'array') {
		foreach ($infl as $i) {
			foreach ($i as $k => $val) {
				if (print_r($k,true) != 'term')
					$str = $str.print_r($val->{'$'}, true)." ";
			}
			$strarray[] = trim($str);
			$str = "";
		}
	} elseif (gettype($infl) == 'object') {
		foreach ($infl as $k => $val) {
			if (print_r($k,true) != 'pofs')
				$str = $str.print_r($val->{'$'}, true)." ";
		}
		$strarray[] = trim($str);
		$str = '';
	}
	return $strarray;

}
function printFloatBox($word){ 
  $word_clean = preg_replace("/[[:punct:]]/", "", $word); // remove punctuation
	$jsonString = file_get_contents("http://services.perseids.org/bsp/morphologyservice/analysis/word?lang=grc&engine=morpheusgrc&word=".$word_clean) or die('<script type="text/javascript">document.getElementById("status").innerHTML=\'<font color="red">Cannot reach Perseus server</font>\';</script>');
	$json = json_decode($jsonString);
	$str = "";
	$strarray = array();
	$Body = $json->RDF->Annotation->Body;
	if (gettype($Body) == 'object')
		$strarray = generateStrArray($Body);
	else { // $Body is an array:
		foreach ($Body as $b) {
			$strarray = array_merge($strarray,generateStrArray($b));
		}
	}
	$strarray = array_filter($strarray);
	$strarray = array_unique($strarray);
	//Print it out
	$pos_as_html = '<div id="box"><select>';
	foreach ($strarray as $elem)
		$pos_as_html = $pos_as_html."<option>".$elem."</option>";
	$pos_as_html = $pos_as_html.'</select><div id="lemma"><a href="javascript:definePopup(\''.$word_clean.'\')">'.$word."</a></div></div>";
	echo $pos_as_html;
}

if ($_POST['thetext']!="") {
	echo '<script type="text/javascript">document.getElementById("status").innerHTML=\'<font color="blue">Processing...</font>\';</script>';
	flush(); ob_flush();
	$wordsarray = preg_split("/\s+/",$_POST['thetext']);
	foreach ($wordsarray as $word) {
		printFloatBox($word);
	}
	echo '<script type="text/javascript">document.getElementById("status").innerHTML=\'\';</script>';
} else
	die('<script type="text/javascript">document.getElementById("status").innerHTML=\'<font color="red">No Greek phrase entered</font>\';parent.document.getElementById(\'thetext\').focus();</script>');
              ?>
</body>
</html>
