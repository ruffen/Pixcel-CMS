<div class="imagepicker">

	<div class="editordashheading">
        <h2><img align="left" class="icon" src="static/images/icons/24/image_edit.png">Edit Image</h2>
        <p>Update template images for your site</p>
    </div>
    
	<div class="editorleft">
		<ul class="filetree">
			<?php echo $folderlist;?>
		</ul>
	</div>
	<div class="editorright">
		<div class="imagelist">
			<table>
			<?php echo $imagelist;?>
			</table>
		</div>
		
	</div>
    <p class="greyboxbottom"><span class="greyboxredbutton" id="cancelspot">Cancel</span><span class="greyboxgreenbutton" id="ok">Select</span></p>
	<input type="hidden" name="spotid" id="spotId" value="<?php echo $spotId;?>" />
</div>
