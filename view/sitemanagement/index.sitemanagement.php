    <div id="content">
    	<div id="leftcol">
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/home_process.png" align="left" class="icon" />Menu</h2>
                    <p>Use these options to create and remove website configurations</p>
                </div>
                <div class="dashcontent">
                	<ul class="subnav">
                        <li><a href="/sitemangement/createsite" id="newsite"><img src="/static/images/icons/24/home_add.png" alt="Create New Page" /> Create New Website</a></li>
                        <li><a href="/sitemangement/removesite" id="deletesite"><img src="/static/images/icons/24/home_remove.png" alt="Delete Current Page" /> Delete Current Website</a></li>
                    </ul>       
                </div>
                <div class="dashfooter"></div>
            </div>
                                
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/application.png" align="left" class="icon" />Websites</h2>
                    <p>View and update your Websites</p>
                </div>
                <div class="dashcontent">
                        <ul class="file_tree" id="site_tree">
<?php
foreach($sites as $menuSite){
?>
                            <li><a href="#" id="<?php echo $menuSite->getId();?>"><?php echo $menuSite->getName();?></a></li>
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
                    <h2><img src="/static/images/icons/24/info.png" align="left" class="icon" />Website Configuration</h2>
                    <p>Set up and customise your website</p>
                </div>
                <div class="dashcontent">
                	<h3><img src="/static/images/icons/48/home.png" alt="" class="site" /><span id="heading_sitename"><?php echo $site->getName();?></span>&nbsp;</h3>
                    <div class="separator"></div>
                    
                    
                    <div class="forms">	
						<form action="#" method="get" id="sitemanagement_form">
							<fieldset>
								<legend><span>Website Configuration</span></legend>
								<div>
									<label for="sitename">Site Title:</label>
									<input name="sitename" id="sitename" type="text" value="<?php echo $site->getName();?>" />
								</div>
							</fieldset>    
							<fieldset>    
								<div>
									<label for="siteurl">Website URL:</label>
									<input name="siteurl" id="siteurl" type="text" value="<?php echo $site->getUrl();?>" />
	                          
								</div>
								<div>
									<label for="protocol">Server:</label>
									<select name="protocol" id="protocol" style="width:75px;">
										<option value="ftp" <?php echo ($ftpdetails['protocol'] == 'ftp') ? 'selected="selected"': '';?>>FTP</option>
										<!--<option value="sftp" <?php echo ($ftpdetails['protocol'] == 'sftp') ? 'selected="selected"': '';?>>SFTP</option>-->
									</select>
									<input name="ftp_url" id="ftp_url" type="text" style="width:199px;" value="<?php echo $ftpdetails['url'];?>" />
									<input type="checkbox" name="pasv" id="pasv" <?php echo ($ftpdetails['passv']) ? 'checked="checked"' :'';?> /> PASV
								</div>
								<div>
									<label for="port">Port:</label>
									<input name="port" id="port" type="text" value="<?php echo $ftpdetails['port'];?>" />
	    
								</div>
								<div>
									<label for="username">Username:</label>
									<input name="username" id="username" type="text" value="<?php echo $ftpdetails['username'];?>" />
	    
								</div>
								<div>
									<label for="password">Password:</label>
									<input name="password" id="password" type="password" value="<?php echo $ftpdetails['password'];?>" />&nbsp;<a id="test_connection" href="/sitemanagement/testftpconn" class="smlbutton">Test</a>
								</div>
								<div>
	                        		<label for="root">Website Root:</label>    
									<input name="root" id="root" type="text" value="<?php echo $site->getroot();?>" />&nbsp;<a id="browseftp" href="/?rt=sitemanagement/browseftp" class="smlbutton">Browse</a>
								</div>
							</fieldset>
						</form>                    
                    </div>
					<div class="hidden loadingmain">
						<img src="/static/images/ajax-loader-small-white.gif" alt="" />
					</div>
                    <p class="greyboxbottom">Confirm Changes
                    
                    <a id="cancel" href="/sitemanagement/cancel" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel Changes</a>
                    <a id="save" href="/sitemanagement/save" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Save Changes</a>
                    
                    </p>
                </div>
                <div class="dashfooter"></div>
            </div>     
        </div>
    </div>
    <input type="hidden" id="siteId" value="<?php echo $site->getId();?>" />
