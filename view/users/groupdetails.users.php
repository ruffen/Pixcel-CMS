             <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/info.png" align="left" class="icon" />Group Properties for '<span class="groupname"><?php echo $group->getName();?></span>'</h2>
                    <p>Control access and priviledges for your groups</p>
                </div>
                <div class="dashcontent">
                	<h3><img src="/static/images/icons/48/users.png" alt="" class="site" /><span id="groupheader" class="groupname"><?php echo $group->getName();?></span>&nbsp;</h3>
                    <div class="separator"></div>
                    <div class="forms">
                    	<form>
                    	<fieldset>
	                    <div>
                            <label for="groupname">Group Name:</label>
                            <input name="groupname" id="groupname" type="text" value="<?php echo $group->getName();?>" />
                        </div>
                        </fieldset>
                        </form>
                    </div>    
                    
                    <table class="dashtable usertbl" id="moduletable">
<?php
foreach($customer->getSites() as $index => $site){
?>
								<tr>
									<td width="35%" class="greytd">
										<img src="/static/images/icons/24/site.png" alt="" class="groupicon" />
										<?php echo $site->getName();?>
									</td>
									<td><?php echo $site->getUrl();?></td>
									<td><span class="tblcheck"><input type="checkbox" name="checkbox[]" id="site_<?php echo $site->getId();?>" class="top5 fancy" <?php echo ($group->hasAccess($site)) ? 'checked="checked"' : '';?> /></span></td>
								</tr>
<?php
}
foreach($modules as $index => $module){
?>
							<tr>
								<td width="35%" class="greytd">
									<img src="/static/images/icons/24/<?php echo $module->getClass();?>.png" alt="" class="groupicon" />
									<?php echo $module->getName();?>
								</td>
                                <td><?php echo $module->getDescription();?></td>
                                <td><span class="tblcheck"><input type="checkbox" name="checkbox[]" id="module_<?php echo $module->getId();?>" class="top5 fancy" <?php echo ($group->hasAccess($module)) ? 'checked="checked"' : '';?> /></span></td>
                            </tr>
<?php
	foreach($module->getKids() as $index => $kid){
?>
							<tr>
								<td width="35%" class="greytd">
									<img width="24" class="groupicon" alt="" src="/static/images/blank.gif">
									<img class="groupicon" alt="" src="/static/images/icons/24/subicon.png">
									<img class="groupicon" alt="" src="/static/images/icons/24/<?php echo $kid->getClass();?>.png">
									<?php echo $kid->getName();?>
								</td>
                                <td><?php echo $kid->getDescription();?></td>
                                <td><span class="tblcheck"><input type="checkbox" name="checkbox[]" id="module_<?php echo $kid->getId();?>" class="top5 fancy" <?php echo ($group->hasAccess($kid) || $kid->isStandard()) ? 'checked="checked"' : '';?> /></span></td>
							</tr>
<?php
	}
	foreach($module->getRights() as $index => $right){
?>
							<tr>
								<td width="35%" class="greytd">
									<img width="24" class="groupicon" alt="" src="/static/images/blank.gif">
									<img class="groupicon" alt="" src="/static/images/icons/24/subicon.png">
									<img class="groupicon" alt="" src="/static/images/icons/24/<?php echo $right->getKey();?>.png">
									<?php echo $right->getName();?>
								</td>
                                <td><?php echo $right->getDescription();?></td>
                                <td><span class="tblcheck"><input type="checkbox" name="checkbox[]" id="right_<?php echo $right->getId();?>_<?php echo $module->getId();?>" class="top5 fancy" <?php echo ($group->hasAccess($right, $module)) ? 'checked="checked"' : '';?> /></span></td>
							</tr>
<?php
	}
}
?>                                   

                        </table> 
                    <p class="greyboxbottom">Confirm Changes
                    
                    <a id="cancel_group" href="#" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel Changes</a>
                    <a id="save_group" href="#" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Save Changes</a>
                    <input type="hidden" id="groupId" name="groupId" value="<?php echo $group->getId();?>" />
					</p>
                </div>
                <div class="dashfooter"></div>
            </div>     
