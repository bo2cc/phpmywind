<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('diymodel');

//模型标识
if(!empty($m))
{
	$r = $dosql->GetOne("SELECT * FROM `#@__diymodel` WHERE `modelname`='$m'");
	if(empty($r) && !is_array($r))
	{
		echo '<script>history.go(-1);</script>';
		exit();
	}
	// 接受cid参数并判断其有效
	if ( isset($cid) && ($cid <> "") ) {
		$r_class = $dosql->GetOne("SELECT * FROM `#@__infoclass` WHERE `id`='".$cid."'");
	} else {
		$r_class = $dosql->GetOne("SELECT * FROM `#@__infoclass` WHERE `infotype`='".$r['id']."'");
		$cid = (!empty($r_class) && is_array($r_class)) ? $r_class['id'] : '0';
	}
}
else
{
	echo '<script>history.go(-1);</script>';
	exit();
}
$admintitle = "未分类信息";
if ($r_class['id']>0) $admintitle = $r_class['classname'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $r['modeltitle']; ?>管理</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/forms.func.js"></script>
</head>
<body>
<div class="topToolbar"> <span class="title"><?php echo $r['modeltitle']." / ".$admintitle; ?> </span> <a href="javascript:location.reload();" class="reload">刷新</a></div>
<?php
$fieldArr = array();

$dosql->Execute("SELECT * FROM `#@__diyfield` WHERE `infotype`=".$r['id']." LIMIT 0,2");
while($row = $dosql->GetArray())
{
	$fieldArr[$row['fieldname']] = $row['fieldtitle']; 
}
?>
<form name="form" id="form" method="post" action="modeldata_save.php">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable">
		<tr align="left" class="head">
			<td width="20" height="36" class="firstCol"><input type="checkbox" name="checkid" id="checkid" onclick="CheckAll(this.checked);"></td>
			<td>ID</td>
			<td>标题</td>
			<td>所属栏目</td>
			<?php
			foreach($fieldArr as $k=>$v)
			{
				echo '<td>'.$v.'</td>';
			}
			?>
			<td class="hidden">更新时间</td>
			<td>Flag操作</td>
			<td width="15%" class="endCol">操作</td>
		</tr>
		<?php

		$sql = "SELECT * FROM `".$r['modeltbname']."` WHERE `siteid`='$cfg_siteid' ";
		if ($cid>0) $sql .= " and classid = ".$cid;
		$dopage->GetPage($sql,30);
		while($row = $dosql->GetArray())
		{
			//获取类型名称
			$r_c = $dosql->GetOne("SELECT classname FROM `#@__infoclass` WHERE `id`=".$row['classid']);
	
			if(isset($r_c['classname']))
				$classname = $r_c['classname'].' ['.$row['classid'].']';
			else
				$classname = '<span class="red">分类已删 ['.$row['classid'].']</span>';


			switch($row['checkinfo'])
			{
				case 'true':
					$checkinfo = '已审';
					break;  
				case 'false':
					$checkinfo = '未审';
					break;
				default:
					$checkinfo = '没有获取到参数';
			}
		?>
		<tr align="left" class="dataTr">
			<td height="36" class="firstCol"><input type="checkbox" name="checkid[]" id="checkid[]" value="<?php echo $row['id']; ?>" /></td>
			<td><?php echo $row['id']; ?></td>
			<td><?php echo $row['title']; ?></td>
			<td><?php echo $classname; ?></td>
			<?php
			foreach($fieldArr as $k=>$v) {
				if ($k<>"content"){
					$modelname = $r['modelname'];
					echo '<td><input name="'.$k.$row['id'].'" class="datainput" id="'.$k.$row['id'].'" iid="'.$row['id'].'" mname="'.$modelname.'" dname="'.$k.'" type="text" value="'.ReStrLen(strip_tags($row[$k]),200).'"></td>';
			}
			}
			?>
			<td class="number hidden"><?php echo GetDateTime($row['posttime']); ?></td>
			<td class="action">
				<?php
				//信息属性 infoflag
				$dosql->Execute("SELECT * FROM `#@__infoflag` ORDER BY `orderid` ASC","if");
				$infoflag = array();

				while($r_sysflags = $dosql->GetArray("if")) {

					$infoflag[] = $r_sysflags;
					$ifname = $r_sysflags['flagname'];
					$actflag = $r_sysflags['flag'];
					$ifflagtemparr = explode($actflag,$row['flag']); 
					$newflags = $row['flag'];
					$ifact = "setflag";
					if (count($ifflagtemparr)==1) {
						$ifname = "<span>".$ifname."</span>";
						$t = ",";
						if ($row['flag']=="") $t = "";
						$newflags = $row['flag'].$t.$actflag;
					}
					else if (count($ifflagtemparr)==2) {
						$ifname = "<span class='am-btn-primary'>已".$ifname."</span>";
						if ($ifflagtemparr[0]=="") {
							$newflags = substr($ifflagtemparr[1],1);
						}
						else if ($ifflagtemparr[1]=="") {
							$newflags = substr($ifflagtemparr[0],0,-1);
						}
						else {
							$newflags = $ifflagtemparr[0].substr($ifflagtemparr[1],1);
						}
					}
					else {
						$ifname = "ERR";
					}
					
					?>
					<span>
						<a href="modeldata_save.php?m=<?php echo $m; ?>&id=<?php echo $row['id']; ?>&cid=<?php echo $cid; ?>&action=<?php echo $ifact; ?>&infoflag=<?php echo $newflags; ?>" title="点击进行审核与未审操作"><?php echo $ifname; ?></a>
					</span>
					<?php
				}
				?>				
			</td>
			<td class="action endCol">
				<span><a href="modeldata_save.php?m=<?php echo $m; ?>&id=<?php echo $row['id']; ?>&cid=<?php echo $cid; ?>&action=check&checkinfo=<?php echo $row['checkinfo']; ?>" title="点击进行审核与未审操作"><?php echo $checkinfo; ?></a></span> | 
				<span><a href="modeldata_update.php?m=<?php echo $m; ?>&id=<?php echo $row['id']; ?>">修改</a></span> | 
				<span class="nb"><a href="modeldata_save.php?m=<?php echo $m; ?>&action=del2&id=<?php echo $row['id']; ?>&cid=<?php echo $cid; ?>" onclick="return ConfDel(0);">删除</a></span>
			</td>
		</tr>
		<?php
		}
		?>
	</table>
</form>
<?php
if($dosql->GetTotalRow() == 0)
{
	echo '<div class="dataEmpty">暂时没有相关的记录</div>';
}
?>
<div class="bottomToolbar"> <span class="selArea"><span>选择：</span> <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:SubUrlParam('modeldata_save.php?m=<?php echo $m; ?>&action=delall2');" onclick="return ConfDelAll(0);">删除</a></span> <a href="modeldata_add.php?m=<?php echo $m; ?>&cid=<?php echo $cid;?>" class="dataBtn">添加新信息</a> </div>
<div class="page"> <?php echo $dopage->GetList(); ?> </div>
<?php

//判断是否启用快捷工具栏
if($cfg_quicktool == 'Y')
{
?>
<div class="quickToolbar">
	<div class="qiuckWarp">
		<div class="quickArea"> <span class="selArea"><span>选择：</span> <a href="javascript:CheckAll(true);">全部</a> - <a href="javascript:CheckAll(false);">无</a> - <a href="javascript:SubUrlParam('modeldata_save.php?m=<?php echo $m; ?>&action=delall2');" onclick="return ConfDelAll(0);">删除</a></span> <a href="modeldata_add.php?m=<?php echo $m; ?>" class="dataBtn">添加新信息</a> <span class="pageSmall">
			<?php echo $dopage->GetList(); ?>
			</span></div>
		<div class="quickAreaBg"></div>
	</div>
</div>
<?php
}
?>
<script>
jQuery(function($){
    $("input.datainput").blur(function(){
        console.log($(this).attr('mname'));
        console.log($(this).attr('iid'));
        console.log($(this).attr('dname'));
        console.log($(this).attr('value'));
		$.ajax({
			url : "ajax_do.php?action=setmodeldiydata&m="+$(this).attr('mname')+"&i="+$(this).attr('iid')+"&d="+$(this).attr('dname')+"&v="+$(this).attr('value'),
			type:'get',
			dataType:'html',
			beforeSend:function(){},
			success:function(data, textStatus, xmlHttp){
				if(data != ''){
					$(".purviewList").html(data);
				}
			}
		});
    }); 
}); 
</script>

</script>
</body>
</html>