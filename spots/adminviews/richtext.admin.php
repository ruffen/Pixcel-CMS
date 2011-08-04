<div class="editor"> 
 	<div class="editordashheading">
        <h2><img align="left" class="icon" src="static/images/icons/24/page_edit.png">Edit Page</h2>
        <p>Edit the content of your page. You can include images, documents and videos.</p>
    </div>

	<div class="full">
		<textarea class="tinymce" id="tinymce" name="tinymce" style="width:100%; height:360px; padding:0; margin:0;"><?php echo $spotvalue;?></textarea>
	</div>
	<p class="greyboxbottom"><span class="greyboxredbutton" id="cancelspot">Cancel</span><span class="greyboxgreenbutton" id="ok">Confirm</span></p>
	
	<input type="hidden" name="spotid" id="spotId" value="<?php echo $spotId;?>" />
</div>