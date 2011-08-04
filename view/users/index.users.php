    <div id="content">
    	<div id="leftcol">
        
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/users_process.png" align="left" class="icon" />Menu</h2>
                    <p>Use these options to create, remove and maintain users and groups</p>
                </div>
                <div class="dashcontent">
                	<ul class="subnav">
                        <li><a href="#" id="createGroup"><img src="/static/images/icons/24/users_add.png" alt="Create New Group" /> Create New Group</a></li>
                        <li class="groupmenu"><a href="#" id="deleteGroup"><img src="/static/images/icons/24/users_remove.png" alt="Delete Current Group" /> Delete Current Group</a></li>
                        <li><a href="#" id="createUser"><img src="/static/images/icons/24/user_add.png" alt="Create New User" /> Create New User</a></li> 
                        <li class="hidden usermenu"><a href="#" id="deleteUser"><img src="/static/images/icons/24/user_remove.png" alt="Delete Current User" /> Delete Current User</a></li>  
                    </ul>       
                </div>
                <div class="dashfooter"></div>
            </div>
                                
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/application.png" align="left" class="icon" />Group Structure</h2>
                    <p>Organise and order your groups and users</p>
                </div>
                <div class="dashcontent">
					<p class="greybox">Edit User/Group Structure:<span class="greyboxbutton"><a href="#" id="editStructure" rel="false">Edit Structure</a></span></p>
					<p class="blackbox hidden" id="editstructure_buttons">
						Save Changes:
						<a class="button" href="#" id="user_structure_cancel"><img alt="" src="/static/images/icons/16/remove.png">Cancel</a>
						<a class="button" href="#" id="user_structure_submit"><img alt="" src="/static/images/icons/16/accept.png">Save</a>
					</p>
                    <ul id="dhtmlgoodies_tree2" class="file_tree">
						<?php echo $usermenu;?>
                    </ul>            
                </div>
                <div class="dashfooter"></div>
            </div>
        
        </div>
        
        
        <div id="rightcol">
<?php echo $details;?>
        </div>
    </div>
