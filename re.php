<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set("Asia/Shanghai");
require_once './config.php';
require_once './api/api.php';
session_start();
if ($_GET['logout'] == 'true') {
    session_destroy();
    header('Location: auth.php');
}
session_write_close();
header('Content-type:text/json;charset=utf-8');
if (!is_dir('./u')) {
    mkdir('./u');
}
if (!file_exists('./u/indexes.php')) {
    file_put_contents('./u/indexes.php', '<?php $users=array();$unauth=array();?>');
}
if (!file_exists('./u/ips.php')) {
    file_put_contents('./u/ips.php', '<?php $ips=array();?>');
}
$ntime = date('Y-m-d H:i:s', time());
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
function tc($s, $e) {
    $hour = floor((strtotime($e) - strtotime($s)) % 86400 / 3600);
    $minute = floor((strtotime($e) - strtotime($s)) % 86400 / 60);
    $second = floor((strtotime($e) - strtotime($s)) % 86400 % 60);
    return $hour * 3600 + $minute * 60 + $second;
}
function checkunauth() {
    require './u/indexes.php';
    foreach ($unauth as $k => $v) {
        foreach ($v as $key => $val) {
            if ($key == 'time') {
                if (abs(tc($val, $ntime)) >= 60) {
                    unset($unauth[$k]);
                    deleteDir('./u/' . $k);
                }
            }
        }
    }
    file_put_contents('./u/indexes.php', '<?php $users=' . var_export($users, true) . ';$unauth=' . var_export($unauth, true) . ';?>');
}
checkunauth(); /*检查没有验证的水军用户*/
$r['r'] = '';
$r['m'] = '';
$do = @$_POST['type'];
$usr = @$_POST['u'];
$ps = @$_POST['pass'];
$ps = htmlspecialchars($ps);
require_once './u/ips.php';
foreach ($ips as $k => $val) {
    if (tc($val['time'], $ntime) >= 10) {
        unset($ips[$k]);
    }
}
if (!isset($ips[getip(false) ])) {
    $ips[getip(false) ]['ts'] = ipcool();
    $ips[getip(false) ]['time'] = $ntime;
} else {
    $ips[getip(false) ]['ts'] = intval($ips[getip(false) ]['ts']) - 1;
    $ips[getip(false) ]['time'] = $ntime;
}
file_put_contents('./u/ips.php', '<?php $ips=' . var_export($ips, true) . ';?>');
if (intval($ips[getip(false) ]['ts']) > 0) {
    if ($do == 'cu') {
        if (!file_exists('./u/' . $usr)) {
            $r['r'] = 'none';
        } else {
            $r['r'] = 'exist';
        }
    } else if ($do == 'rauth') {
        $mode = '';
        if (!file_exists('./u/' . $usr)) {
            $mode = 'reg';
        } else {
            $mode = 'log';
        }
        $r['mo'] = $mode;
        if (strlen($usr) >= 4 && strlen($usr) <= usermax() && !preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $usr) && !is_numeric($usr)) {
            if (!preg_match('/[\x{4e00}-\x{9fa5}]/u', $usr)) {
                if (strlen($ps) >= 4 && strlen($ps) <= passmax()) {
                    /*开始注册*/
                    if ($mode == 'log') { /*登录模式*/
                        /*同样的分割密码*/
                        $pslen = strlen($ps);
                        $spnum = ceil($pslen / pssplit());
                        $psa = str_split($ps, $spnum);
                        $tnum = 0;
                        $query = 0;
                        $passo = array();
                        $passn = array();
                        require './u/' . $usr . '/auth.php';
                        $realpp = count($passauth);
                        if ($passpnum !== $realpp) {
                            $passpnum = $realpp;
                        }
                        $passpnum-= 1;
                        while ($tnum <= $passpnum) {
                            $anum = 0;
                            while ($anum <= ($passpnum + 1)) {
                                $snum = 0;
                                $rps = sha1(sha1($psa[$tnum]) . $passsalt[$anum]);
                                while ($snum <= $passpnum) {
                                    if ($rps == $passauth[$snum]) {
                                        $passo[$snum] = $rps;
                                        $passn[$snum] = $tnum;
                                        $query = $query + 1;
                                    }
                                    $snum+= 1;
                                }
                                $anum+= 1;
                            }
                            $tnum+= 1;
                        }
                        asort($passn);
                        $fpass = '';
                        $dpass = '';
                        foreach ($passn as $val) {
                            $fpass = $fpass . $passo[$val];
                        }
                        foreach ($passauth as $val) {
                            $dpass = $dpass . $val;
                        }
                        if ($dpass == $fpass && $query == ($passpnum + 1) && strlen($ps) == $passlen) { /*登录成功*/
                            require './u/indexes.php';
                            $r['r'] = 'success';
                            $r['m'] = logsuc();
                            if (in_array($usr, $unauth)) {
                                $r['ifauth'] = 'unauth';
                            } else { /*已验证*/
                                $r['ifauth'] = 'authed';
                                session_start();
                                $_SESSION[sesname() . 'user'] = $usr;
                                $_SESSION[sesname() . 'iflogin'] = 'logged';
                                session_write_close();
                                setprofile($usr, 'logip', getip(true));
                                setprofile($usr, 'logtime', $ntime);
                                $r['t'] = getip(false);
                                $r['rd'] = rdpath();
                            }
                        } else { /*账号或者密码错误*/
                            $r['r'] = 'denied';
                            $r['m'] = incpass();
                        }
                    } else if ($mode == 'reg') { /*注册模式*/
                        mkdir('./u/' . $usr);
                        $pslen = strlen($ps);
                        $spnum = ceil($pslen / pssplit());
                        $psarray = str_split($ps, $spnum);
                        $stnum = 1;
                        $st = array();
                        $psw = array();
                        foreach ($psarray as $k => $v) {
                            $salt = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)) . base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)) . base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)); /*加三层盐*/
                            $st[$stnum] = $salt;
                            $psw[$stnum] = sha1(sha1($v) . $salt);
                            $stnum+= 1;
                        }
                        shuffle($psw); /*彻底打乱密码*/
                        $fstr = '<?php $passlen=' . strlen($ps) . ';$passpnum=' . pssplit() . ';$passauth=' . var_export($psw, true) . ';$passsalt=' . var_export($st, true) . ';?>';
                        file_put_contents('./u/' . $usr . '/auth.php', $fstr);
                        require './u/indexes.php';
                        $unauth[$usr]['time'] = $ntime;
                        $unauth[$usr]['m'] = getip(false);
                        file_put_contents('./u/indexes.php', '<?php $users=' . var_export($users, true) . ';$unauth=' . var_export($unauth, true) . ';?>');
                        $r['t'] = getip(false);
                        $r['r'] = 'success';
                        $r['m'] = regsuc();
                    }
                } else {
                    $r['m'] = passnt();
                }
            } else {
                $r['m'] = usercnt();
            }
        } else {
            $r['m'] = usernt();
        }
    } else if ($do == 'reset') {
        @session_start();
        if (checklogin()) {
            $usr = $_SESSION[sesname() . 'user'];
            $r['r'] = 'success';
            $r['m'] = resetsuc();
            $pslen = strlen($ps);
            $spnum = ceil($pslen / pssplit());
            $psarray = str_split($ps, $spnum);
            $stnum = 1;
            $st = array();
            $psw = array();
            foreach ($psarray as $k => $v) {
                $salt = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)) . base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)) . base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_RANDOM)); /*加三层盐*/
                $st[$stnum] = $salt;
                $psw[$stnum] = sha1(sha1($v) . $salt);
                $stnum+= 1;
            }
            shuffle($psw); /*彻底打乱密码*/
            $fstr = '<?php $passlen=' . strlen($ps) . ';$passpnum=' . pssplit() . ';$passauth=' . var_export($psw, true) . ';$passsalt=' . var_export($st, true) . ';?>';
            file_put_contents('./u/' . $usr . '/auth.php', $fstr);
            @session_destroy();
        } else {
            $r['m'] = notlog();
            $r['r'] = 'failed';
        }
        @session_write_close();
    }
} else { /*IP请求太多了*/
    $r['m'] = ipmaxw();
}
echo json_encode($r, true);
?>