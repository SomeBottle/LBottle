<?php
$o['title']='Sign.';/*标题*/
$o['userph']='昵称';/*用户名Placeholder*/
$o['passph']='*********';/*密码Placeholder*/
$o['btnlog']='登录';/*按钮文字*/
$o['btnreg']='注册';
$o['btndef']='穿越！';
$o['passmax']=16;/*密码最多位数*/
$o['usermax']=12;/*用户名最多位数*/
$o['usernotice']='昵称长度要求(4~'.usermax().')，不允许为纯数字或者有特殊字符哦~';/*用户名验证不过反馈*/
$o['passnotice']='密码长度有要求(4~'.passmax().')哦~';/*密码验证不过反馈*/
$o['usercnnotice']='昵称不支持中文呢OAO.';/*用户名不支持中文反馈*/
$o['userauthpass']='搬运箱子中...嘿咻~';/*验证中反馈*/
$o['passsplit']=4;/*密码分割成多少段保存，太高了不安全且计算缓慢*/
$o['logsuccess']='登录成功(๑•̀ㅂ•́)و✧';/*登陆成功提示*/
$o['regsuccess']='注册成功，前往验证~';/*注册成功提示*/
$o['ipcooldown']=15;/*10秒内同一ip能调用的次数*/
$o['ipmaxwarn']='兄dei！停一停火，请求太多！';
$o['redirect']='http://'.$_SERVER['HTTP_HOST'].'/'.'examplepage.php';/*在域名根目录下的文件目录*/
$o['passincorrect']='账号或密码错误~';/*密码错误提示*/
$o['sessiond']='bottle';/*SESSION同域分割*/
$o['resetpass']='请输入要重设的密码';/*密码重设Placeholder*/
$o['resetpassbtn']='重设！';/*密码重设btn*/
$o['successreset']='重设成功！';/*密码重设成功*/
$o['notlog']='你还没有登录呢！';/*未登录*/

/*函数*/
function title(){
	global $o;
	return $o['title'];
}
function userph(){
	global $o;
	return $o['userph'];
}
function passph(){
	global $o;
	return $o['passph'];
}
function btnlog(){
	global $o;
	return $o['btnlog'];
}
function btnreg(){
	global $o;
	return $o['btnreg'];
}
function btndef(){
	global $o;
	return $o['btndef'];
}
function passmax(){
	global $o;
	return $o['passmax'];
}
function usermax(){
	global $o;
	return $o['usermax'];
}
function passnt(){
	global $o;
	return $o['passnotice'];
}
function usernt(){
	global $o;
	return $o['usernotice'];
}
function usercnt(){
	global $o;
	return $o['usercnnotice'];
}
function userapass(){
	global $o;
	return $o['userauthpass'];
}
function pssplit(){
	global $o;
	return $o['passsplit'];
}
function logsuc(){
	global $o;
	return $o['logsuccess'];
}
function regsuc(){
	global $o;
	return $o['regsuccess'];
}
function incpass(){
	global $o;
	return $o['passincorrect'];
}
function ipcool(){
	global $o;
	return $o['ipcooldown'];
}
function ipmaxw(){
	global $o;
	return $o['ipmaxwarn'];
}
function sesname(){
	global $o;
	return $o['sessiond'];
}
function rdpath(){
	global $o;
	return $o['redirect'];
}
function resetpass(){
	global $o;
	return $o['resetpass'];
}
function resetbtn(){
	global $o;
	return $o['resetpassbtn'];
}
function notlog(){
	global $o;
	return $o['notlog'];
}
function resetsuc(){
	global $o;
	return $o['successreset'];
}
?>