/*表格奇偶颜色变换、hover*/
function tableColor(tbl)
{
	for(var i=0;i<tbl.tBodies[0].rows.length;i++)
	{	
		tbl.tBodies[0].rows[i].onmouseover=function(){
			this.style.backgroundColor='#ccffcc';
		};
		if(i%2==0)
		{
			tbl.tBodies[0].rows[i].style.backgroundColor='#ffffff';
			tbl.tBodies[0].rows[i].onmouseover=function(){
				this.style.backgroundColor='#ccffcc';
			};
			tbl.tBodies[0].rows[i].onmouseout=function(){
				this.style.backgroundColor='#ffffff';
			};
		}
		else
		{
			tbl.tBodies[0].rows[i].style.backgroundColor='#f5f5f5';
			tbl.tBodies[0].rows[i].onmouseover=function(){
				this.style.backgroundColor='#ccffcc';
			};
			tbl.tBodies[0].rows[i].onmouseout=function(){
				this.style.backgroundColor='#f5f5f5';
			};
		}
	}
	
}
function trim(text)
{
  if (typeof(text) == "string")
  {
    return text.replace(/^\s*|\s*$/g, "");
  }
  else
  {
    return text;
  }
}
/*
 * 通过ajax删除指定id的expense
 */
function del(id)
{
	if(confirm("确定删除它？"))
	{
		Ajax.call('?act=del','id='+id,delResponse,'GET','TEXT');
		var tr=document.getElementById("tr"+id);
		tr.parentNode.removeChild(tr);
	}
}
function delResponse(result)
{
}
/*
 * 左边导航的mouseover效果
 */
