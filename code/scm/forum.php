<?php
// error_reporting(0);

$TPL_FORUM_INDEX = "forum_index.html";
$TPL_FORUM_MAIN = "forum_main.html";
$TPL_FORUM_ADMIN = "forum_admin.html";
$TPL_FORUM_VIEW = "forum_view.html";
$TPL_FORUM_POST = "forum_post.html";
$TPL_FORUM_RE_POST = "forum_re_post.html";
$TPL_FORUM_EDIT = "forum_edit.html";
$TPL_FORUM_RE_EDIT = "forum_re_edit.html";
$TPL_FORUM_ERROR = "forum_error.html";
$TPL_FORUM_ERROR2 = "forum_error2.html";
$TPL_FORUM_ERROR3 = "forum_error3.html";
$TPL_FORUM_ERROR4 = "forum_error4.html";
$TPL_FORUM_ERROR5 = "forum_error5.html";
$TPL_FORUM_ERROR6 = "forum_error6.html";
$TPL_FORUM_SEARCH = "forum_search.html";
$TPL_FORUM_MANAGER_ADD = "forum_manager_add.html";
$TPL_FORUM_MANAGER_EDIT = "forum_manager_edit.html";



# theme -> = 1 Post ; = 2 Re Post ;
/*----------
# 功能說明 : 關聯 class
# 關聯變數 : 
# 輸入變數 : 
# 輸出變數 : 
# 撰寫日期 : 2010/06/01
# 備註說明 : 
----------*/
include_once("config.php");
include_once("config.admin.php");
include_once("init.object.php");
include_once("includes/forum.class.php"); $Forum = new FORUM();
include_once("includes/upload.class.php"); $Uploads = new Uploads();

$Upload = new Upload;

$op = array();
$auth = '079';
$job = $_SESSION['ITEM']['ADMIN_PERM'];



