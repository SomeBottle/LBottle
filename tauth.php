<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("Asia/Shanghai");
header('Content-type:text/json;charset=utf-8');
$ntime = date('Y-m-d H:i:s', time());
$r['r'] = '';
$r['error'] = '';
$email = $_POST['em'];
require_once './api/api.php';
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
if ($_SESSION['saltverifys'] == 'success') {
    $md = $_SESSION['verifymd'];
    $query = 0;
    if (strval($md) == strval(getip(false))) {
        require './u/indexes.php';
        foreach ($unauth as $k => $v) {
            foreach ($v as $val) {
                if ($val == $md) {
                    if (stripos($email, '@') !== false && stripos($email, '.') !== false) {
                        if (abs(tc($unauth[$k]['time'], $ntime)) <= 60) {
                            unset($unauth[$k]);
                            $usernum = count($users);
                            $users[$usernum] = $k;
                            setprofile($k, 'name', $k);
                            setprofile($k, 'md', $val);
                            setprofile($k, 'regtime', $ntime);
                            setprofile($k, 'regip', getip(true));
                            $r['r'] = 'success';
                            $query+= 1;
                        } else {
                            unset($unauth[$k]);
                            deleteDir('./u/' . $k);
                            $r['r'] = 'failed';
                            $r['m'] = '验证超时(60s)';
                        }
                    } else {
                        $r['r'] = 'failed';
                        $r['m'] = '邮箱格式有问题QAQ';
                        $r['error'] = 'email';
                    }
                }
            }
        }
        if ($query !== 1) {
            $r['r'] = 'failed';
            $r['m'] = '验证超时(60s)';
        }
        file_put_contents('./u/indexes.php', '<?php $users=' . var_export($users, true) . ';$unauth=' . var_export($unauth, true) . ';?>');
    } else { /*ip不匹配*/
        $r['r'] = 'failed';
        $r['m'] = '验证IP不匹配';
    }
}
echo json_encode($r, true);
session_write_close();
?>