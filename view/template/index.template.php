    <div id="content">
    	<div id="leftcol">
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/page_process.png" align="left" class="icon" />Menu</h2>
                    <p>Use these options to create and remove website templates</p>
                </div>
                <div class="dashcontent">
                	<ul class="subnav">
                        <li><a href="#" id="createTemplate"><img src="/static/images/icons/24/page_add.png" alt="Create New Page" /> Create New Template</a></li>
                        <li><a href="#" id="deleteTemplate"><img src="/static/images/icons/24/page_remove.png" alt="Delete Current Page" /> Delete Current Template</a></li>
                    </ul>
                </div>
                <div class="dashfooter"></div>
            </div>       
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/application.png" align="left" class="icon" />Template Structure</h2>
                    <p>View and update your templates</p>
                </div>
                <div class="dashcontent">
                    
                        <ul>
<?php
foreach($sites as $menuSite){
?>
                            <li><img src="/static/images/icons/16/home.png" alt="" /><?php echo $menuSite->getName();?>
                            	<ul id="templatemenu_<?php echo $menuSite->getId();?>" class="templatemenu" rel="<?php echo $menuSite->getId();?>">
                                </ul>
                            </li>
<?php
}
?>
                        </ul>    
                </div>
                <div class="dashfooter"></div>
            </div>
        </div>
        <div id="rightcol">
             <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/info.png" align="left" class="icon" />Template Properties</h2>
                    <p>Set up and customise templates for your websites</p>
                </div>
                <div class="dashcontent">
                	<h3><img src="/static/images/icons/48/page_process.png" alt="" class="site" /><span id="templateheader"><?php echo $template->getName();?></span>&nbsp;</h3>
                    <div class="separator"></div>
                    <div class="forms">	
						<form action="#" method="get">
                    
							<fieldset>
    
								<legend><span>Template Configuration</span></legend>
								<div>
									<label for="site">Site:</label>
									<select id="site">
<?php
foreach($sites as $selectlistSite){
	$selected = ($selectlistSite->hasTemplate($template)) ? ' selected="selected"' : '';
?>
									<option value="<?php echo $selectlistSite->getId();?>"<?php echo $selected;?>><?php echo $selectlistSite->getName();?></option>
<?php
}
?>							
									</select>
								</div>
								<div>
									<label for="pagename">Template Name:</label>
									<input name="templatename" id="templatename" type="text" value="<?php echo $template->getName();?>" />
								</div>
								<div id="resourcefilefields">
									<label for="resourcefoldername">Template Files:</label><input name="resourcefoldername" id="resourcefoldername" type="text" value="<?php echo $resourceFolderName;?>" disabled="disabled" /><input type="hidden" id="resourceFolderId" value="<?php echo $resourceFolderId;?>"/><a href="#" class="smlbutton" id="resourcefolder">Select folder</a>
								</div>                        
    
								<form id="file_upload_form" method="post" enctype="multipart/form-data" action="/?rt=files/upload">
									<div>
	                        			<label>Template Location:</label>    
										<input name="file" id="file" size="27" type="file" /><a id="fileSubmit" href="#" class="smlbutton">Upload</a>
    								</div>
                    			</form>
								<div>
                        			<label>Content Regions:</label>
									<table class="dashtable usertbl" id="spottable">
	
									</table>
								</div>    
							</fieldset>    
						</form>
					</div>
				<p class="greyboxbottom">Confirm Changes
					<a id="canceltemplate" href="#" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel Changes</a>
					<a id="saveTemplate" href="#" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Save Changes</a>
				</p>
				<input type="hidden" name="templateId" id="templateId" value="<?php echo $template->getId();?>" />
				
            </div>
            <div class="dashfooter"></div>     
        </div>
    </div>
