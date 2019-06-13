<?php
 include_once "header.php";

if ($_GET['edit_id'] ) {


	$id_array = preg_split('/_/',$_GET['edit_id'] ) ;
	$id = $id_array[1]  ;


	if ( $id >0 ) {
  		$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where id='$id' " ;
 		$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
 		$row=$xoopsDB->fetchArray($result) ;

		//取得登記資料
		$sql = " select *  from " . $xoopsDB->prefix("mac_input") . "  where mac ='{$row['mac']}'  or ip='{$row['ip']}' "  ;
 		$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
 		while($row2=$xoopsDB->fetchArray($result)){
   			$input_data .= $row2['user']  .'-' . $row2['place'] ;
        $input_data .= $row2['c_id'] ?' @' . $row2['c_id']  :'' ;
        $input_data .= $row2['s_id'] ?' +' . $row2['s_id']  :'' ;
  		}

     		echo "
     		<form method='post' name='editForm' id='editForm_{$row['id']}' action='comp_submit'  >
     		<div class='span2'>
     			office:<input class='span8' name='txt_kind' type='text' id='txt_kind' value='{$row['kind']}'  placeholder='單位' title='單位' /><br/>
			phon:<input  class='span8' name='txt_phid' type='text' id='txt_phid' value='{$row['phid']}'  placeholder='分機'  title='分機號'  />
		</div>
		<div class='span3'>
			ip:<input name='txt_ip' type='text' id='txt_ip' value='{$row['ip']}'  placeholder='ipv4' /><br />
			{$row['mac']}
            <br />
            財號1:<input name='txt_scmid' type='text' id='txt_scmid' value='{$row['scM_id']}'  placeholder='財產編號' />
            <br />
            財號2:<input name='txt_scmid2' type='text' id='txt_scmid' value='{$row['scM_id2']}'  placeholder='附加財產編號' />
            <br />
            型號:<input name='txt_mbaseb' type='text' id='txt_mbaseb' value='{$row['baseboard']}'  placeholder='主機型號' />
		</div>
		<div class='span3'>
  			<input class='span8' name='txt_comp' type='text' id='txt_comp' value='{$row['comp']}'  placeholder='機器名(限用英數文字)' title='機器名(限用英數文字)'/><br />
            <input  class='span8' name='txt_c_p' type='text' id='txt_c_p' value='{$row['class_place']}' placeholder='建築代號如北1-1' title='放置位置' />
			<input  class='span8' name='txt_ps' type='text' id='txt_ps' value='{$row['ps']}' placeholder='說明(無 則視為未登記)' title='字串#號後文字不會出現在前台，可做密碼等記錄' />
		</div>
		<div class='span3'>

			<input name='now_id' type='hidden' value='{$row['id']}' />
			<input name='ord_id' type='hidden' value='{$_GET['edit_id_ord']}' />
			<span class='ed'>save</span><br />
			<div  class='alert alert-error'>$input_data </div>
		</div>
		</form>
		" ;


     	}else {
     		echo "edit error  {$_GET['edit_id'] }  " ;
     	}

}
