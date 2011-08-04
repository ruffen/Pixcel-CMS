                 
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/info.png" align="left" class="icon" />User Properties for '<span class="firstname"><?php echo $user->getFirstname();?></span>&nbsp;<span class="lastname"><?php echo $user->getLastname();?></span>'</h2>
                    <p>Control access and details for this user</p>
                </div>
                <div class="dashcontent">
                	<h3><img src="/static/images/icons/48/user.png" alt="" class="site" /><span class="firstname"><?php echo $user->getFirstname();?></span>&nbsp;<span class="lastname"><?php echo $user->getLastname();?></span>
                    
                   	</h3>
                    <div class="separator"></div>
                    
                    
                    <div class="forms">	
                    <form action="#" method="get">
                    
                    <fieldset>
    
                        <legend><span>User Access/Details</span></legend>
                        <div>
                            <label for="group">Group:</label>
                            <select id="group" name="group">
                                <option value="0">Choose a group</option>
<?php
foreach($groups as $index => $group){
?>
								<option value="<?php echo $group->getId();?>" <?php echo ($group->hasAccess($user)) ? 'selected="selected"' : '';?>><?php echo $group->getName();?></option>
<?php
}
?>
                            </select>
                          
                        </div>
                        <div>
                            <label for="firstname">First Name:</label>
                            <input name="firstname" id="firstname" type="text" value="<?php echo $user->getFirstname();?>" />
                          
                        </div>
                        <div>
                            <label for="lastname">Last Name:</label>
                            <input name="lastname" id="lastname" type="text" value="<?php echo $user->getLastname();?>" />
                          
                        </div>
                        <div>
                            <label for="email">Email:</label>
                            <input name="email" id="email" type="text" value="<?php echo $user->getEMail();?>" />
                          
                        </div>
                        <div>
                            <label for="username">Username:</label>
                            <input name="username" id="username" type="text" value="<?php echo $user->getUsername();?>" />
                          
                        </div>
                        <div>
                            <label for="password">Password:</label>
                            <input name="password" id="password" type="text" />
                          
                        </div>
                        <div>
                            <label for="password_repeat">Re-enter Password:</label>
                            <input name="password_repeat" id="password_repeat" type="text" />
                          
                        </div>
                        <div>
                            <label for="active">Active Account:</label>
                            <input type="checkbox" value="checkbox" id="active" name="active" <?php echo ($user->active()) ? 'checked="checked"' : '';?> />
                        </div>
                    </fieldset>
                    </form>
                    
                    </div>
                    <p class="greyboxbottom">Confirm Changes
                    
                    <a id="cancel_user" href="#" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel Changes</a>
                    <a id="save_user" href="#" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Save Changes</a>
                    
                    </p>
                </div>
                <div class="dashfooter"></div>
            </div>      
<!--                  <div class="dashcontainer">
                    <div class="dashheading">
                        <h2><img src="/static/images/icons/24/note_edit.png" align="left" class="icon" />Recent Activity for 'Krister Karto'</h2>
                        <p>View recent activity logs for the current user</p>
                    </div>
                    <div class="dashcontent">          

                                <table class="dashtable">
    
                                        <tr>
                                            <td width="30%">12 September 2010 @ 21:00</td>
                                            <td><img src="/static/images/icons/16/page_add.png" class="recenticon" />New page 'Index' created in system</td>
                                        </tr>
                                        <tr class="tralt">
                                            <td>12 September 2010 @ 21:10</td>
                                            <td><img src="/static/images/icons/16/page_edit.png" class="recenticon" />'Index' page edited</td>
                                        </tr>
                                        <tr>
                                            <td>12 September 2010 @ 22:03</td>
                                            <td><img src="/static/images/icons/16/page_up.png" class="recenticon" />'Index' page published to the live environment</td>
                                        </tr>
                                        <tr class="tralt">
                                            <td>12 September 2010 @ 23:16</td>
                                            <td><img src="/static/images/icons/16/folder_add.png" class="recenticon" />New file 'test.png' uploaded to system</td>
                                        </tr>
                                        <tr>
                                            <td>12 September 2010 @ 22:03</td>
                                            <td><img src="/static/images/icons/16/folder_remove.png" class="recenticon" />'Old_PDF_2003.pdf' file deleted from system</td>
                                        </tr>
                                        <tr class="tralt">
                                            <td>12 September 2010 @ 23:16</td>
                                            <td><img src="/static/images/icons/16/user_add.png" class="recenticon" />User 'John Smith' created in 'Publishers'</td>
                                        </tr>
                     </table>     
                  
 
                </div>
                		<div class="dashfooter"></div>
            		</div>-->
				<input type="hidden" name="userId" id="userId" value="<?php echo $user->getId();?>" />
