<?php
require_once './config.php';
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
	if(isset($_SESSION[sesname().'user'])&&$_SESSION[sesname().'iflogin']=='logged'){
		return true;
	}else{
        return false;
	}	
}
function getnowusr(){
	if(isset($_SESSION[sesname().'user'])){
		return $_SESSION[sesname().'user'];
	}
}
@session_write_close();
?>