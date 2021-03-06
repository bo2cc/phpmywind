<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('getmode'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>货到方式管理</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
</head>
<body>
<div class="topToolbar"> <span class="title">货到方式管理</span> <a href="javascript:location.reload();" class="reload">刷新</a></div>
<form name="form" id="form" method="post" action="getmode_save.php?action=save">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
		<tr align="left" class="head">
			<td width="5%" height="36" class="firstCol"><input type="checkbox" name="checkid" id="checkid" onclick="CheckAll(this.checked);"></td>
			<td width="5%">ID</td>
			<td width="40%">货到方式</td>
			<td width="30%" align="center">排序</td>
			<td width="20%" class="endCol">操作</td>
		</tr>
		<?php

		$dosql->Execute("SELECT * FROM `#@__getmode` ORDER BY `orderid` ASC");
		if($dosql->GetTotalRow() > 0)
		{
			while($row = $dosql->GetArray())
			{
				switch($row['checkinfo'])
				{
					case 'true':
						$checkinfo = '显示';
						break;  
					case 'false':
						$checkinfo = '隐藏';
						break;
					default:
						$checkinfo = '没有获取到参数';
				}
		?>
		<tr align="left" class="dataTr">
			<td height="36" class="firstCol"><input type="checkbox" name="checkid[]" id="checkid[]" value="<?php echo $row['id']; ?>" /></td>
			<td><?php echo $row['id']; ?>
				<input type="hidden" name="id[]" id="id[]" value="<?php echo $row['id']; ?>" /></td>
			<td align="left"><input type="text" name="classname[]" id="classname[]" class="inputd" value="<?php echo $row['classname']; ?>" /></td>
			<td align="center"><a href="getmode_save.php?action=up&id=<?php echo $row['id']; ?>&orderid=<?php echo $row['orderid']; ?>" class="leftArrow" title="提升排序"></a>
				<input type="text" name="orderid[]" id="orderid[]" class="inputls" value="<?php echo $row['orderid']; ?>" />
				<a href="getmode_save.php?action=down&id=<?php echo $row['id']; ?>&orderid=<?php echo $row['orderid']; ?>" class="rightArrow" title="下降排序"></a></td>
			<td class="action endCol"><span><a href="getmode_save.php?action=check&id=<?php echo $row['id']; ?>&checkinfo=<?php echo $row['checkinfo']; ?>"><?php echo $checkinfo; ?></a></span> | <span class="nb"><a href="getmode_save.php?action=del2&id=<?php echo $row['id'] ?>" onclick="return ConfDel(0);">删除</a></span></td>
		</tr>
		<?php
			}
		}
		else
		{
		?>
		<tr align="center">
			<td colspan="5" class="dataEmpty">暂时没有相关的记录</td>
		</tr>
		<?php
		}
		?>
		<tr align="center">
			<td height="36" colspan="5"><strong>新增一个方式</strong></td>
		</tr>
		<tr align="left" class="dataTrOn">
			<td height="36">&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type="text" name="classnameadd" id="classnameadd" class="input" /></td>
			<td align="center"><input type="text" name="orderidadd" id="orderidadd" class="inputls" value="<?php echo GetOrderID('#@__getmode'); ?>" /></td>
			<td class="endCol"><input type="radio" name="checkinfoadd" value="true" checked="checked"  />
				显示&nbsp;
				<input type="radio" name="checkinfoadd" value="false" />
				隐藏</td>
		</tr>
	</table>
</form>
<div class="bottomToolbar"><span class="selArea"><span>选择：</span> <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:DelAllNone('getmode_save.php');" onclick="return ConfDelAll(0);">删除</a>　<span>操作：</span><a href="javascript:UpOrderID('getmode_save.php');">更新排序</a></span> <a href="#" onclick="form.submit();" class="dataBtn">更新全部</a> </div>
<div class="page">
	<div class="pageText">共有<span><?php echo $dosql->GetTableRow('#@__getmode'); ?></span>条记录</div>
</div>
<?php

//判断是否启用快捷工具栏
if($cfg_quicktool == 'Y')
{
?>
<div class="quickToolbar">
	<div class="qiuckWarp">
		<div class="quickArea"><span class="selArea"><span>选择：</span> <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:DelAllNone('getmode_save.php');" onclick="return ConfDelAll(0);">删除</a>　<span>操作：</span><a href="javascript:UpOrderID('getmode_save.php');">更新排序</a></span> <a href="#" onclick="form.submit();" class="dataBtn">更新全部</a><span class="pageSmall">
			<div class="pageText">共有<span><?php echo $dosql->GetTableRow('#@__getmode'); ?></span>条记录</div>
			</span></div>
		<div class="quickAreaBg"></div>
	</div>
</div>
<?php
}
?>
</body>
</html>