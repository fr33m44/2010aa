<?php
//test foreach
/*
$search=array(
	array(
		'field'	=>	'is_update',
		'op'	=>	'=',
		'value'	=>	0
	),
	array(
		'field'	=>	'date',
		'op'	=>	'<=',
		'value'	=>	'2010-10-1'
	)
);
$str='';
foreach($search as $s)
{
	$str.=$s['field'] . ' ' . $s['op'] .' ' .$s['value'] . ' and ';
}
$str=substr($str,0,strrpos($str, "and"));

echo $str;

//echo local_date('Y-m-d','1284825600');
//echo time();
$arr1=array(1,2,3,4);
$arr2=array(1,2,3,4);
$arr3=array(4,3,2,1);
print_r(sort($arr3));
print_r($arr3);
if($arr1 == $arr2)
{
	echo 'equal';
}
else
{
	echo 'not equal';
}
*/
echo rand(1,10);
