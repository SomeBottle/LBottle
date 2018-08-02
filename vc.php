<?php 
session_start();
$rds=rand(1,2);
if($rds==1){
$img=imagecreatetruecolor(200,100);
$bg = imagecolorallocate($img, 255, 255, 255);
$blue = imagecolorallocate($img,rand(0,255),0,0);
$blue2 = imagecolorallocate($img,rand(0,255),20,60);
$dblue = imagecolorallocate($img,rand(0,255),0,205);
imagefill($img,0,0,$bg);
imagestring($img,0.1,20,30,md5(rand(1,233)),$dblue);
imagestring($img,0.1,20,35,md5(rand(1,233)),$dblue);
imagestring($img,0.1,20,40,md5(rand(1,233)),$dblue);
$ran1=rand(100,999);
$ran2=rand(100,999);
imagestring($img,5,20,30,$ran1,$blue2);
imagestring($img,5,rand(45,150),30,$ran2,$blue);
$a=rand(1,2);
$number=strval($ran1).strval($ran2);
$nar=str_split($number,1);
$sco=0;
$max=count($nar)-1;
$saved='';
if($a==1){
	while($sco<=$max){
		$saved=$saved.strval($nar[$sco]);
		$sco+=1;
	}
	imagestring($img,5,0,0,'-------------->',$blue);
}else{
	while($sco<=$max){
		$saved=$saved.strval($nar[$max-$sco]);
		$sco+=1;
	}
imagestring($img,5,0,0,'<--------------',$blue);
}
$_SESSION['verifycode']=sha1($saved);
header('Content-type:image/png;charset=utf-8');
imagepng($img);
imagedestroy($img);
}else{
	header ('Content-Type: image/png');
$image=imagecreatetruecolor(100, 30);
//背景颜色为白色
$color=imagecolorallocate($image, 255, 255, 255);
imagefill($image, 20, 20, $color);
$code='';
for($i=0;$i<4;$i++){
    $fontSize=8;
    $x=rand(5,10)+$i*100/4;
    $y=rand(5, 15);
    $data='abcdefghijklmnopqrstuvwxyz123456789';
    $string=substr($data,rand(0, strlen($data)),1);
    $code.=$string;
    $color=imagecolorallocate($image,rand(0,120), rand(0,120), rand(0,120));
    imagestring($image, $fontSize, $x, $y, $string, $color);
}
$_SESSION['verifycode']=sha1($code);//存储在session里
for($i=0;$i<200;$i++){
    $pointColor=imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
    imagesetpixel($image, rand(0, 100), rand(0, 30), $pointColor);
}
for($i=0;$i<2;$i++){
    $linePoint=imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
    imageline($image, rand(10, 50), rand(10, 20), rand(80,90), rand(15, 25), $linePoint);
}
imagepng($image);
imagedestroy($image);
}
session_write_close();
?>