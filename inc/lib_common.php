<?php
/**
 * 载入配置信息
 *
 * @access  public
 * @return  array
 */
function load_config()
{
    	$arr = array();
        $sql = 'SELECT `code`,`type`,`option`,`value`,`sort_order` FROM ' . $GLOBALS['aa']->table('config');
        $res = $GLOBALS['db']->getAll($sql);
    	foreach($res as $key=>$cfg)
    	{
    		$code=$cfg['code'];
    		$arr[$code]=$cfg['value'];
    	}
    	return $arr;
    	 
}
function debug($var)
{
	echo '<!DOCTYPE html  
			<meta http-equiv="Content-Type"
    		content="text/html; charset=utf-8" />
	<head></head><body><pre>';
	print_r($var);
	echo '</pre></body></html>';
	exit;
}