$Ac = !empty($_POST['Ac']) ? $_POST['Ac'] : (!empty($_GET['Ac'])?$_GET['Ac']:'');
switch ($Ac) {
//=======================================================
	default;

	#
	# 討論版首頁
	case "forum_index":
 	$jobs = check_authority($auth,$job[1]);

	if(empty($_SESSION['USER']['ADMIN']['login_id'])){
		$op['msg'] = '您是否看不到討論版的畫面呢？';
		switch ($_GET['mode']){
			default;
			page_display($op,$auth,$TPL_FORUM_ERROR);
			break;

			case 2:
			page_display($op,$auth,$TPL_FORUM_ERROR2);
			break;

			case 3:
			page_display($op,$auth,$TPL_FORUM_ERROR3);
			break;

			case 4:
			page_display($op,$auth,$TPL_FORUM_ERROR4);
			break;

			case 5:
			page_display($op,$auth,$TPL_FORUM_ERROR5);
			break;

			case 6:
			page_display($op,$auth,$TPL_FORUM_ERROR6);
			break;
		}
		break;
	}
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
	$forum_main = $Forum->get_forum_main();
	$op['pages'] = $forum_main;
    if($forum_main)
	foreach($forum_main as $key => $val){
		$id = !empty($main_id) ? $main_id : $val['id'];
		if($val['id'] == $id){
			$forum_group = $Forum->get_forum_group('',$id);
			if($forum_group){
				foreach($forum_group as $keys => $vals){
					$forum_group[$keys]['TOPICS'] = $Forum->TOPICS($vals['id']);
					$forum_group[$keys]['POSTS'] = $Forum->POSTS($vals['id']);
					$re_post = $Forum->LAST_POST($vals['id']);
					$post =	$Forum->TOP_POST($vals['id']);
					($re_post['time'] > $post['time']) ? $top_post = $re_post : $top_post = $post ;
					if(empty($re_post['time']))$top_post = $post ;
					$forum_group[$keys]['LAST_POST'] = $top_post;
				}
				$op['pages'][$key]['main'] = $forum_group;
			}
		}
	}
	$op['select_main'] = $Forum->select_search($main_id);
	$op['forum_name'] = 'SCM 討論版';
	page_display($op,$auth,$TPL_FORUM_INDEX);
	break;




	#
	# 討論版列表
	case "forum_main":
 	$jobs = check_authority($auth,$job[1]);
  
    $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
	$forum_group = $Forum->list_forum($group_id);
	if(!empty($forum_group['pages'])){
		foreach($forum_group['pages'] as $keys => $values){
			foreach($values as $key => $val){
				if( $key == 'serial' ){
					$re_post = $Forum->LAST_POST($group_id,$val);
					$post =	$Forum->TOP_POST($group_id,$val);
					($re_post['time'] > $post['time']) ? $top_post = $re_post : $top_post = $post ;
					$forum_group['pages'][$keys]['LAST_POST'] =	$top_post;
					$top_post = $re_post = $post = '';
				}
			}
		}
	}
  
  if(!empty($forum_group['pages']))
	$op['pages'] = $forum_group['pages'];
	$op['page_num'] = $forum_group['page_num'];
	# 主題名稱
	$forum_main = $Forum->get_forum_main($main_id);
	$op['main_title'] = $forum_main[0]['title'];
	$forum_name = $Forum->get_forum_group($group_id);
	$op['post_title'] = $forum_name[0]['title'];

	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：'.$forum_main[0]['title'];
	$op['group_id'] = $group_id;
	$op['main_id'] = $main_id;
	$op['select_main'] = $Forum->select_search($main_id);
	page_display($op,$auth,$TPL_FORUM_MAIN);
	break;


	#
	# 檢視文章
	case "forum_view":
 	$jobs = check_authority($auth,$job[1]);
    $serial = !empty($_GET['serial'])?$_GET['serial']:'';
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
    $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';

	# 增加點閱數
	$Forum->count_plus($serial);

	$op['post'] = $Forum->post($serial,'',1);

	if($op['post']['upload']){
		$file=explode('|',$op['post']['upload']);
		$ct = count($file);
		$content = $op['post']['content'];
		for($m=0;$m < $ct; $m++){
			$filev=explode('.',$file[$m]);
			$filen = str_replace('.'.array_pop($filev),'',$file[$m]);
			#$filelink = '<a href="index2.php?Ac=download&PHP_dir=http://scm/forum/data/'.$serial.'/&PHP_file='.$file[$m].'&PHP_name='.$filen.'" >'.$filen.'</a>';
			$filelink = '<a href="forum.php?Ac=download&upload_dir=forum%2Fdata%2F'.$serial.'%2F&upload_file='.urlencode($file[$m]).'" style="color:#996600">('.$filen.')</a>';
			$files[$m] = '<a href="forum.php?Ac=download&upload_dir=forum%2Fdata%2F'.$serial.'%2F&upload_file='.urlencode($file[$m]).'" style="color:#996600">'.$file[$m].'</a>&nbsp;&nbsp;';
			#$content = str_replace($filen,$filelink,$content);
		}
		$op['post']['content'] = html_entity_decode($content);
		$op['upload'] = $files;
	}

	$forum_post = $Forum->re_post_list($serial);
    if(!empty($forum_post['pages']))
	$op['pages'] = $forum_post['pages'];
	$op['page_num'] = $forum_post['page_num'];

	# 主題名稱
	$forum_main = $Forum->get_forum_main($main_id);
	$op['main_title'] = $forum_main[0]['title'];
	$forum_name = $Forum->get_forum_group($group_id);
	$op['post_title'] = $forum_name[0]['title'];

	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：'.$forum_main[0]['title'].' \ 檢視文章';

	$op['group_id'] = $group_id;
	$op['main_id'] = $main_id;

	# 內文主題名稱
	$forum_title = $Forum->get_forum_post($serial);
	$op['re_title'] = $forum_title[0]['title'];
	$op['serial'] = $forum_title[0]['serial'];
	$op['select_main'] = $Forum->select_search($main_id);
	page_display($op,$auth,$TPL_FORUM_VIEW);
	break;


	#
	# 發表主題
	case "forum_post":
 	$jobs = check_authority($auth,$job[1]);
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
    $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';
  
	# 主題名稱
	$forum_main = $Forum->get_forum_main($main_id);
	$op['main_title'] = $forum_main[0]['title'];
	$forum_name = $Forum->get_forum_group($group_id);
	$op['post_title'] = $forum_name[0]['title'];
	$op['title'] = $forum_name[0]['title'];
	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：'.$forum_main[0]['title'].' \ 發表主題';

	$op['group_id'] = $group_id;
	$op['main_id'] = $main_id;
	$op['theme'] = 1;

  if(!empty($_SESSION['msg']))
	$op['msg'] = $_SESSION['msg'];
	unset($_SESSION['msg']);
	page_display($op,$auth,$TPL_FORUM_POST);
	break;


	#
	# 回覆主題
	case "forum_re_post":
 	$jobs = check_authority($auth,$job[1]);
    $serial = !empty($_GET['serial'])?$_GET['serial']:'';
    $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
  
	# 主題名稱
	$forum_main = $Forum->get_forum_main($main_id);
	$op['main_title'] = $forum_main[0]['title'];
	$forum_name = $Forum->get_forum_group($group_id);
	$op['post_title'] = $forum_name[0]['title'];

	# 內文主題名稱
	$forum_title = $Forum->get_forum_post($serial);
	$op['re_title'] = $forum_title[0]['title'];
	$op['serial'] = $forum_title[0]['serial'];
	$op['content'] = nl2br($forum_title[0]['content']);

	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：'.$forum_main[0]['title'].' \ 回覆主題';

	$op['group_id'] = $group_id;
	$op['main_id'] = $main_id;
	$op['serial'] = $serial;
	$op['theme'] = 2;
 	$parm = array(
 									'serial'		=>  $serial,
 									'main_id'		=>  $main_id,
 									'group_id'	=>  $group_id );
	$op['post'] = $parm;

	page_display($op,$auth,$TPL_FORUM_POST);
	break;


	#
	# 修改主題
	case "forum_edit":
 	$jobs = check_authority($auth,$job[3]);
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
    $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';
    $serial = !empty($_GET['serial'])?$_GET['serial']:'';
    $re_serial = !empty($_GET['re_serial'])?$_GET['re_serial']:'';
    $search_view = !empty($_GET['search_view'])?$_GET['search_view']:'';
    $forum_text = !empty($_GET['forum_text'])?$_GET['forum_text']:'';
  
	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] != "SA")){
		$row = $Forum->get_login($serial,'');
		if( $row['login_id'] <> $_SESSION['USER']['ADMIN']['login_id'] ){
			$_SESSION['msg'] = '<font color="red">必須要發文者,才可編輯!!</font>';
			header ("Location: ".$_SERVER['HTTP_REFERER']." ");
			break;
		}
	}

	# 主題名稱
	$forum_main = $Forum->get_forum_main($main_id);
	$op['main_title'] = $forum_main[0]['title'];
	$forum_name = $Forum->get_forum_group($group_id);
	$op['post_title'] = $forum_name[0]['title'];

	# 內文主題名稱
	$forum_title = $Forum->get_forum_post($serial);
	$op['re_title'] = $forum_title[0]['title'];

	$op['post'] = $Forum->post($serial);
	if($op['post']['upload']){
		$file=explode('|',$op['post']['upload']);
		$ct = count($file);
		for($m=0;$m < $ct; $m++){
			$filev=explode('.',$file[$m]);
			$fn = array_pop($filev);
			$filen = str_replace('.'.$fn,'',$file[$m]);
			#$files[$m] = '<a href="forum/data/'.$serial.'/'.$file[$m].'" target="Resource Window" tyle="color:#996600">'.$file[$m].'</a> <a href="forum.php?Ac=forum_del_file&serial='.$serial.'&group_id='.$group_id.'&main_id='.$main_id.'&file_name='.urlencode($file[$m]).'" ><img src="images/button_empty.png" style="cursor:hand;" alt="刪除：'.$file[$m].'" border="0"></a>&nbsp;&nbsp;';
			#$files[$m] = '<a href="#" onclick="window.open2(\'forum/data/'.$serial.'/'.$file[$m].'\',\'123\')" style="color:#996600">'.$file[$m].'</a> <a href="forum.php?Ac=forum_del_file&serial='.$serial.'&group_id='.$group_id.'&main_id='.$main_id.'&file_name='.urlencode($file[$m]).'" ><img src="images/button_empty.png" style="cursor:hand;" alt="刪除：'.$file[$m].'" border="0"></a>&nbsp;&nbsp;';
			#$files[$m] = '<a href="index2.php?Ac=download&PHP_dir=forum%2Fdata%2F'.$serial.'%2F&PHP_file='.urlencode($file[$m]).'&PHP_name='.urlencode($file[$m]).'" target="_blank" style="color:#996600">'.$file[$m].'</a> <a href="forum.php?Ac=forum_del_file&serial='.$serial.'&group_id='.$group_id.'&main_id='.$main_id.'&file_name='.urlencode($file[$m]).'" ><img src="images/button_empty.png" style="cursor:hand;" alt="刪除：'.$file[$m].'" border="0"></a>&nbsp;&nbsp;';
			#$files[$m] = '<a href="#" onclick="location.href=\'forum/data/'.$serial.'/'.$file[$m].'\'" style="color:#996600">'.$file[$m].'</a> <a href="forum.php?Ac=forum_del_file&serial='.$serial.'&group_id='.$group_id.'&main_id='.$main_id.'&file_name='.urlencode($file[$m]).'" ><img src="images/button_empty.png" style="cursor:hand;" alt="刪除：'.$file[$m].'" border="0"></a>&nbsp;&nbsp;';
			$files[$m] = '<a href="forum.php?Ac=download&upload_dir=forum%2Fdata%2F'.$serial.'%2F&upload_file='.urlencode($file[$m]).'" style="color:#996600">'.$file[$m].'</a> <a href="forum.php?Ac=forum_del_file&serial='.$serial.'&group_id='.$group_id.'&main_id='.$main_id.'&file_name='.urlencode($file[$m]).'" ><img src="images/button_empty.png" style="cursor:hand;" alt="刪除：'.$file[$m].'" border="0" onclick="return rusure();"></a>&nbsp;&nbsp;';

		}
		$op['upload'] = $files;
	}

	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：'.$forum_main[0]['title'].' \ 修改主題';

	$op['group_id'] = $group_id;
	$op['main_id'] = $main_id;
	$op['re_serial'] = $re_serial;
	$op['serial'] = $serial;
	$op['search_view'] = $search_view;
	$op['forum_text'] = $forum_text;
	$op['theme'] = 1;

	page_display($op,$auth,$TPL_FORUM_EDIT);
	break;




	#
	# 修改回覆文章
	case "download":

	if(!$filename)$filename=$upload_file;
	$fp = fopen($upload_dir.$upload_file,"r");
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($upload_dir.$upload_file));
	#Header("Content-Disposition: attachment; filename=" . $upload_file);

	if (ereg("MSIE",$_SERVER["HTTP_USER_AGENT"])){
	//如果是IE那個低能兒就將檔名轉為Big5,再送出檔名
		Header("Content-Disposition: attachment; filename=".iconv('utf8','big5',$upload_file));
	        }else{
	//其他高智商的瀏覽器就直接送出檔名
		Header("Content-Disposition: attachment; filename=".$upload_file);
	}
	echo fread($fp,filesize($upload_dir . $upload_file));
	fclose($fp);
	break;



	#
	# 修改回覆文章
	case "forum_re_edit":
    $jobs = check_authority($auth,$job[3]);
    $theme = !empty($_GET['theme'])?$_GET['theme']:'';
    $serial = !empty($_GET['serial'])?$_GET['serial']:'';
    $re_serial = !empty($_GET['re_serial'])?$_GET['re_serial']:'';
    $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';
    $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';

    $search_view = !empty($_GET['search_view'])?$_GET['search_view']:'';
    $forum_text = !empty($_GET['forum_text'])?$_GET['forum_text']:'';

	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] != "SA")){
		$row = $Forum->get_login('',$re_serial);
		if( $row['re_login_id'] <> $_SESSION['USER']['ADMIN']['login_id'] ){
			$_SESSION['msg'] = '<font color="red">必須要發文者,才可編輯!!</font>';
			header ("Location: ".$_SERVER['HTTP_REFERER']." ");
			break;
		}
	}

	# 主題名稱
	$forum_main = $Forum->get_forum_main($main_id);
	$op['main_title'] = $forum_main[0]['title'];
	$forum_name = $Forum->get_forum_group($group_id);
	$op['post_title'] = $forum_name[0]['title'];

	# 內文主題名稱
	$forum_title = $Forum->get_forum_post($serial);
	$op['re_title'] = $forum_title[0]['title'];

	$op['post'] = $Forum->re_post($re_serial);
	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：'.$forum_main[0]['title'].' \ 修改回覆文章';

	$op['group_id'] = $group_id;
	$op['main_id'] = $main_id;
	$op['re_serial'] = $re_serial;
	$op['serial'] = $serial;
	$op['search_view'] = $search_view;
	$op['forum_text'] = $forum_text;
	$op['theme'] = 2;

	page_display($op,$auth,$TPL_FORUM_RE_EDIT);
	break;



	#
	# 新增文章
	case "forum_add":
 	$jobs = check_authority($auth,$job[2]);
    $theme = !empty($_POST['theme'])?$_POST['theme']:'';
    $title = !empty($_POST['title'])?$_POST['title']:'';
    $post_file = !empty($_POST['post_file'])?$_POST['post_file']:'';
    $content = !empty($_POST['content'])?$_POST['content']:'';

    $serial = !empty($_POST['serial'])?$_POST['serial']:'';
    $group_id = !empty($_POST['group_id'])?$_POST['group_id']:'';  
    $main_id = !empty($_POST['main_id'])?$_POST['main_id']:'';  
  
	$Upload->setMaxSize(2072864);
	$error_msg = $filename ='';

	if(!empty($post_file[0])){
		$co = count($post_file);
		for($m=0;$m < $co ;$m++){
			$file = explode('.',$_FILES['post_file']['name'][$m]);
			$fval = array_pop($file);

			if (!$Upload->chk_valid_types($fval)){
				$error_msg .= '不能上傳該副檔名的檔案 [ '.$fval.' ]。<br>';
			}

			if (!$Upload->check_Size($_FILES['post_file']['size'][$m])){
				$error_msg .= '該檔案超過上傳容許大小 [ '.$_FILES['post_file']['name'][$m].' ]。<br>';
			}
			$filename .= $_FILES['post_file']['name'][$m].'|';
		}
		if($filename)$filename= substr($filename,0,-1);

		if(!empty($error_msg)){
			$_SESSION['msg'] = $error_msg;
			if($theme==1)
				redirect_page('forum.php?Ac=forum_post&title='.$title.'&serial='.$serial.'&content='.$content.'&main_id='.$main_id.'&group_id='.$group_id);
			else
				redirect_page('forum.php?Ac=forum_re_post&title='.$title.'&serial='.$serial.'&content='.$content.'&main_id='.$main_id.'&group_id='.$group_id);
			break;
		}
	}

