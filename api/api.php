<?php
/*简单配置*/
$redirect = 'http://' . $_SERVER['HTTP_HOST'] . '/' . 'examplepage.php'; /*登录后跳转页*/
$sessionname = 'bottle'; /*session区分符*/
$allowreg = true; /*是否允许注册*/
$mailauth = false; /*是否开启邮件验证*/
$ipaccesstime = 8; /*ip每十秒访问次数的限制*/
$smtpserver = "smtp.qq.com"; //SMTP服务器
$smtpserverport = 25; //SMTP服务器端口
$smtpusermail = "xxxx@qq.com"; //SMTP服务器的用户邮箱
$smtpuser = "xxxx@qq.com"; //SMTP服务器的用户帐号
$smtppass = "xxxxxxxxx"; //SMTP服务器的用户密码
$mailtitle = '注册邮箱验证=w='; //注册邮件标题
/*配置结束*/
@session_start();
function deleteDir($dir) {
    if (!$handle = @opendir($dir)) {
        return false;
    }
    while (false !== ($file = readdir($handle))) {
        if ($file !== "." && $file !== "..") { //排除当前目录与父级目录
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
function grc($length) {
    $str = null;
    $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz7823647^&*^&*^&(^GYGdghevghwevfghfvgrf_+KD:QW{D
   NUIGH^&S%$X%#$XWUXWJBXHWBXJHXHWBXHWJBJBXWJHVB';
    $max = strlen($strPol) - 1;
    for ($i = 0;$i < $length;$i++) {
        $str.= $strPol[rand(0, $max) ];
    }
    return $str;
}
function setprofile($usr, $pr, $ct) {
    if (!file_exists(dirname(__FILE__) . '/../u/' . $usr . '/profile.php')) {
        file_put_contents(dirname(__FILE__) . '/../u/' . $usr . '/profile.php', '<?php $profiles=array();?>');
    }
    require dirname(__FILE__) . '/../u/' . $usr . '/profile.php';
    $profiles[$pr] = $ct;
    file_put_contents(dirname(__FILE__) . '/../u/' . $usr . '/profile.php', '<?php $profiles=' . var_export($profiles, true) . ';?>');
}
function sendcode($maila, $cd) {
    global $mailtitle;
    require dirname(__FILE__) . '/email.class.php';
    sendmailto($maila, $mailtitle, '<h2>你的注册验证链接在ze里：</h2><p><a href=\'' . $cd . '\' target=\'_blank\'>点我继续注册</a></p><h3>什么！？没法点击</h3><p>那就复制下面的内容到地址栏吧~</p><p>' . $cd . '</p>');
}
function removeprofile($usr, $pr) {
    if (!file_exists(dirname(__FILE__) . '/../u/' . $usr . '/profile.php')) {
        file_put_contents(dirname(__FILE__) . '/../u/' . $usr . '/profile.php', '<?php $profiles=array();?>');
    }
    require dirname(__FILE__) . '/../u/' . $usr . '/profile.php';
    if (isset($profiles[$pr])) {
        unset($profiles[$pr]);
    }
    file_put_contents(dirname(__FILE__) . '/../u/' . $usr . '/profile.php', '<?php $profiles=' . var_export($profiles, true) . ';?>');
}
function getprofile($usr, $pr) {
    if (!file_exists(dirname(__FILE__) . '/../u/' . $usr . '/profile.php')) {
        file_put_contents(dirname(__FILE__) . '/../u/' . $usr . '/profile.php', '<?php $profiles=array();?>');
    }
    require dirname(__FILE__) . '/../u/' . $usr . '/profile.php';
    if (isset($profiles[$pr])) {
        return $profiles[$pr];
    } else {
        return false;
    }
}
function deleteuser($usr) {
    if (is_dir(dirname(__FILE__) . '/../u/' . $usr)) {
        require dirname(__FILE__) . '/../u/indexes.php';
        unset($users[getid($usr) ]);
        deleteDir(dirname(__FILE__) . '/../u/' . $usr);
        file_put_contents('./u/indexes.php', '<?php $users=' . var_export($users, true) . ';$unauth=' . var_export($unauth, true) . ';?>');
    } else {
        return false;
    }
}
function getid($usr) {
    require dirname(__FILE__) . '/../u/indexes.php';
    foreach ($users as $k => $v) {
        if ($v == $usr) {
            return $k;
        }
    }
}
function getusr($id) {
    require dirname(__FILE__) . '/../u/indexes.php';
    return $users[$id];
}
function checklogin() {
    global $sessionname;
    if (isset($_SESSION[$sessionname . 'usr']) && $_SESSION[$sessionname . 'logged'] == 'yes') {
        return true;
    } else {
        return false;
    }
}
function getnowusr() {
    global $sessionname;
    if (isset($_SESSION[$sessionname . 'usr'])) {
        return $_SESSION[$sessionname . 'usr'];
    }
}
function sendmailto($mailto, $mailsub, $mailbd) {
    global $smtpserver;
    global $smtpserverport;
    global $smtpusermail;
    global $smtpuser;
    global $smtppass;
	$smtpemailto = $mailto;
    $mailsubject = $mailsub;
    $mailsubject = "=?UTF-8?B?" . base64_encode($mailsubject) . "?=";
    $mailbody = $mailbd;
    $mailtype = "HTML";
    $smtp = new smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);
    $smtp->debug = FALSE;
    $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
}
function setpass($up, $pw) {
    if (is_dir(dirname(__FILE__) . '/../u/' . $up)) {
        $salt = base64_encode(base64_encode(grc(64)) . base64_encode(grc(64)) . base64_encode(grc(64)) . base64_encode(grc(64)) . base64_encode(grc(64)));
        $pass = sha1(crypt(sha1(htmlspecialchars($pw)), $salt));
        file_put_contents('./u/' . $up . '/passport.php', '<?php $authpass=\'' . $pass . '\';$authsalt=\'' . $salt . '\';?>');
    } else {
        return false;
    }
}
@session_write_close();
?>