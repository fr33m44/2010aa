<?php
/*
 * 获得消费记录列表
 * @author tunpishuang
 * @access public
 * @param $search 	where限定 array(array(field),array(op),array(value))
 * @param $sort		排序字段
 * @parame $order	asc/desc
 * @return array
 */
function get_expense_list($search='',$sort='',$order='',$page=1,$list_count)
{
	$sql="SELECT e.*,u.user_name as user_name from  " . $GLOBALS['aa']->table('expense') . ' as e inner join ' .$GLOBALS['aa']->table('user'). " as u on u.id=e.user_id and e.is_delete='0' ";
	if(empty($search))
	{
		$str=" order by id desc";
	}
	else 
	{
		if(empty($sort))
		{	
			$str= " where ";
			foreach($search as $s)
			{
				$str.=$s[0] . ' = ' .$s[1] . ' and ';
			}
			$str=substr($str,0,strrpos($str, "and"));
			$str.=' order by id desc';
		}
		else
		{	
			if(empty($order))
			{
				$str= " order by ". $sort . " asc ";
			}
			else
			{
				$str= " order by ". $sort . " " .$order;	
			}
		}
	}
	$sql.=$str;
	$row=$GLOBALS['db']->getAll($sql);
	$rowCount=$GLOBALS['db']->affected_rows($sql);
	$sql.=' limit '.($page-1)*$list_count.",".$list_count;
	$row=$GLOBALS['db']->getAll($sql);
	foreach($row as $k=>$v)
	{
		$row[$k]['count']=$rowCount;
	}
	return $row;
}

/*
 * 获得消费记录详情
 * @author tunpishuang
 * @param $id 费用id
 * @access public
 * @return array
 */
function get_expense_detail($id)
{
	$sql="SELECT * FROM ". $GLOBALS['aa']->table('expense')."where id=".$id;
	$row=$GLOBALS['db']->getRow($sql);
	if($row['is_public'] == 1)
	{
		$user_group_arr_id=explode(';',$row['user_group']);
		$row['user_group']=array();
		foreach($user_group_arr_id as $k=>$v)
		{
			require_once 'lib_user.php';
			$row['user_group'][$v]=get_username($v);
		}
	}
	return $row;
}
/*
 * 保存/添加 消费记录
 * @author tunpishuang
 * @param $id 费用id
 * @param $expense 费用数据数组
 * @access public
 * @return boolean
 */
function save_expense($id = -1,$expense)
{	
	if($id == -1)
	{	
		$mode='INSERT';
		$GLOBALS['db']->autoExecute($GLOBALS['aa']->table('expense'),$expense,$mode);
	}
	else
	{
		$mode='UPDATE';
		$where='id='.$id;
		$GLOBALS['db']->autoExecute($GLOBALS['aa']->table('expense'),$expense,$mode,$where);
	}
}
/*
 * 删除指定id的expense
 */
function del_expense($id)
{
	$expense['is_delete']=1;
	$GLOBALS['db']->autoExecute($GLOBALS['aa']->table('expense'),$expense,'UPDATE',"id=$id");
}
/*
 * 结算
 * @param $settlement 1:结算（set is_close=1）, 0:不结算,只显示报告
 */
function settlement($user_group,$settlement)
{
	if($settlement ==0)
	{
		$sql="select * from ".$GLOBALS['aa']->table('expense')." where is_close=0";
		$rows=$GLOBALS['db']->getAll($sql);
		$rowArr=array();
		$totalFee=0;
		$userFee=array();
		foreach($user_group as $u)
		{
			$userFee[$u]=0;
		}
		foreach($rows as $row)
		{	
			$rowArr[$row['id']]=explode(';',$row['user_group']);
			sort($rowArr[$row['id']]);
			//user_group判断
			if($rowArr[$row['id']] == $user_group)
			{	
				//总额
				$totalFee+=$row['fee'];
				//每个人的支付
				foreach($user_group as $u)
				{
					$sql2=$sql." and id=".$row['id'];
					$row2=$GLOBALS['db']->getRow($sql2);
					if($row2['user_id'] == $u)
					{
						$userFee[$row2['user_id']]+=$row2['fee'];
					}
					
				}
			}
		}
		//公费人数
		$userCount=count($user_group);
		//ratio总和
		$ratioSum=0;
		foreach($user_group as $u)
		{
		    $ratioSum+=get_ratio($u);
		}
		//平均费用
		$avgFee=$totalFee/$ratioSum;
		foreach($userFee as $k=>$uf)
		{	
			$temp[$k]=$userFee[$k];
			$userFee[$k]=array();
			$userFee[$k]['money_paied']=$temp[$k];
			$userFee[$k]['money_balance']=$temp[$k]-($avgFee*get_ratio($k));
			$userFee[$k]['ratio']=get_ratio($k);
		}
		//获得user_name
		foreach($userFee as $k=>$uf)
		{
			$userFee[get_username($k)]=$userFee[$k];
		}
		foreach($userFee as $k=>$uf)
		{
			if(is_numeric($k))
			{
				unset($userFee[$k]);
			}
		}
		$result=array('totalFee'=>$totalFee,'userCount'=>$userCount,'avgFee'=>$avgFee,'userFee'=>$userFee);
		return $result;
	}
	if($settlement == 1)
	{
		//update is_close=1;
	}
	
}
/*
 * //获取支付比例（aka. 自然人）
 * @return $ratio
 */
function get_ratio($user_id)
{
    $sql="select ratio from ".$GLOBALS['aa']->table('user')." where id = $user_id";
    return $ratio=$GLOBALS['db']->getOne($sql);
}