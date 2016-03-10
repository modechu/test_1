<?php

	require_once "config.php";
	require_once "config.admin.php";
	require_once "init.object.php";
	$PHP_SELF = $_SERVER['PHP_SELF'];
	$mb = date('ymd');

	$img = null;


	if(!empty($PHP_title)){
		if($PHP_type == 'smpl_pic'){
			$op['main_pic'] = 'smpl_pic/'.$PHP_title.'.jpg?'.$mb;
			if(!empty($PHP_pic_url))$op['main_pic'] = $PHP_pic_url;
		} elseif ($PHP_type == 'fab_stock'){
			// $op['main_pic'] = 'fab_stock/'.$PHP_title.'.jpg?'.$mb;
			// if(!empty($PHP_pic_url))$op['main_pic'] = $PHP_pic_url;
		}else{

			if(!empty($PHP_pic_num)){
				$p_num = explode('|',$PHP_pic_num);
				for($i=0;$i < count($p_num); $i++)if( $p_num[$i] == $PHP_now_img ) $PHP_now_img = $i;
				$pic_num = array_merge($PHP_title, $p_num);
				$num = array_pop($p_num);				
				if(isset($PHP_now_img) && $PHP_now_img <> -1 ){
					$op['main_pic'] = 'picture/'.$PHP_title.'_'.$pic_num[($PHP_now_img+1)].'.jpg?'.$mb;
				} else {

					$op['main_pic'] = 'picture/'.$PHP_title.'.jpg?'.$mb;
					$PHP_now_img = -1;

				}

				if($PHP_title <> $pic_num[($PHP_now_img+1)] ) $op['perv'] =  "$PHP_SELF?PHP_title=$PHP_title&PHP_type=$PHP_type&PHP_pic_num=$PHP_pic_num&PHP_now_img=".($PHP_now_img-1);
				if($num <> $pic_num[($PHP_now_img+1)] ) $op['next'] = "$PHP_SELF?PHP_title=$PHP_title&PHP_type=$PHP_type&PHP_pic_num=$PHP_pic_num&PHP_now_img=".($PHP_now_img+1);

			} else {
			
				$op['main_pic'] = 'picture/'.$PHP_title.'.jpg?'.$mb;
				if(!empty($PHP_pic_url))$op['main_pic'] = $PHP_pic_url;
				
			}
		}
	}
	
	page_display($op, 000, $TPL_POPUP_IMG);
?>