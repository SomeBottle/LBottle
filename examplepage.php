<?php
require_once './api/api.php';
if(!checklogin()){
	header('Location: auth.php');
}
$user=getnowusr();
?>
<h1>这里是LBottle的示例页面</h1>
<h3>通过getnowusr()获得session用户</h3>
<h4>现在用户：<?php echo $user;?></h4>
<hr>
<h3>通过getusr(id)获得id对应的用户</h3>
<h4>ID为零的用户：<?php echo getusr(0);?></h4>
<hr>
<h3>通过getid(usr)获得用户对应的id</h3>
<h4>现在用户：<?php echo getid($user);?></h4>
<hr>
<h3>通过deleteuser(usr)删除用户</h3>
<hr>
<h3>通过checklogin()判断是否登录</h3>
<h4>返回bool值</h4>
<hr>
<h3>通过setprofile(user,property,content),removeprofile(user,property),getprofile(user,property)</h3>
<h4>来设置、移除、获取用户的属性</h4>
<hr>
<h3>通过访问auth.php?reset</h3>
<h4>来重设密码（在登陆后）</h4>
<p><a href='auth.php?reset'>重设密码</a></p>
<hr>
<h3>通过访问auth.php?logout</h3>
<h4>来登出↓</h4>
<p><a href='auth.php?logout'>登出</a></p>
<hr>