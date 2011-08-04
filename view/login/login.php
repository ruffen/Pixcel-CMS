<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Pixel CMS&trade;</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="/static/js/browser_selector.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/jquery.js" ></script>
<script type="text/javascript" src="/static/js/login/login.js"></script>
<link href="/static/css/login.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 7]> <link href="/static/css/ie7.css" rel="stylesheet" type="text/css" media="screen" /> <![endif]-->
<!--[if IE 6]> <link href="/static/css/ie6.css" rel="stylesheet" type="text/css" media="screen" /> <![endif]-->
</head>

<body>
	<div class="hidden">
		<img src="/static/images/loading-login.gif" class="hidden" />	
	</div>

	<div id="logo">
    	<div id="logospan"><a href="http://www.pixelcms.com.au"><img src="/static/images/blank.gif" width="186px" height="34px" alt="Pixel CMS Website" /></a></div>
    </div>
    <div id="panel">
    
    	<div id="container">
			<form name="loginform" id="loginform" action="/" method="post">
				<div id="left">
					<input type="text" name="username" id="username" value="Username" class="input1" />
				</div>
				<div id="middle"> 
					<input type="password" name="password" id="password" value="" class="input1" />
				</div>
				<div id="right">
        			<div class="bluebutton" id="loginbutton">Login</div>
				</div>
			</form>
        </div>    
    </div>
    <div class="<?php echo $message['css'];?>" id="notifybox"><img src="/static/images/icons/16/<?php echo $message['icon'];?>.png" alt="" class="pagestblicon" /><span id="errortext"><?php echo $message['text'];?></span></div>
</body>
</html>