$parm = array(
'id'        =>  $_SESSION['USER']['ADMIN']['id'],
'dept'		=>  $_SESSION['USER']['ADMIN']['dept'],
'team'		=>  $_SESSION['USER']['ADMIN']['team_id'],
'login_id'	=>  $_SESSION['USER']['ADMIN']['login_id'],
'email'		=>  $_SESSION['USER']['ADMIN']['email'],
'ip'		=>	$GLOBALS['_SERVER']['REMOTE_ADDR'],
'theme'		=>  $theme,
'title'		=>  $title,
'serial'	=>  $serial,
'content'	=>  $content,
'main_id'	=>  $main_id,
'group_id'	=>  $group_id,
'upload'	=>  $filename 
);


$serial_id = $Forum->add($parm);

if($theme==1){
    if($post_file){
        $url_forum = $_SERVER["DOCUMENT_ROOT"].'/forum/';
        $url_data = $_SERVER["DOCUMENT_ROOT"].'/forum/data/';
        add_folder($url_forum);
        add_folder($url_data);

        $url_file = $_SERVER["DOCUMENT_ROOT"].'/forum/data/'.$serial_id;
        add_folder($url_file);

        for($m=0;$m < $co ;$m++){
            if (!move_uploaded_file($_FILES['post_file']['tmp_name'][$m], $url_file.'/'.$_FILES['post_file']['name'][$m])) {
                $error_msg .= "Possible file upload attack!";
            }
        }

        if(!empty($error_msg)){
            $_SESSION['msg'] = $error_msg;
            if($theme==1)
                redirect_page('forum.php?Ac=forum_post&title='.$title.'&serial='.$serial.'&content='.$content.'&main_id='.$main_id.'&group_id='.$group_id);
            else
                redirect_page('forum.php?Ac=forum_re_post&title='.$title.'&serial='.$serial.'&content='.$content.'&main_id='.$main_id.'&group_id='.$group_id);
            break;
        }
    }
}


