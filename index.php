<?php
set_time_limit(30);
ignore_user_abort(true);
require_once './api/api.php';
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("Asia/Shanghai");
$setmail = false;
$sendmail = false;
$rq = explode('?', $_SERVER['REQUEST_URI']) [1];
$rq = explode('&', $rq) [0];
if (empty($rq)) {
    unset($_SESSION[$sessionname . 'mailcode']);
    unset($_SESSION[$sessionname . 'reguser']);
    unset($_SESSION[$sessionname . 'regpass']);
    unset($_SESSION[$sessionname . 'regmail']);
    unset($_SESSION[$sessionname . 'regprocess']);
}
if ($rq !== 'reset' && $rq !== 'logout') {
    if (checklogin()) {
        header('Location: ' . $redirect);
    }
}
if ($rq == 'logout') {
    @session_start();
    @session_destroy();
    @session_write_close();
    header('Location: ?');
}
$u = $_POST['u'];
$maillink = $_GET['code'];
$p = $_POST['p'];
$v = $_POST['v'];
$e = $_POST['e'];
$email = $_POST['em'];
if (isset($_SESSION[$sessionname . 'reguser']) && isset($_SESSION[$sessionname . 'regpass']) && isset($_SESSION[$sessionname . 'regmail'])) {
    $u = $_SESSION[$sessionname . 'reguser'];
    $p = $_SESSION[$sessionname . 'regpass'];
    $email = $_SESSION[$sessionname . 'regmail']; /*从服务端取出注册信息*/
}
$msg = '';
$ntime = date('Y-m-d H:i:s', time());
function tc($s, $e) {
    $hour = floor((strtotime($e) - strtotime($s)) % 86400 / 3600);
    $minute = floor((strtotime($e) - strtotime($s)) % 86400 / 60);
    $second = floor((strtotime($e) - strtotime($s)) % 86400 % 60);
    return $hour * 3600 + $minute * 60 + $second;
}
function getip($i) {
    global $ip;
    if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";
    if (!$i) {
        $ip = md5($ip);
    }
    return $ip;
}
if ($rq == 'verify') {
    @session_start();
    if (!is_dir('./u')) {
        mkdir('./u');
    }
    /*刷新IP库*/
    if (!file_exists('./u/ips.php')) {
        file_put_contents('./u/ips.php', '<?php $denys=array();$ips=array();?>');
    }
    if (!file_exists('./u/indexes.php')) {
        file_put_contents('./u/indexes.php', '<?php $users=array();$emails=array();?>');
    }
    require_once './u/ips.php';
    require_once './u/indexes.php';
    foreach ($ips as $k => $val) {
        if (tc($val['time'], $ntime) >= 10) {
            unset($ips[$k]);
        }
    }
    if (!isset($ips[getip(false) ])) {
        $ips[getip(false) ]['ts'] = $ipaccesstime; /*IP每10秒最多请求次数*/
        $ips[getip(false) ]['time'] = $ntime;
    } else {
        $ips[getip(false) ]['ts'] = intval($ips[getip(false) ]['ts']) - 1;
        $ips[getip(false) ]['time'] = $ntime;
    }
    file_put_contents('./u/ips.php', '<?php $denys=' . var_export($denys, true) . ';$ips=' . var_export($ips, true) . ';?>');
    /*刷新IP库完毕*/
    if (intval($ips[getip(false) ]['ts']) > 0) {
        if ($_SESSION['verifycode'] == sha1($v) || $_SESSION[$sessionname . 'regprocess'] == 'yes') {
            if (is_dir('./u/' . $u) && $_SESSION[$sessionname . 'regprocess'] !== 'yes') {
                /*登录*/
                if (file_exists('./u/' . $u . '/passport.php')) {
                    require './u/' . $u . '/passport.php';
                    $pass = sha1(crypt(sha1(htmlspecialchars($p)), $authsalt));
                    if ($authpass == $pass) {
                        $msg = '登录成功~' . $sessionname;
                        setprofile($u, 'logtime', $ntime);
                        setprofile($u, 'logip', getip(true));
                        $_SESSION[$sessionname . 'usr'] = $u;
                        $_SESSION[$sessionname . 'logged'] = 'yes';
                        header('Location: ' . $redirect);
                    } else {
                        $msg = '账号密码不匹配。';
                    }
                } else {
                    $msg = '账号密码不匹配。';
                }
            } else { /*最长用户名限制*/
                if ($allowreg) {
                    if (strlen($u) >= 4 && strlen($u) <= 14 && !preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $u) && !is_numeric($u)) {
                        if (!preg_match('/[\x{4e00}-\x{9fa5}]/u', $u)) {
                            /*限制密码长度*/
                            if (strlen($p) >= 4 && strlen($p) <= 16) {
                                if (!in_array(getip(false), $denys)) {
									if(!array_key_exists($email,$emails)){
                                    if ($mailauth && empty($maillink)) { /*是否开启邮箱验证*/
                                        if (empty($email)) {
                                            $msg = '您正在注册，请补充邮箱';
                                            $setmail = true;
                                        } else {
                                            $sendmail = true;
                                            $msg = '<p>邮件已经发送</p><p>请点击邮件中的链接以继续注册</p>';
                                            $authcode = base64_encode(grc(48));
                                            $authlink = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] . '&code=' . $authcode;
                                            $_SESSION[$sessionname . 'mailcode'] = md5(base64_decode($authcode));
                                            $_SESSION[$sessionname . 'reguser'] = $u;
                                            $_SESSION[$sessionname . 'regpass'] = $p;
                                            $_SESSION[$sessionname . 'regmail'] = $email; /*注册信息临时存入服务端*/
                                            $_SESSION[$sessionname . 'regprocess'] = 'yes';
                                            //@fastcgi_finish_request(); /*邮件发送转后台执行*/
                                            sendcode($email, $authlink);
                                        }
                                    } else {
                                        if (md5(base64_decode($maillink)) == $_SESSION[$sessionname . 'mailcode'] || !$mailauth) {
                                            /*注册*/
                                            $salt = base64_encode(base64_encode(grc(64)) . base64_encode(grc(64)) . base64_encode(grc(64)) . base64_encode(grc(64)) . base64_encode(grc(64)));
                                            $pass = sha1(crypt(sha1(htmlspecialchars($p)), $salt));
                                            mkdir('./u/' . $u);
                                            file_put_contents('./u/' . $u . '/passport.php', '<?php $authpass=\'' . $pass . '\';$authsalt=\'' . $salt . '\';?>');
                                            $msg = '成功注册！';
                                            $anum = count($denys);
                                            /*禁止IP多次注册*/
                                            $denys[$anum] = getip(false);
                                            file_put_contents('./u/ips.php', '<?php $denys=' . var_export($denys, true) . ';$ips=' . var_export($ips, true) . ';?>');
                                            /*存入用户序列*/
                                            setprofile($u, 'name', $u);
                                            setprofile($u, 'md', getip(false));
                                            setprofile($u, 'regtime', $ntime);
                                            setprofile($u, 'email', $email);
                                            setprofile($u, 'regip', getip(true));
											require './api/afterreg.php';
                                            $usernum = count($users);
                                            $users[$usernum] = $u;
											if(!empty($email)){
											$emails[$email]=$u;
											}
                                            $p = '';
                                            $u = '';
                                            $e = '';
                                            $email = '';
                                            file_put_contents('./u/indexes.php', '<?php $users=' . var_export($users, true) . ';$emails='.var_export($emails,true).';?>');
                                        } else {
                                            $msg = '邮箱验证失败，请重新注册.';
                                        }
                                        unset($_SESSION[$sessionname . 'mailcode']);
                                        unset($_SESSION[$sessionname . 'reguser']);
                                        unset($_SESSION[$sessionname . 'regpass']);
                                        unset($_SESSION[$sessionname . 'regmail']);
                                        unset($_SESSION[$sessionname . 'regprocess']);
                                    }
									}else{
										$msg = '邮箱已经存在OAQ';
									}
                                } else {
                                    $msg = '不允许多次注册。';
                                }
                            } else {
                                $msg = '密码限制4-16个字符';
                            }
                        } else {
                            $msg = '用户名不支持中文~';
                        }
                    } else {
                        $msg = '用户名限制4-14个字符，不允许纯数字和特殊符号~';
                    }
                } else {
                    $msg = '=A=注册被关闭了~';
                }
            }
        } else {
            $msg = '验证码不对诶，已经帮你重置了。';
        }
    } else {
        $msg = '请求过多。';
    }
    @session_write_close();
} else if ($rq == 'reset') {
    /*密码长度限制*/
    if (!empty($p)) {
        if ($_SESSION['verifycode'] == sha1($v)) {
            if (strlen($p) >= 4 && strlen($p) <= 16) {
                if (checklogin()) {
                    setpass(getnowusr(), $p);
                    @session_start();
                    @session_destroy();
                    @session_write_close();
                    header('Location: ?');
                    $msg = '密码修改成功！';
                } else {
                    $msg = '请登录后再修改密码哦~';
                }
            } else {
                $msg = '密码限制4-16个字符';
            }
        } else {
            $msg = '验证码不对诶，已经帮你重置了。';
        }
    }
}
?>
<style>body{font-family:'\5FAE\8F6F\96C5\9ED1';max-width:500px;margin:0 auto;text-align:center;}</style>
<head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
	  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
	  <title>Auth.</title>
