<?php
	if (!defined('IN_AA'))
	{
		die('ERROR NO.你懂的！');
	}
	/* 	定义路径 	*/
	define('ROOT_PATH',str_replace('inc'.DIRECTORY_SEPARATOR.'init.php','',__FILE__));
	define('SMARTY_DIR',ROOT_PATH.'smarty'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR);
	/*	安装检测	*/
	if (!file_exists(ROOT_PATH . 'data/install.lock') && !defined('NO_CHECK_INSTALL'))
	{
	    header("Location: ./install/index.php\n");
	
	    exit;
	}
	
	/* 初始化设置 */
	@ini_set('memory_limit',          '64M');
	@ini_set('session.cache_expire',  180);
	@ini_set('session.use_trans_sid', 0);
	@ini_set('session.use_cookies',   1);
	@ini_set('session.auto_start',    0);
	@ini_set('display_errors',        1);
	
	/*	设定include_path	*/
	if (DIRECTORY_SEPARATOR == '\\')
	{
	    @ini_set('include_path', '.;' . ROOT_PATH . ';' . SMARTY_DIR);
	}
	else
	{
	    @ini_set('include_path', '.:' . ROOT_PATH . ';' . SMARTY_DIR);
	}
	/*	载入数据库配置文件	*/
	require(ROOT_PATH . 'data/config.php');
	/*	调试模式	*/
	if (defined('DEBUG_MODE') == false)
	{
	    define('DEBUG_MODE', 0);
	}
	/*	require_once库文件	*/
	require(ROOT_PATH . 'inc/inc_constant.php');
	require(ROOT_PATH . 'inc/cls_aa.php');
	require(ROOT_PATH . 'inc/cls_json.php');
	require(ROOT_PATH . 'inc/lib_time.php');
	require(ROOT_PATH . 'inc/lib_base.php');
	require(ROOT_PATH . 'inc/lib_common.php');
	
	/* 对用户传入的变量进行转义操作。*/
	if (!get_magic_quotes_gpc())
	{
	    if (!empty($_GET))
	    {
	        $_GET  = addslashes_deep($_GET);
	    }
	    if (!empty($_POST))
	    {
	        $_POST = addslashes_deep($_POST);
	    }
	
	    $_COOKIE   = addslashes_deep($_COOKIE);
	    $_REQUEST  = addslashes_deep($_REQUEST);
	}
	/* 创建 ECSHOP 对象 */
	$aa = new AA($db_name, $prefix);
	/* 初始化数据库类 */
	require(ROOT_PATH . 'inc/cls_mysql.php');
	$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
	
	/* 	载入系统参数 		*/
	$_CFG = load_config();
	/* 载入语言文件 */
	require(ROOT_PATH . 'lang/' . $_CFG['sys_lang'] . '/common.php');
	
	if ($_CFG['is_close'] == 1)
	{
	    /* 商店关闭了，输出关闭的消息 */
	    header('Content-type: text/html; charset='.AA_CHARSET);
	
	    die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $_LANG['sys_closed'] . '</p><p>' . $_CFG['close_comment'] . '</p></div>');
	}
	/*	载入smarty模板引擎	*/
	require(SMARTY_DIR . 'Smarty.class.php');
    $smarty = new Smarty();

    $smarty->cache_lifetime = $_CFG['cache_life_time'];
    $smarty->template_dir   = ROOT_PATH . 'themes/' . $_CFG['template'] ;
    $smarty->template_rel_dir   = 'themes/' . $_CFG['template'] . '/';
    $smarty->cache_dir      = ROOT_PATH . 'temp/caches';
    $smarty->compile_dir    = ROOT_PATH . 'temp/compiled';

    if ((DEBUG_MODE & 2) == 2)
    {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }
    else
    {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }

    $smarty->assign('lang', $_LANG);
    $smarty->assign('aa_charset', AA_CHARSET);
  	$smarty->assign('aa_css_path', 'themes/' . $_CFG['template'] . '/style.css');
  	/*会员信息*/
  	
  	
  	
  	require(ROOT_PATH . 'inc/lib_main.php');
  	
  	
  	/* 判断是否支持 Gzip 模式 */
	if (!defined('INIT_NO_SMARTY') && gzip_enabled())
	{
	    ob_start('ob_gzhandler');
	}
	else
	{
	    ob_start();
	}