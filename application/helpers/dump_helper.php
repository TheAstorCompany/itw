<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('Dump')){
    
    function PostGet($variable){
	if(isset($_GET[$variable])){
            return $_GET[$variable];
	}else if(isset($_POST[$variable])){
            return $_POST[$variable];
	}else{
            return null;
	}
    }

    function Dump($oObject, $bDump = false, $sIPCondition = "", $bXmlError = false){
	
	if($sIPCondition != "" && $sIPCondition != $_SERVER['REMOTE_ADDR'])
		return;
	
	
	if($bXmlError){
		echo '<error><url>'. htmlentities($_SERVER['QUERY_STRING']) .'</url><message><![CDATA[';
	}elseif(true || PostGet('XmlHttpRequest') != null){
		$arBT = debug_backtrace();
		echo "
================================================================
". $arBT[0]['file'] .":". $arBT[0]['line'] ."



";
		//echo '<DUMP>'."\n";
	}else{
		echo '<pre id="dump" style="font-family:Tahoma;font-size:11px;color:#000077;font-weight:normal; text-transform: none; text-align: left;" contentEditable="true"><span style="color:#FF0000; font-weight:bold">&lt;Dump&gt;</span>'."\n";
	}
	
	if($bDump){
		var_dump($oObject);
	}else{
		print_r($oObject);
	}
	
	
	if($bXmlError){
		echo ']]></message></error>';
	}elseif(true || PostGet('XmlHttpRequest') != null){
		echo "




================================================================
";
		//echo "\n".'</DUMP>';
	}else{
		echo '<span style="color:#FF0000; font-weight:bold">&lt;/Dump&gt;</span>';
		echo '</pre>';
	}
	
	#print_r($oObject);
    }
}

