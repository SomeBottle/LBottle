<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
require './config.php';
require_once './api/api.php';
$request = explode('?', $_SERVER['REQUEST_URI']) [1];
if (checklogin() && $request !== 'reset') {
    header('Location: ' . rdpath());
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<link type="text/css" href="./i/m.css" rel="stylesheet" />
<title><?php echo title(); ?></title> 
</head>
<body>
<div class='mask' id='mask'></div>
<h2 class='t' id='tt'><?php echo title(); ?></h2>
<div id='f'>
<?php if ($request == 'authed') { ?>
<p>请输入你的邮箱来通过验证~</p>
<script>setTimeout(function(){emauth();},200);</script>
<p><input type='text' id='em' placeholder='输入完毕自动提交'></input></p>
<?php
} else if ($request == 'reset') { ?>
<p><input type='password' id='rpi' placeholder='<?php echo resetpass(); ?>'></input></p>
<p><input type='button' id ='rpb' value='<?php echo resetbtn(); ?>' onclick='sub()'></input></p>
<?php
} else { ?>
<p><input type='text' id='u' placeholder='<?php echo userph(); ?>'></input></p>
<p><input type='password' id='p' placeholder='<?php echo passph(); ?>'></input></p>
<p><input type='button' id ='b' value='<?php echo btndef(); ?>' onclick='sub()'></input></p>
<?php
} ?>
</div>
<div id='t'></div>
</body>
<script>var btndef='<?php echo btndef(); ?>';var btnlog='<?php echo btnlog(); ?>';var btnreg='<?php echo btnreg(); ?>';var usera='<?php echo userapass(); ?>';</script>
<script src='./i/m.js'></script>