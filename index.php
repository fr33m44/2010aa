<?php 
	define('IN_AA',true);
	
	require_once('inc/init.php');
	require_once('inc/lib_expense.php');
	require_once('inc/lib_user.php');
	
	session_start();
	$act_arr=array('list','login','logout','search','detail','add','del','user_search','modpass','settlement','browserupdate','');
	//验证act是否非法
	isset($_GET['act'])?$_GET['act']:$_GET['act']='';
	//当前日期
	$smarty->assign('now',date('Y年m月d日'));
	if(!in_array($_GET['act'],$act_arr))
	{	
		session_auth();
		$smarty->assign('msg',$_LANG['invalid_operation']);
		$smarty->assign('load_file','lib/msg.lbi');
	}
	else
	{	//act=list,list为主页默认act
		if($_GET['act'] == 'list' || $_GET['act'] == '')
		{
			session_auth();
			$p=!isset($_GET['p'])?1:intval($_GET['p']);
			if($p<1)
			{
				$p=1;
			}
			$uri=$_SERVER["REQUEST_URI"];
			$uri=explode('&',$uri);
			unset($uri[0]);
			$sort=isset($_GET['sort'])?$sort:'';
			$order=isset($_GET['order'])?$order:'';
			
			$a=array();
			foreach($uri as $k=>$u)
			{	
				$a[$k]=explode('=',$u);
				if($a[$k][0] == 'p')
				{
					unset($a[$k]);
				}
			}
			
			$list=get_expense_list($a,$sort,$order,$p,$_CFG['list_count']);
			$rowCount=$list[0]['count'];
			$rowCount%$_CFG['list_count']>0?$pages=floor($rowCount/$_CFG['list_count'])+1:$pages=floor($rowCount/$_CFG['list_count']);
			if($p>$pages)
			{
				$p=$pages;
			}
			
			$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['expense_list']);
			$smarty->assign('row_count',$rowCount);
			$smarty->assign('expense_list',$list);
			$smarty->assign('pages',$pages);
			$smarty->assign('p',$p);
			$smarty->assign('load_file','lib/list.lbi');
		}
		//act=detail
		elseif($_GET['act'] == 'login')
		{
			if($_SERVER['REQUEST_METHOD'] == 'GET')
			{	
				$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['sys_login']);
				$smarty->display('login.dwt');
				exit;
			}
			if($_POST['act']=='login')
			{
				$user_name=trim($_POST['username']);
				$pass_word=trim($_POST['password']);
				if($userinfo=check_login($user_name,$pass_word))
				{
					$_SESSION['uid']=$userinfo['id'];
					$_SESSION['u']=$userinfo['user_name'];
					$_SESSION['p']=$userinfo['pass_word'];
					
					header('location:?act=list');
				}
				else
				{	
					$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['sys_login']);
					$smarty->assign('login_fail',$_LANG['login_fail']);
					$smarty->display('login.dwt');
					exit;
				}
			}
		}
		elseif($_GET['act'] == 'logout')
		{
			unset($_SESSION['uid']);
			unset($_SESSION['u']);
			unset($_SESSION['p']);
			$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['sys_logout']);
			$smarty->display('login.dwt');
			exit;
			
		}
		elseif($_GET['act'] == 'detail')
		{
			session_auth();
			if($_SERVER['REQUEST_METHOD'] == 'GET')
			{	
				isset($_GET['id'])?$id=abs($_GET['id']):$id=1;
				$id=intval($id);
				$expense=get_expense_detail($id);
				$users=get_user_list();
				$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['expense_detail']);
				$smarty->assign('expense',$expense);
				$smarty->assign('users',$users);
				$smarty->assign('id',$id);
				$smarty->assign('load_file','lib/detail.lbi');
			}
			else
			{	//保存expense
				!empty($_POST['id'])?$id=intval($_POST['id']):$id=-1;
				$expense=array(
					'name'			=>	$_POST['name'],
					'fee'			=>	$_POST['fee'],
					'user_id'		=>	$_POST['user_id'],
					'user_group'	=>	$_POST['user_group'],
					'is_close'		=>	$_POST['is_close'],
					'is_public'		=>	$_POST['is_public'],
					'comment'		=>	$_POST['comment'],
					'date'			=>	$_POST['date'],
					'last_update'	=>	time()
				);
				if($expense['is_public'] == 1)
				{
					$expense['user_group']=implode(';',$expense['user_group']);
				}
				else
				{
					$expense['user_id']=$_SESSION['uid'];
					$expense['is_close']=1;
				}
				save_expense($id,$expense);
				$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['save_success']);
				sys_msg('保存成功！',0,array(array('href'=>"?act=detail&id=$id",'text'=>'返回上一页'),array('href'=>'?act=list','text'=>'返回费用列表')),false);
			}
			
		}
		elseif($_GET['act'] == 'user_search')
		{	
			$json=new JSON();
			$name=$_GET['name'];
			echo $json->encode(user_search_ajax($name));
			exit;
		}
		elseif($_GET['act'] == 'add')
		{	
			session_auth();
			$users=get_user_list();
			$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['expense_add']);
			$smarty->assign('users',$users);
			$smarty->assign('id',-1);
			$smarty->assign('load_file','lib/detail.lbi');
		}
		elseif($_GET['act'] == 'del')
		{	
			session_auth();
			del_expense(intval($_GET['id']));
			exit;
		}
		elseif($_GET['act'] == 'settlement')
		{
			session_auth();
			if($_SERVER['REQUEST_METHOD'] == 'GET')
			{//展示页面
				$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['expense_settlement']);
				$smarty->assign('users',get_user_list());
				$smarty->assign('load_file','lib/settlement.lbi');
			}
			elseif(!isset($_POST['do']))
			{//显示报告
				$json=new JSON();
				$user_group=explode(',',$_POST['user_group']);
				sort($user_group);
				$report_info=settlement($user_group,0);
				$smarty->assign('report_info',$report_info);
				echo $smarty->fetch('lib/report.lbi');
				exit;
			}
			else
			{//实现结算
			    echo 'shit';
			    exit;
			}
		}
		elseif($_GET['act'] == 'modpass')
		{
			session_auth();
			if($_SERVER['REQUEST_METHOD'] == 'GET')
			{
				$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['mod_pass']);
				$smarty->assign('load_file','lib/pass.lbi');
			}
			else
			{
				$newpass=$_POST['newpass'];
				$oldpass=$_POST['oldpass'];
				switch(mod_pass($_SESSION['uid'],$oldpass,$newpass))
				{
					case(0):
						{
							$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['mod_pass_failed']);
							sys_msg('密码修改失败！');
							break;
						}
					case(1):
						{
							$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['mod_pass_success']);
							sys_msg('密码修改成功！下次登录请使用新密码！');
							break;
						}
					case(2):
						{
							$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['old_pass_wrong']);
							sys_msg($_LANG['old_pass_wrong']);
							break;
						}
				}
			}
		}
		elseif($_GET['act'] == 'browserupdate')
		{	
			$smarty->assign('page_title',$_CFG['sys_name'].' '.$_LANG['browser_update']);
			$smarty->display('browserupdate.dwt');
			exit;
		}
	}
	$smarty->display('index.dwt');

?>