# 增加回覆數
$Forum->count_plus($serial,$theme);

if($theme==1){

$user = _base64($GLOBALS['SCACHE']['ADMIN']['name']);

// $to = 'Yankee<jack@tp.carnival.com.tw>';
$to = 'Mode<mode@tp.carnival.com.tw>';

$headers="MIME-Version:1.0\r\n";
$headers.="Content-type:text/html;charset=big5\r\n";
$headers .= "From: ".$user." <".$_SESSION['USER']['ADMIN']['email'].">\r\n";
$headers .= "Bcc:Mode<mode@tp.carnival.com.tw> \n";
// $headers .= "Bcc:Angel<angelin@tp.carnival.com.tw> \n";
$headers .= "Reply-To: ".$to;
$headers .= "Return-Path: ".$to;

$from = $_SESSION['USER']['ADMIN']['email'];
$boundary = uniqid( "");
$content = nl2br(str_replace('\\','',$content));
# 主題名稱
$forum_main = $Forum->get_forum_main($main_id);
$main_title = $forum_main[0]['title'];
$forum_name = $Forum->get_forum_group($group_id);
$post_title = $forum_name[0]['title'];
$subject = _base64("[ From:SCM 討論版 ] ".$main_title.' -> '.$post_title.' -> '.$title);
$html ='
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>討論版主題回應</title>
<style type="text/css">
<!--
body {	margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}
A {
	COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial; TEXT-DECORATION: none;}
A:link {
	COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial; TEXT-DECORATION: none;
}

A:visited {	COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial; TEXT-DECORATION: none;
}
A:active {	COLOR: black; FONT-FAMILY: Tahoma, Verdana, Arial; TEXT-DECORATION: none;
}
A:hover {	COLOR: black; FONT-FAMILY: Tahoma, Verdana, Arial; TEXT-DECORATION: none;
}
BODY {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
TD {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
SELECT {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
INPUT {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
DIV {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
FORM {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
TEXTAREA {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
CENTER {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
OPTION {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
PRE {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
BR {	FONT-SIZE: 9pt; COLOR: #666666; FONT-FAMILY: Tahoma, Verdana, Arial}
BODY {	SCROLLBAR-FACE-COLOR: white; MARGIN: 0px; SCROLLBAR-HIGHLIGHT-COLOR: gainsboro; OVERFLOW: auto; SCROLLBAR-SHADOW-COLOR: gainsboro; SCROLLBAR-3DLIGHT-COLOR: whitesmoke; SCROLLBAR-ARROW-COLOR: gainsboro; SCROLLBAR-TRACK-COLOR: white; SCROLLBAR-DARKSHADOW-COLOR: whitesmoke}
.button {	BORDER-RIGHT: black 1px solid; BORDER-TOP: black 1px solid; BORDER-LEFT: black 1px solid; BORDER-BOTTOM: black 1px solid; FONT-FAMILY: Tahoma, Verdana, Arial; HEIGHT: 20px; BACKGROUND-COLOR: white}
.input {	BORDER-RIGHT: black 1px solid; BORDER-TOP: black 1px solid; BORDER-LEFT: black 1px solid; BORDER-BOTTOM: black 1px solid; FONT-FAMILY: Tahoma, Verdana, Arial; BACKGROUND-COLOR: white}
.minichi {	FONT-SIZE: 8pt; FONT-FAMILY: 新細明體, PMingLiU}
SELECT {	FONT-FAMILY: 新細明體, PMingLiU}
body {	background-color: #E5E5E5;	scrollbar-face-color: #DEE3E7;	scrollbar-highlight-color: #FFFFFF;	scrollbar-shadow-color: #DEE3E7;	scrollbar-3dlight-color: #D1D7DC;	scrollbar-arrow-color:  #006699;	scrollbar-track-color: #EFEFEF;	scrollbar-darkshadow-color: #98AAB1;}
.forumline	{ background-color: #FFFFFF; border: 2px #CCCCCC solid; }
th	{	color: #EEEEEE; font-size: 11px; font-weight : bold;	background-color: #333333; height: 25px;	background-image: url(images/cellpic3.gif);}
th.thHead,th.thSides,th.thTop,th.thLeft,th.thRight,th.thBottom,th.thCornerL,th.thCornerR {	font-weight: bold; border: #FFFFFF; border-style: solid; height: 28px; }
td.row3Right,td.spaceRow {	background-color: #D1D7DC; border: #FFFFFF; border-style: solid; }
th.thHead,td.catHead { font-size: 12px; border-width: 1px 1px 0px 1px; }
th.thSides,td.catSides,td.spaceRow	 { border-width: 0px 1px 0px 1px; }
th.thRight,td.catRight,td.row3Right	 { border-width: 0px 1px 0px 0px; }
th.thBottom,td.catBottom  { border-width: 0px 1px 1px 1px; }
th.thCornerL { border-width: 1px 0px 0px 1px; }
th.thTop	 { border-width: 1px 0px 0px 0px; }
th.thCornerR { border-width: 1px 1px 0px 0px; }

/* Forum category titles */
a.cattitle		{ text-decoration: underline; color : #006699; }
.cattitle		{	font-weight: bold;	font-size: 12px;	letter-spacing: 1px;	color : #006699;}
td.rowpic {		background-color: #FFFFFF;		background-image: url(images/cellpic2.jpg);		background-repeat: repeat-y;}
td.cat,td.catHead,td.catSides,td.catLeft,td.catRight,td.catBottom {			background-image: url(images/cellpic1.gif);			background-color:#D1D7DC; border: #FFFFFF; border-style: solid; height: 28px;}
td.cat,td.catHead,td.catBottom {	height: 29px;	border-width: 0px 0px 0px 0px;}
th.thLeft,td.catLeft	  { border-width: 0px 0px 0px 1px; }
/* Main table cell colours and backgrounds */
td.row1	{ background-color: #EFEFEF; }
td.row2	{ background-color: #DEE3E7; }
td.row3	{ background-color: #D1D7DC; }
-->
</style>
</head>
<body>
<br>
<p>
<table width="80%" border=0 align="center" cellspacing="2" cellpadding="2" border="0">
  <tr>
    <td align="left" valign="middle" width="100%"><span class="nav">&nbsp;&nbsp;&nbsp; '.$main_title.' -> '.$post_title.' -> '.$title.'</span></td>
  </tr>
</table>
<table width="80%" border=0 align="center" cellpadding=0 cellspacing=0 class="forumline">
  <tbody>
    <tr>
      <td valign="top">
				<table width="100%" border="0" align="center" cellpadding="6" cellspacing="2">
					<tr>
					  <th class="thLeft" width="150" height="26" nowrap="nowrap">發表人</th>
					  <th class="thRight" nowrap="nowrap">內容</th>
					</tr>
					<tr>
					  <td width="150" align="left" valign="top" class="row1"><span class="name"><b>'.$GLOBALS['SCACHE']['ADMIN']['name'].'</b></span><br /></td>
					  <td width="100%" height="28" valign="top" class="row1">
					  	<table width="100%" border="0" cellspacing="0" cellpadding="0">
					      <tr>
					        <td width="100%"><span class="postdetails">發表主題 : '.$title.'<span class="gen">&nbsp;</span>&nbsp; &nbsp;發表時間 : '.date("Y-m-d h:i:s").'</span></td>
					        <td valign="top" nowrap="nowrap"></td>
					      </tr>
					      <tr>
					        <td colspan="2"><hr /></td>
					      </tr>
					      <tr>
					        <td colspan="2">'.$content.'</td>
					      </tr>
					  	</table>
					  </td>
					</tr>
				</table>
		  </td>
		</tr>
	</tbody>
</table>
</body>
</html>
';

$msg = "


$html

";


mail("$to", "$subject", "$msg", $headers, "-f" . $from);

}

if(empty($theme))
    header ("Location: ".$PHP_SELF."?Ac=forum_view&group_id=".$group_id."&main_id=".$main_id."&serial=".$serial);
elseif($theme==2)
    header ("Location: ".$PHP_SELF."?Ac=forum_view&group_id=".$group_id."&main_id=".$main_id."&serial=".$serial);
else
    header ("Location: ".$PHP_SELF."?Ac=forum_main&group_id=".$group_id."&main_id=".$main_id);

break;


#
# 更新文章
case "forum_update":
$jobs = check_authority($auth,$job[3]);
$theme = !empty($_POST['theme'])?$_POST['theme']:'';
$title = !empty($_POST['title'])?$_POST['title']:'';
$post_file = !empty($_POST['post_file'])?$_POST['post_file']:'';
$content = !empty($_POST['content'])?$_POST['content']:'';

$serial = !empty($_POST['serial'])?$_POST['serial']:'';
$group_id = !empty($_POST['group_id'])?$_POST['group_id']:'';
$main_id = !empty($_POST['main_id'])?$_POST['main_id']:'';
$upload = !empty($_POST['upload'])?$_POST['upload']:'';
$search_view = !empty($_POST['search_view'])?$_POST['search_view']:'';
$re_serial = !empty($_POST['re_serial'])?$_POST['re_serial']:'';

$Upload->setMaxSize(2072864);
$error_msg = $filename ='';

	if(!empty($post_file[0])){
		$co = count($post_file);
		for($m=0;$m < $co ;$m++){
			$file = explode('.',$_FILES['post_file']['name'][$m]);
			$fval = array_pop($file);

			if (!$Upload->chk_valid_types($fval)){
				$error_msg .= '不能上傳該副檔名的檔案 [ '.$fval.' ]。<br>';
			}

			if (!$Upload->check_Size($_FILES['post_file']['size'][$m])){
				$error_msg .= '該檔案超過上傳容許大小 [ '.$_FILES['post_file']['name'][$m].' ]。<br>';
			}
			$upfile = explode('|',$upload);
			$upco = count($upfile);
			for($o=0;$o < $upco ;$o++){
				if ($_FILES['post_file']['name'][$m] == $upfile[$o]){
					$error_msg .= '該檔案重複上傳 [ '.$_FILES['post_file']['name'][$m].' ]！<br>';
				}
			}
			$filename .= $_FILES['post_file']['name'][$m].'|';
		}
		if($filename)$filename = substr($filename,0,-1);
		if($upload)$filename = $upload.'|'.$filename;
		if(!empty($error_msg)){
			$_SESSION['msg'] = $error_msg;
			redirect_page('forum.php?Ac=forum_edit&serial='.$serial.'&main_id='.$main_id.'&group_id='.$group_id);
			break;
		}
	}else if($upload){
		$filename = $upload;
	}

 	$parm = array(
 									'ip'				=>	$GLOBALS['_SERVER']['REMOTE_ADDR'],
 									'theme'			=>  $theme,
 									'title'			=>  $title,
 									'serial'		=>  $serial,
 									're_serial'	=>  $re_serial,
 									'content'		=>  $content,
 									'upload'		=>  $filename,);

	$Forum->update($parm);

	$url_forum = $_SERVER["DOCUMENT_ROOT"].'/forum/';
	$url_data = $_SERVER["DOCUMENT_ROOT"].'/forum/data/';
	add_folder($url_forum);
	add_folder($url_data);

	$url_file = $_SERVER["DOCUMENT_ROOT"].'/forum/data/'.$serial;
	add_folder($url_file);

	for($m=0;$m < $co ;$m++){
		if (!move_uploaded_file($_FILES['post_file']['tmp_name'][$m], $url_file.'/'.$_FILES['post_file']['name'][$m])) {
			$error_msg .= "Possible file upload attack!";
		}
	}

	if(!empty($error_msg)){
		$_SESSION['msg'] = $error_msg;
		redirect_page('forum.php?Ac=forum_edit&serial='.$serial.'&main_id='.$main_id.'&group_id='.$group_id);
		break;
	}

	if(!empty($search_view)){
		header ("Location: ".$PHP_SELF."?Ac=forum_search&group_id=".$group_id."&main_id=".$main_id."&serial=".$serial);
	}else{
		header ("Location: ".$PHP_SELF."?Ac=forum_view&group_id=".$group_id."&main_id=".$main_id."&serial=".$serial);
	}
	break;


	#
	# 刪除文章
	case "forum_del":
 	$jobs = check_authority($auth,$job[4]);
  $theme = !empty($_GET['theme'])?$_GET['theme']:'';
  $re_serial = !empty($_GET['re_serial'])?$_GET['re_serial']:'';
  $serial = !empty($_GET['serial'])?$_GET['serial']:'';
  $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
  $group_id = !empty($_GET['group_id'])?$_GET['group_id']:'';
  
	$Forum->del($theme,$serial,$re_serial);

	if(!empty($search_view))
		header ("Location: ".$PHP_SELF."?Ac=forum_search&group_id=".$group_id."&main_id=".$main_id."&serial=".$serial);
	else if($theme==2)
		header ("Location: ".$PHP_SELF."?Ac=forum_view&group_id=".$group_id."&main_id=".$main_id."&serial=".$serial);
	else
		header ("Location: ".$PHP_SELF."?Ac=forum_main&group_id=".$group_id."&main_id=".$main_id);

	break;


	#
	# 刪除文章附件
	case "forum_del_file":
 	$jobs = check_authority($auth,$job[4]);

	$Forum->del_file($serial,$file_name);

	redirect_page('forum.php?Ac=forum_edit&serial='.$serial.'&main_id='.$main_id.'&group_id='.$group_id);
	break;



	#
	# 討論版管理
	case "forum_manager":
 	$jobs = check_authority($auth,$job[4]);
  
  $main_id = !empty($_POST['main_id'])?$_POST['main_id']:'';
  
	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")){
		$forum_main = $Forum->get_forum_main();
		if($forum_main){
			$op['pages'] = $forum_main;
			foreach($forum_main as $key => $val){
				$id = ($main_id) ? $main_id : $val['id'];
				if($val['id'] == $id){
					$forum_group = $Forum->get_forum_group('',$id);
					if($forum_group){
						foreach($forum_group as $keys => $vals){
							$forum_group[$keys]['TOPICS'] = $Forum->TOPICS($vals['id']);
							$forum_group[$keys]['POSTS'] = $Forum->POSTS($vals['id']);
							$re_post = $Forum->LAST_POST($vals['id']);
							$post =	$Forum->TOP_POST($vals['id']);
							$top_post = ($re_post['time'] > $post['time']) ? $re_post : $post ;
							if(empty($re_post['time']))$top_post = $post ;
							$forum_group[$keys]['LAST_POST'] = $top_post;
						}
						$op['pages'][$key]['main'] = $forum_group;
					}
				}
			}
		}

		$op['forum_name'] = 'SCM 討論版';
	}

	page_display($op,$auth,$TPL_FORUM_ADMIN);
	break;



#
# 討論版管理
case "forum_manager_add":
$jobs = check_authority($auth,$job[4]);

$status = !empty($_POST['status'])?$_POST['status']:(!empty($_GET['status'])?$_GET['status']:'');
$main_id = !empty($_POST['main_id'])?$_POST['main_id']:(!empty($_GET['main_id'])?$_GET['main_id']:'');
$sort_id = !empty($_POST['sort_id'])?$_POST['sort_id']:(!empty($_GET['sort_id'])?$_GET['sort_id']:'');
$title = !empty($_POST['title'])?$_POST['title']:(!empty($_GET['title'])?$_GET['title']:'');
$content = !empty($_POST['content'])?$_POST['content']:(!empty($_GET['content'])?$_GET['content']:'');

if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")){
    if(!empty($new)){
        $op['title'] = $title;
        $op['status'] = $status;
        if($status=='main'){
            $op['htitle'] = '分區';
        }else{
            $op['htitle'] = '版面';
            $op['select_main'] = $Forum->select_main($main_id);
        }
        $op['forum_name'] = 'SCM 討論版';
        $op['pp'] = '版面：'.$forum_main[0]['title'].' \ 建立新'.$op['htitle'];
        page_display($op,$auth,$TPL_FORUM_MANAGER_ADD);
        break;
    }

    $parm = array(	'sort_id'		=>  $sort_id,
                                    'main_id'		=>  $main_id,
                                    'title'			=>  $title,
                                    'content'		=>  $content,
                                    'status'		=>  $status);
    $Forum->add($parm);

}
header ("Location: ".$PHP_SELF."?Ac=forum_manager");
break;



	#
	# 討論版管理
	case "forum_manager_update":
 	$jobs = check_authority($auth,$job[4]);

  $id = !empty($_POST['id'])?$_POST['id']:(!empty($_GET['id'])?$_GET['id']:'');
  $status = !empty($_POST['status'])?$_POST['status']:(!empty($_GET['status'])?$_GET['status']:'');
  $main_id = !empty($_POST['main_id'])?$_POST['main_id']:(!empty($_GET['main_id'])?$_GET['main_id']:'');
  $sort_id = !empty($_POST['sort_id'])?$_POST['sort_id']:(!empty($_GET['sort_id'])?$_GET['sort_id']:'');
  $new = !empty($_GET['new'])?$_GET['new']:'';
  $title = !empty($_POST['title'])?$_POST['title']:'';
  $content = !empty($_POST['content'])?$_POST['content']:'';

	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")){
		if(!empty($new)){
			$op['title'] = $title;
			$op['status'] = $status;
			$op['id'] = $id;
			if($status=='main'){
				$op['pages'] = $Forum->get_forum_main($id);
				$op['htitle'] = '分區';
			}else{
				$op['pages'] = $Forum->get_forum_group($id);
				$op['htitle'] = '版面';
				$op['select_main'] = $Forum->select_main($main_id);
			}
			$op['forum_name'] = 'SCM 討論版';
      $forum_main = $Forum->get_forum_main();
			$op['pp'] = '版面：'.$forum_main[0]['title'].' \ 編輯'.$op['htitle'];
			page_display($op,$auth,$TPL_FORUM_MANAGER_EDIT);
			break;
		}

	 	$parm = array(	'id'				=>  $id,
	 									'main_id'		=>  $main_id,
	 									'title'			=>  $title,
	 									'content'		=>  $content,
	 									'status'		=>  $status);
		$Forum->manager_update($parm);
	}

	header ("Location: ".$PHP_SELF."?Ac=forum_manager");
	break;


	#
	# 討論版管理
	case "forum_manager_del":
 	$jobs = check_authority($auth,$job[4]);
  
  $status = !empty($_GET['status'])?$_GET['status']:'';
  $id = !empty($_GET['id'])?$_GET['id']:'';
  
	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")){
		$Forum->manager_del($id,$status);
	}
	header ("Location: ".$PHP_SELF."?Ac=forum_manager");
	break;


	#
	# 搜尋討論版
	case "forum_search":
 	$jobs = check_authority($auth,$job[4]);
  
  $main_id = !empty($_POST['main_id'])?$_POST['main_id']:'';  
  $forum_text = !empty($_POST['forum_text'])?$_POST['forum_text']:'';  

	$op['select_main'] = $Forum->select_search();
	if(isset($forum_text))$_SESSION['TEMP']['forum_text'] = $forum_text;
	$op['text'] = $Forum->forum_search($main_id,$_SESSION['TEMP']['forum_text']);

	$op['forum_text'] = $_SESSION['TEMP']['forum_text'];
	$op['forum_name'] = 'SCM 討論版';
	$op['pp'] = '版面：文章搜尋';
	page_display($op,$auth,$TPL_FORUM_SEARCH);
	break;


	#
	# 分區設定
	case "forum_main_sort":
 	$jobs = check_authority($auth,$job[4]);
  
  $status = !empty($_GET['status'])?$_GET['status']:'';
  $id = !empty($_GET['id'])?$_GET['id']:'';
  
 	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")){
 		$Forum->set_forum_main($id,$status);
 	}
	header ("Location: ".$PHP_SELF."?Ac=forum_manager");
	break;

	#
	# 版面設定
	case "forum_group_sort":
 	$jobs = check_authority($auth,$job[4]);

  $status = !empty($_GET['status'])?$_GET['status']:'';
  $id = !empty($_GET['id'])?$_GET['id']:'';
  $main_id = !empty($_GET['main_id'])?$_GET['main_id']:'';
  
 	if(( !empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")){
 		$Forum->set_forum_group($id,$status,$main_id);
 	}
	header ("Location: ".$PHP_SELF."?Ac=forum_manager");
	break;

//-------------------------------------------------------------------------
}   // end case ---------

?>