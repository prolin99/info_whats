<?php
 include_once "header.php";

if ($_POST['now_id']) {
  	$ip_k = preg_split("/[.]/", $_POST['txt_ip']);
   	$ip_id=$ip_k[2]*1000 + $ip_k[3] ;
     	$sql = " update " . $xoopsDB->prefix("mac_info") .  " set  phid ='{$_POST['txt_phid']}'  ,kind ='{$_POST['txt_kind']}'  ,  ip ='{$_POST['txt_ip']}' , comp='{$_POST['txt_comp']}',  ps='{$_POST['txt_ps']}'  ,  ip_id ='$ip_id'  , modify_day=now() ,
            scM_id ='{$_POST['txt_scmid']}' ,scM_id2 ='{$_POST['txt_scmid2']}'  , baseboard ='{$_POST['txt_mbaseb']}' ,class_place ='{$_POST['txt_c_p']}'
          where id='{$_POST['now_id']}' " ;

      	$result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());
      	//echo $sqlstr ;

      	//read
		$sql = " select * from " . $xoopsDB->prefix("mac_info") .  "  where id='{$_POST['now_id']}' " ;


     		$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
     		$row=$xoopsDB->fetchArray($result) ;
     		//echo $sqlstr  ;
     		$row['creat_day']= substr($row['creat_day'] ,2,8) ;
            $row['modify_day_s']= substr($row['modify_day'] ,2,8) ;

     		echo "
	<div class='span1'><!---- 重要標記  -->
	<span id='point_{$row['id']}' class='point' title='重要標記' data='{$row['id']}_0'> <i class='icon-star-empty'></i></span>
	<span class='badge badge-info'>{$_POST['ord_id']} </span>{$row['kind']}-{$row['phid']}</div>
	<div class='span2'><span class='ip' alt='{$row['ip']}'>{$row['ip']}</span></div>
	<div class='span2'>{$row['mac']}<br/>{$row['class_place']}--{$row['scM_id']}<br/>{$row['scM_id2']}</div>
	<div class='span2'><span class='label label-info'>{$row['comp']} </span>{$row['ps']} {$row['baseboard']}

    <span class='label' title='{$row['modify_day']}'>{$row['modify_day_s']}</span>
    </div>
	<div class='span1'>
    <a href='hardware.php?id={$row['id']}'
    <span class='badge badge-inverse'>#{$row['id']}</span>
    </a>
	    <span class='edit'><i class='icon-pencil'></i></span>

	  </div>

	<div class='span2'>{$row['recode_time']}
	<span class='del'><i class='icon-trash'></i>
	<br /><span class='label'>{$row['creat_day']}</span>
	</span>
	</div>
		" ;


}
