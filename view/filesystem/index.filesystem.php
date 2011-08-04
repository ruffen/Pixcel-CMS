    <div id="content">
    	<div id="leftcol">
        
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/folder_process.png" align="left" class="icon" />Menu</h2>
                    <p>Use these options to create, remove and maintain folders</p>
                </div>
                <div class="dashcontent">
                	<ul class="subnav">
                        <li><a href="#" id="createfolder"><img src="/static/images/icons/24/folder_add.png" alt="Create New Page" /> Create New Folder</a></li>
                        <li><a href="#" id="deletefolder"><img src="/static/images/icons/24/folder_remove.png" alt="Delete Current Page" /> Delete Current Folder</a></li>
                        <li><a href="#" id="renamefolder"><img src="/static/images/icons/24/folder_edit.png" alt="Edit Current Page" /> Rename Current Folder</a></li> 
                        <li><a href="/?rt=filesystem/fileuploader" id="fileuploader"><img src="/static/images/icons/24/folder_up.png" alt="Upload file" /> Upload Files</a></li>  
                    </ul>       
                </div>
                <div class="dashfooter"></div>
            </div>
                                
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/application.png" align="left" class="icon" />Folder Structure</h2>
                    <p>Organise and order your images and documents</p>
                </div>
                <div class="dashcontent">
<!--					<p class="greybox">Open Folders By Default<span class="greyboxcheck"><input type="checkbox" name="checkbox[]" class="top5" checked="checked" /></span></p>-->
                    <p class="greybox">Edit Folder Structure<span class="greyboxbutton"><a href="#" id="changestructure">Edit Folder Structure</a></span></p>
                    <p class="blackbox hidden" id="foldertreeChanges">Save Changes:
						<a class="button" href="#" id="cancelChanges"><img alt="" src="/static/images/icons/16/remove.png">Cancel</a>
						<a class="button" href="#" id="saveChanges"><img alt="" src="/static/images/icons/16/accept.png">Save</a>
                    </p>
					<ul id="dhtmlgoodies_tree2" class="file_tree">
<?php echo $folderlist;?>
					</ul> 
                </div>
                <div class="dashfooter"></div>
            </div>
        </div>
        <div id="rightcol">
             <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/info.png" align="left" class="icon" />'<span class="foldername"><?php echo (isset($folder) && is_object($folder)) ? $folder->getName() : '';?></span>' Folder</h2>
                    <p>Preview, move and maintain your files</p>
                </div>
                <div class="dashcontent">
                	<h3><img src="/static/images/icons/48/folder.png" alt="" class="site" /><span id="foldername" class="foldername"><?php echo (isset($folder) && is_object($folder)) ? $folder->getName() : '';?></span></h3>
                    <div class="separator"></div>
                    
                    
                    
                    
                    <table class="filetable">
                    	<thead>
                        <tr>
                            <th width="5%"><input type="checkbox" value="checkbox" class="fileactionbox" /></th>
                            <th width="20%">
                            	<select name="fileoptionsone" id="fileoptionsone" style="width:200px;" >
                                	<option value="Select" >Select an Action</option>
                            		<option value="Movefile" title="static/images/icons/16/next.png">Move File(s)</option>
                            		<option value="Deletefile" title="static/images/icons/16/remove.png">Delete File(s)</option>
                                </select>
                            </th>
                            <th width="8%" class="bold">Type</th>
                            <th width="35%" class="bold">Title</th>
                            <th width="20%" class="bold">Added</th>
                            <th width="5%" class="bold">Size</th>
                            <th width="7%" class="bold" align="center">Replace</th>
                        </tr>
                        </thead>
                        <tbody>
						
                        </tbody>
                        <tfoot>
                        <tr>
                            <th width="5%"><input type="checkbox" value="checkbox" class="fileactionbox" /></th>
                            <th width="20%">
                            	<select name="fileoptionstwo" id="fileoptionstwo" style="width:200px;" >
                                	<option value="Select" >Select an Action</option>
                            		<option value="Movefiles" title="static/images/icons/16/next.png">Move File(s)</option>
                            		<option value="Deletefiles" title="static/images/icons/16/remove.png">Delete File(s)</option>
                                </select>
                            </th>
                            <th width="8%">Type</th>
                            <th width="35%">Title</th>
                            <th width="20%">Added</th>
                            <th width="5%">Size</th>
                            <th width="7%" align="center">Replace</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="dashfooter"></div>
            </div>     
        </div>
    </div>
	<input type="hidden" id="folderId" value="<?php echo (isset($folder) && is_object($folder)) ? $folder->getId() : 'new';?>" />