<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Pixel CMS&trade;</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script src="/static/js/browser_selector.js" type="text/javascript"></script>
<link href="/static/css/master.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 7]> <link href="/static/css/ie7.css" rel="stylesheet" type="text/css" media="screen" /> <![endif]-->
<!--[if IE 6]> <link href="/static/css/ie6.css" rel="stylesheet" type="text/css" media="screen" /> <![endif]-->
<link rel="stylesheet" type="text/css" href="/static/css/jquery.checkbox.css" />
<link rel="stylesheet" type="text/css" href="/static/css/dd.css" />
<link rel="stylesheet" href="/static/js/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<?php echo $cssfiles;?>
<!-- Jquery files -->
<script type="text/javascript" src="/static/js/jquery.js" ></script>
<script type="text/javascript" src="/static/js/jquery.dd.js"></script>
<script type="text/javascript" src="/static/js/jquery.checkbox.js"></script>
<!-- JQuery fancybox files -->
<script type="text/javascript" src="/static/js/fancybox/jquery.fancybox-1.3.1.js"></script>
<!-- Start PHP File Tree -->
<script type="text/javascript" src="/static/js/ajax.js"></script>
<!-- IMPORTANT! INCLUDE THE context-menu.js FILE BEFORE drag-drop-folder-tree.js -->
<script type="text/javascript" src="/static/js/context-menu.js"></script>
<!--<script type="text/javascript" src="static/js/drag-drop-folder-tree-pms.js"></script>-->
<!-- End PHP File Tree -->
<!-- pixelCms custom files -->
<script type="text/javascript" src="/static/js/general.js"></script>
<!-- end pixelCms custom files -->
<!-- Extra scripts -->
<script type="text/javascript" src="/static/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="/static/js/swfobject.js"></script>
<!-- Controller scripts -->

<script src="/static/js/highcharts.js" type="text/javascript"></script>

<?php echo $javascripts;?>

</head>
<body>

<div id="preloaded-images">
   <img src="/static/images/ajax-loader-small-white-button.gif" width="1" height="1" alt="" />
</div>

	<div id="preamble">
<?php
if($hasUser){
?>
	    <div class="siteSelectList">
		    <p>Currently Viewing:</p>
			<select id="topbarSitelist">
<?php
	foreach($sites as $index => $site){
		$selected = '';
		try{
			$selected = ($site->getId() == $INK_User->getSite()->getId()) ? ' selected="selected"' : '';
		}catch(DataException $e){
			$selected = '';
		}
?>
				<option value="<?php echo $site->getId();?>"<?php echo $selected;?>><?php echo $site->getName();?></option>
<?php
	}
?>				
			</select>
		</div>
        <div id="user">
        	<div class="userbutton"><img src="/static/images//icons/16/user.png" alt="" /><?php echo $INK_User->getFullname();?></div>
            <div class="helpbutton"></div>
            <div class="logoutbutton"><a href="/?rt=logout"><img src="/static/images/icons/16/back.png" alt="" />Logout User</a></div>
        </div>
		<div id="global">
<?php
	foreach($SysModules as $index => $sysMod){
		$href = (count($sysMod->getKids()) > 0) ? '#' : $sysMod->getIndexRoute();
?>
		<div class="<?php echo $sysMod->getClass()?>button">
			<a href="<?php echo $href;?>"><img alt="" src="/static/images/icons/<?php echo $sysMod->getClass();?>.png" style="vertical-align:middle;"><?php echo $sysMod->getName();?></a>
<?php
		if(count($sysMod) > 0){
?>
			<div id="settings-picker">
				<ul>
<?php
			foreach($sysMod->getKids() as $index => $kid){
				$href = (count($kid->getKids()) > 0) ? '#' : $kid->getIndexRoute();
?>
						<li><a href="<?php echo $href;?>"><img src="/static/images/icons/16/<?php echo $kid->getClass();?>.png" alt="" width="16" height="16" /> <?php echo $kid->getName();?></a></li>
<?php		
			}
?>	
				</ul>
			</div>
<?php
		}
?>		
		</div>
<?php
	}
}
?>
		</div>
	</div>
	<div id="header">
	   	<div id="pixelcmslogo">
    		<a href="index.html"><img src="/static/images/pixelcms-logo.png" alt=""  /></a>
        </div>
        <div id="menu">
        	<ul id="navigation">
<?php echo ($hasUser) ? $modules : '';?>
            </ul>
        </div>        
    </div>
    <div style="clear:both;"></div>
<?php echo $maincontent;?>
<!-- The next two elements are nescessary, we create these instead of letting js create them. More clean this way-->
<div id="ruffBlockerContent" style="display:none;"></div>
<div id="ruffBlocker" style="display:none;"></div>
<!-- End of ruffblocker -->
</body>
</html>