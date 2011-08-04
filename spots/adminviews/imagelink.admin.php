<div class="admin">
	<div class="full">
		<fieldset>
			<label for="url"></label>
			<input type="text" value="<?php echo $url;?>" id="url" ?>
		</fieldset>
	</div>
	<div class="left">
		<ul class="filetree">
			<?php echo $folderlist;?>
		</ul>
	</div>
	<div class="right">
		<div class="imagelist">
			<table>
			<?php echo $imagelist;?>
			</table>
		</div>
	    <p class="greyboxbottom"><span class="greyboxredbutton" id="cancelspot">Cancel</span><span class="greyboxgreenbutton" id="ok">Select</span></p>
		<input type="hidden" name="spotid" id="spotId" value="<?php echo $spotId;?>" />
	</div>
</div>