</head>
<body>
<style>
input[type="text"],input[type="password"]{transition: 1s ease;font-family:'\5FAE\8F6F\96C5\9ED1';color:#a4a4a4;text-align:center;border:0;background-color:#e6e6e6;width:100%;max-width:350px;border-radius:10px;padding:14px 10px 14px 10px;font-size:30px;outline:0}input[type="submit"]{transition: 1s ease;cursor:hand;margin:0 auto;font-family:'\5FAE\8F6F\96C5\9ED1';display:block;-webkit-appearance: none;color:#f2f2f2;text-align:center;border:0;background-color:#0080ff;width:auto;border-radius:10px;padding:10px 10px 10px 10px;font-size:30px;outline:0}
</style>
<h1 style="text-align:center;margin-top:20px;">Auth.</h1>
<h3><?php if (!empty($msg)) {
    echo $msg;
}; ?></h3>
<div id='co' style='opacity:0;'>
<?php if ($rq !== 'reset') { ?>
<form action='?verify' method='post'>
<p><input type='text' placeholder='用户名' name='u' value='<?php echo $u; ?>'></input></p>
<?php if ($setmail) { ?>
<p><input type='text' placeholder='邮箱@' name='em' value='<?php echo $email; ?>'></input></p>
<?php
    }; ?>
<p><input type='password' placeholder='密码' name='p' value='<?php echo $p; ?>'></input></p>
<p><img src='./vc.php?<?php echo rand(1, 2000); ?>' style='max-width:300px;width:100%;'></img></p>
<p><input type='text' style='font-size:10px;' placeholder='按照箭头顺序输入四至六位验证码' name='v'></input></p>
<p><input type='submit' value='Come On!'></input></p>
</form>
<?php
} else { ?>
<form action='?reset' method='post'>
<p><input type='password' placeholder='你想修改的密码' name='p' value='<?php echo $p; ?>'></input></p>
<p><img src='./vc.php?<?php echo rand(1, 2000); ?>' style='max-width:300px;width:100%;'></img></p>
<p><input type='text' style='font-size:10px;' placeholder='按照箭头顺序输入四至六位验证码' name='v'></input></p>
<p><input type='submit' value='Come On!'></input></p>
</form>
<?php
} ?>
<p><a href='javascript:void(0);' onclick='goback()' style='color:#AAA;'>返回</a></p>
</div>
</body>
<script>$('#co').animate({opacity:'1'},1000);function goback(){window.history.go(-1);}</script>