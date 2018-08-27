<?php 
/*简单配置*/
$redirect='http://'.$_SERVER['HTTP_HOST'].'/'.'examplepage.php';/*登录后跳转页*/
$sessionname='bottle';/*session区分符*/
$allowreg=true;/*是否允许注册*/
/*配置结束*/
@session_start();
function deleteDir($dir)
    {
        if (!$handle = @opendir($dir)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
                $file = $dir . '/' . $file;
                if (is_dir($file)) {
                    deleteDir($file);
                } else {
                    @unlink($file);
                }
            }

        }
        @rmdir($dir);
    }
	  function grc($length){
   $str = null;
   $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz7823647^&*^&*^&(^GYGdghevghwevfghfvgrf_+KD:QW{D
   NUIGH^&S%$X%#$XWUXWJBXHWBXJHXHWBXHWJBJBXWJHVB';
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];
   }

   return $str;
  }  
function setprofile($usr,$pr,$ct){
	if(!file_exists(dirname(__FILE__) . '/../u/'.$usr.'/profile.php')){
		file_put_contents(dirname(__FILE__) . '/../u/'.$usr.'/profile.php','<?php $profiles=array();?>');
	}
	require dirname(__FILE__) . '/../u/'.$usr.'/profile.php';
	$profiles[$pr]=$ct;
	file_put_contents(dirname(__FILE__) . '/../u/'.$usr.'/profile.php','<?php $profiles='.var_export($profiles,true).';?>');
}
function removeprofile($usr,$pr){
	if(!file_exists(dirname(__FILE__) . '/../u/'.$usr.'/profile.php')){
		file_put_contents(dirname(__FILE__) . '/../u/'.$usr.'/profile.php','<?php $profiles=array();?>');
	}
	require dirname(__FILE__) . '/../u/'.$usr.'/profile.php';
	if(isset($profiles[$pr])){
	unset($profiles[$pr]);
	}
	file_put_contents(dirname(__FILE__) . '/../u/'.$usr.'/profile.php','<?php $profiles='.var_export($profiles,true).';?>');
}
function getprofile($usr,$pr){
	if(!file_exists(dirname(__FILE__) . '/../u/'.$usr.'/profile.php')){
		file_put_contents(dirname(__FILE__) . '/../u/'.$usr.'/profile.php','<?php $profiles=array();?>');
	}
	require dirname(__FILE__) . '/../u/'.$usr.'/profile.php';
	if(isset($profiles[$pr])){
	return $profiles[$pr];
	}else{
	return false;
	}
}
function deleteuser($usr){
	if(is_dir(dirname(__FILE__) . '/../u/'.$usr)){
		require dirname(__FILE__) . '/../u/indexes.php';
		unset($users[getid($usr)]);
		deleteDir(dirname(__FILE__) . '/../u/'.$usr);
		file_put_contents('./u/indexes.php','<?php $users='.var_export($users,true).';$unauth='.var_export($unauth,true).';?>');
	}else{
		return false;
	}
}
function getid($usr){
	require dirname(__FILE__) . '/../u/indexes.php';
	foreach($users as $k=>$v){
		if($v==$usr){
			return $k;
		}
	}
}
function getusr($id){
	require dirname(__FILE__) . '/../u/indexes.php';
	return $users[$id];
}
function checklogin(){
	global $sessionname;
	if(isset($_SESSION[$sessionname.'usr'])&&$_SESSION[$sessionname.'logged']=='yes'){
		return true;
	}else{
        return false;
	}	
}
function getnowusr(){
	global $sessionname;
	if(isset($_SESSION[$sessionname.'usr'])){
		return $_SESSION[$sessionname.'usr'];
	}
}
function setpass($up,$pw){
	if(is_dir(dirname(__FILE__) . '/../u/'.$up)){
		$salt=base64_encode(base64_encode(grc(64)).base64_encode(grc(64)).base64_encode(grc(64)).base64_encode(grc(64)).base64_encode(grc(64)));
		$pass=sha1(crypt(sha1(htmlspecialchars($pw)),$salt));
		file_put_contents('./u/'.$up.'/passport.php','<?php $authpass=\''.$pass.'\';$authsalt=\''.$salt.'\';?>');
	}else{
		return false;
	}
}
@session_write_close();
?>