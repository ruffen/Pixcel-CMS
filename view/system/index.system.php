<div id="sites">
	<h2>Sites</h2>
	<ul>
<?php
foreach($sites as $siteIndex => $site){
?>
		<li><a href="#" id="site_<?php echo $site->getId();?>"><?php echo $site->getName();?></a>
			<ul>
<?php
	foreach($site->getTemplates() as $tplIndex => $template){
?>
				<li><a href="#" id="tpl_<?php echo $template->getId();?>"><?php echo $template->getName();?></a>
					<ul>
<?php
		foreach($template->getSpots() as $spotIndex => $spot){
?>
						<li><a href="#" id="spot_<?php echo $spot->getId();?>"><?php echo $spot->getName();?></a></li>
<?php			
			
		}
?>
					</ul>			
				</li>
<?php
	}
}
?>
			</ul>
		</li>
	</ul>
</div>
<h1>Upload form - upload new templates for selected site</h1>
<form id="file_upload_form" method="post" enctype="multipart/form-data" action="?rt=files/upload">
	<input name="file" id="file" size="27" type="file" />
	<input type="submit" name="action" value="Upload" id="fileSubmit" />
</form>
<div class="templateinfo">
	<span id="filename"></span><a href="#" id="maketemplate">Click here to create template</a>
	<div class="assetlist">
		<div id="createdTemplate">
			<label for="templateName">Templatename:</label>
			<input type="text" name="templatename" id="templateName" />
			<img src="#" alt="templateImage" />
			<div id="assetlist">
				<ul>
				
				</ul>
			</div>
			<div class="controls">
				<a href="#" id="saveTemplate">Save template</a>
			</div>

			
		
		</div>
	</div>

</div>