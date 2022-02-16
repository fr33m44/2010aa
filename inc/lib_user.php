<?php
function get_username($uid)
{
	$sql="select user_name from ".$GLOBALS['aa']->table('user').' where is_delete=0 and id='.$uid;
	$row=$GLOBALS['db']->getOne($sql);
	return $row;
}
function get_users($limit=-1)
{
	if($limit == -1)
	{
		$sql="select id,user_name from ".$GLOBALS['aa']->table('user');
	}
	else
	{
		$sql="select id,user_name from ".$GLOBALS['aa']->table('user') . " limit ".$limit;
	}
	$row=$GLOBALS['db']->getAll($sql);
	return $row;
}
function get_user_list()
{
	$sql="select * from ".$GLOBALS['aa']->table('user');
	$row=$GLOBALS['db']->getAll($sql);
	return $row;
}
/*
 * 登录验证
 * @return mixed
 */
function check_login($u,$p)
{
	$sql="select * from ".$GLOBALS['aa']->table('user')." where user_name='$u' and is_delete=0";
	if(!$row=$GLOBALS['db']->getRow($sql))
	{
		return false;
	}
	else
	{
		if($row['pass_word'] !=  md5($p))
		{
			return false;
		}
		else
		{
			return $row;
		}
	}
}
/*
 * 验证权限
 */
function session_auth()
{
	if(!isset($_SESSION['uid']))
	{
		header('location:?act=login');
	}
}
function mod_pass($uid,$old_pass,$new_pass)
{	
	//判断旧密码正误
	$sql="select * from ".$GLOBALS['aa']->table('user')." where id=$uid and pass_word='".md5($old_pass)."'";
	if(!$GLOBALS['db']->getRow($sql))
	{
		return 2;
	}
	else
	{
		$user['pass_word']=md5($new_pass);
		if(	$GLOBALS['db']->autoExecute($GLOBALS['aa']->table('user'),$user,'UPDATE',"id=$uid"))
		{	
			return 1;
		}
		else
		{
			return 0;
		}
	}
}
?>