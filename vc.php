<?php
session_start();
$img = imagecreatetruecolor(200, 100);
$bg = imagecolorallocate($img, 255, 255, 255);
$blue = imagecolorallocate($img, rand(0, 255), 0, 0);
$blue2 = imagecolorallocate($img, rand(0, 255), 20, 60);
$dblue = imagecolorallocate($img, rand(0, 255), 0, 205);
imagefill($img, 0, 0, $bg);
imagestring($img, 0.1, 20, 30, md5(rand(1, 233)), $dblue);
imagestring($img, 0.1, 20, 35, md5(rand(1, 233)), $dblue);
imagestring($img, 0.1, 20, 40, md5(rand(1, 233)), $dblue);
$ran1 = rand(100, 999);
$ran2 = rand(100, 999);
imagestring($img, 5, 20, 30, $ran1, $blue2);
imagestring($img, 5, rand(45, 150), 30, $ran2, $blue);
$a = rand(1, 2);
$number = strval($ran1) . strval($ran2);
$nar = str_split($number, 1);
$sco = 0;
$max = count($nar) - 1;
$saved = '';
if ($a == 1) {
    while ($sco <= $max) {
        $saved = $saved . strval($nar[$sco]);
        $sco+= 1;
    }
    imagestring($img, 5, 0, 0, '-------------->', $blue);
} else {
    while ($sco <= $max) {
        $saved = $saved . strval($nar[$max - $sco]);
        $sco+= 1;
    }
    imagestring($img, 5, 0, 0, '<--------------', $blue);
}
$_SESSION['verifycode'] = $saved;
header('Content-type:image/png;charset=utf-8');
imagepng($img);
imagedestroy($img);
session_write_close();
?>