<?php
session_start();
header('Content-type:text/json;charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("Asia/Shanghai");
$ntime = date('Y-m-d H:i:s', time());
$t = $_POST['type'];
$md = $_POST['m'];
$r['r'] = '';
$pos = @$_POST['ms'];
$codes = $_POST['code'];
function tc($s, $e) {
    $hour = floor((strtotime($e) - strtotime($s)) % 86400 / 3600);
    $minute = floor((strtotime($e) - strtotime($s)) % 86400 / 60);
    $second = floor((strtotime($e) - strtotime($s)) % 86400 % 60);
    return $hour * 3600 + $minute * 60 + $second;
}
function getip() {
    global $ip;
    if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";
    return md5($ip);
}
if ($t == 'getauth') {
    if (getip() == $md || $_SESSION['verifymd'] == $md) {
        $_SESSION['verifymd'] = $md;
        unset($_SESSION['pos']);
        $r['r'] = 'success';
    } else {
        $r['r'] = 'failed';
    }
} else if ($t == 'startv') {
    if (!isset($_SESSION['verifytime'])) {
        $_SESSION['verifytime'] = $ntime;
    }
    $_SESSION['saltverifys'] = 'notsuccess';
    if (!isset($_SESSION['pos'])) {
        $_SESSION['pos'][1] = $pos;
        $_SESSION['pos']['num'] = 1;
    } else {
        if (intval($_SESSION['pos']['num']) <= 6) { /*减少处理次数*/
            $_SESSION['pos']['num'] = intval($_SESSION['pos']['num']) + 1;
            $_SESSION['pos'][$_SESSION['pos']['num']] = $pos;
            $r['r'] = $_SESSION['pos']['num'];
        } else {
            $r['r'] = 'failed';
        }
    }
} else if ($t == 'countv') {
    if (tc($_SESSION['verifytime'], $ntime) >= 1 && tc($_SESSION['verifytime'], $ntime) < 7) {
        unset($_SESSION['verifytime']);
        $total = intval($_SESSION['pos']['num']);
        if ($total < 2) { /*防止出bug*/
            $total = 2;
        }
        $st = 1;
        $xz = array();
        $yz = array(); /*定义x轴y轴数组*/
        while ($st <= ($total - 1)) {
            $stt = $st + 1;
            $ma = explode('|', $_SESSION['pos'][$st]);
            if (isset($_SESSION['pos'][$stt])) {
                $ma2 = explode('|', $_SESSION['pos'][$stt]);
            }
            if (!empty($ma2[0]) && !empty($ma2[1])) {
                $xz[$st] = abs(intval($ma2[0])) - abs(intval($ma[0]));
                $yz[$st] = abs(intval($ma2[1])) - abs(intval($ma[1]));
            } else {
                $xz[$st] = abs(intval($ma[0]));
                $yz[$st] = abs(intval($ma[1]));
            }
            $st+= 1;
        }
        $xtotal = 0;
        $ytotal = 0;
        foreach ($xz as $val) {
            $xtotal = $xtotal + intval($val);
        }
        foreach ($yz as $val) {
            $ytotal = $ytotal + intval($val);
        }
        $alenx = count($xz);
        $aleny = count($yz);
        $second = intval($_SESSION['pos']['num']);
        /*求x,y平均速度*/
        $unifx = abs(round($xtotal / ($total - 1)));
        $unify = abs(round($ytotal / ($total - 1)));
        /*求合平均速度*/;
        $uniftotal = round(sqrt(pow($unifx, 2) + pow($unify, 2)));
        /*求平均加速度Acceleration*/
        $accx = abs(round(($xz[$alenx] - $xz[1]) / $second));
        $accy = abs(round(($yz[$aleny] - $yz[1]) / $second));
        if (empty($accx) || empty($accy)) { /*防止加速度是空值*/
            $accx = abs(round(($xz[$alenx] * 2) / pow($second, 2)));
            $accy = abs(round(($yz[$aleny] * 2) / pow($second, 2)));
        }
        /*对比*/
        $succount = 0;
        if ($uniftotal - $unifx < 100 && !empty($unifx)) { /*总平均速度与横轴的相差不过100*/
            $succount+= 1;
        }
        if ($uniftotal - $unify < 100 && !empty($unify)) { /*总平均速度与纵轴的相差不过100*/
            $succount+= 1;
        }
        if ($accx <= round($uniftotal / 2)) { /*横轴加速度小于总平均速度的一半*/
            $succount+= 1;
        }
        if ($uniftotal > $unify) { /*总平均速度大于纵轴平均速度*/
            $succount+= 1;
        }
        if (abs($accx - $accy) <= 280) { /*两加速度相减值区间*/
            $succount+= 1;
        }
        if (!empty($unifx) && !empty($unify)) { /*两加速度相减值区间*/
            $succount+= 1;
        }
        if ($succount >= 5) {
            $_SESSION['saltverifys'] = 'success';
            $r['r'] = 'success';
        } else {
            $r['r'] = 'failed';
        }
    } else { /*请求太快或太慢*/
        unset($_SESSION['verifytime']);
        $r['r'] = 'failed';
    }
} else if ($t == 'vrcode') {
    if (strval($codes) == strval($_SESSION['verifycode'])) {
        $_SESSION['saltverifys'] = 'success';
        $r['r'] = 'success';
    } else {
        $r['r'] = 'failed';
    }
}
session_write_close();
echo json_encode($r, true);
?>