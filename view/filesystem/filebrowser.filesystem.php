<div class="file_browser_dialog" id="ftpbrowser">
	<h1>Browse your folders and choose home directory</h1>
	<div id="ftpbrowser_inner" class="file_browser">
		<ul>
			<li class="up_one_level"><a href="#" id="ftp_up">Up one level</a></li>
			<?php echo $folderlist;?>
		</ul>
		<input type="hidden" id="currentroot" name="currentroot" value="<?php echo $root;?>" />
		<input type="hidden" id="currentrootName" name="currentrootName" value="<?php echo $rootname;?>" />
	</div>
	<div class="options">
		<p class="greyboxbottom">
            <a id="cancelbrowse" href="sitemanagement/cancel" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel</a>
            <a id="selectfolder" href="sitemanagement/save" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Select</a>
		</p>
	</div>
</div>