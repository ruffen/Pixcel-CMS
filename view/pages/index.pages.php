   <div id="content">
    	<div id="leftcol">
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/page_process.png" align="left" class="icon" />Menu</h2>
                    <p>Use these options to create, remove and maintain pages</p>
                </div>
                <div class="dashcontent">
                	<ul class="subnav">
                        <li><a href="/?rt=pages/newpage" id="newpage"><img src="/static/images/icons/24/page_add.png" alt="Create New Page" /> Create New Page</a></li>
                        <li><a href="#" id="deletepage"><img src="/static/images/icons/24/page_remove.png" alt="Delete Current Page" /> Delete Current Page</a></li>
                        <li><a href="/?rt=pages/spotmenu" id="editpage" class="fbox"><img src="/static/images/icons/24/page_edit.png" alt="Edit Current Page" /> Edit Current Page</a></li> 
                        <li><a href="#" id="previewpage"><img src="/static/images/icons/24/page_search.png" alt="Preview Current Page" /> Preview Current Page</a></li>  
                        <li><a href="#" id="publishpage"><img src="/static/images/icons/24/page_up.png" alt="Publish Current Page" /> Publish page</a></li>
                        <li class="red"><a href="#" id="expirepage"><img src="/static/images/icons/24/page_down.png" alt="Publish Current Page" />Expire Page</a></li>
                    </ul>       
                </div>
                <div class="dashfooter"></div>
            </div>
            <div class="dashcontainer">
                <div class="dashheading">
                    <h2><img src="/static/images/icons/24/application.png" align="left" class="icon" />Site Structure</h2>
                    <p>Organise and order your Websites and pages</p>
                </div>
                <div class="dashcontent">
<!--					<p class="greybox">Open Folders By Default<span class="greyboxcheck"><input type="checkbox" name="checkbox[]" class="top5 fancy" checked="checked" /></span></p>-->
                    <p class="greybox">Edit Sitemap<span class="greyboxbutton"><a href="?rt=pages/sitemap" id="editSitemap" rel="false">Edit Sitemap</a></span></p>
                    <p class="blackbox hidden" id="sitemap_buttons">Save Changes:                   
						<a id="cancel_sitemap" href="#" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel</a>
						<a id="save_sitemap" href="#" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Save</a>
                    </p>
					<ul id="dhtmlgoodies_tree2" class="file_tree">
                    	<li id="node0" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><a href="#"><?php echo $siteName;?></a>
<?php echo $sitemap;?>
                        </li>
					</ul> 
                </div>
                <div class="dashfooter"></div>
			</div>
        </div>
        <div id="rightcol">
			<div class="dashcontainer">
				<div class="dashheading">
                    <h2><img src="/static/images/icons/24/info.png" align="left" class="icon" />Page Properties for '<span class="pageheadingTitle"><?php echo $page->getTitle();?></span>'</h2>
                  <p>Control metadata and properties for your pages</p>
                </div>
          <div class="dashcontent">
                	<div id="pubstatus">
						<p class="greenbox<?php echo ($page->published() == 1 || ($page->published() == 0 && $page->published(true) == 1)) ? '' : ' hidden';?>" id="pub"><img align="left" class="icon" src="/static/images/icons/16/accept.png">Published - <span id="pubdate"><?php echo $page->getPublishDate('l j F @ g:ia');?></span></p>
						<p class="redbox<?php echo ($page->published() > 1 || ($page->published() == 0 && $page->published(true) > 1)) ? '' : ' hidden';?>" id="withdrawn"><img align="left" class="icon" src="/static/images/icons/16/remove.png">Page <span id="widthrawStatus"><?php echo ($page->published() != 5) ? 'Widthdrawn' : 'Expired';?></span> - <span id="withdrawndate"><?php echo $page->getPublishDate('l j F @ g:ia');?></span></p>
						<p class="yellowbox<?php echo ($page->published() ==  0) ? '' : ' hidden';?>" id="draft"><img align="left" class="icon" src="/static/images/icons/16/notes_edit.png">This saved version has not been published </p>					
					</div>
                	<h3 style="height:75px;">
                        <div class="site" style="border:1px solid #ccc; color:#fff;">
                
<?php
$thumboo_api = "cc09129bdd6b2d5e940b3152f148ff93";
$thumboo_url = "http://www.pixelcms.com.au";
$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=small&api=".$thumboo_api;
@readfile("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params);
?>
                		</div>
                        <span class="pageheadingTitle"><?php echo $page->getTitle();?></span>
						<span id="isIndexText"><?php echo $indexText;?></span>
						<a id="setIndex" href="#" class="smlbutton <?php echo ($page->isIndex()) ? 'hidden' : '';?>">Set as Index</a>
                    </h3>
                	<span class="language">
						<select name="languages" id="languages" style="width:200px;" >
	                        <option value="English" title="/static/images/flags/au.png">English</option>
	                        <option value="Chinese" title="/static/images/flags/chinese.png">Chinese</option>
	                        <option value="French" title="/static/images/flags/french.png">French</option>
	                        <option value="German" title="/static/images/flags/german.png">German</option>
	                        <option value="Italian" title="/static/images/flags/italian.png">Italian</option>
	                        <option value="Nederlands" title="/static/images/flags/nederlands.png">Nederlands</option>
	                        <option value="Polish" title="/static/images/flags/polish.png">Polish</option>
	                        <option value="Spanish" title="/static/images/flags/spanish.png">Spanish</option>
	                        <option value="Swedish" title="/static/images/flags/swedish.png">Swedish</option>
	                        <option value="Vietnamese" title="/static/images/flags/vietnamese.png">Vietnamese</option>
	                    </select>
                    </span>

            <div class="separator"></div>
                    <div class="forms">	
	                    <form action="#" method="get" id="pageDetailForm">
	                    <fieldset>
	                        <legend><span>Page Information</span></legend>
    	                    <div>
        	                    <label for="statusp">Page Status:</label>
            	                <span id="<?php echo ($locked) ? 'statusunp' : 'statusp';?>">
								<img src="/static/images/icons/16/lock_<?php echo ($locked) ? 'disabled' : 'off';?>.png" class="icon" align="left" />
								<span><?php echo ($locked) ? 'Page locked by '.$userWithLocked->getUsername() : 'Page is not locked';?></span>
            	                </span>
                	        </div>
                    	    <div>
								<label for="pageShownId">Internal Sys Id</label>
								<input type="text" value="<?php echo $page->getId();?>" id="pageShownId" disabled="disabled" />
							</div>
							<div>
                        	    <label for="pagename">Page Name:</label>
                            	<input name="pagename" id="pagename" type="text" value="<?php echo $page->getTitle();?>" <?php echo ($disable) ? 'disabled="disabled" ' : '';?>rel="change" />
	                        </div>
    	                    <div>
				                <label for="template">Page Template</label>
            	                <select id="template" name="template" <?php echo ($disable) ? 'disabled="disabled" ' : '';?>rel="change">								
                	                <option value="0">Choose a Template</option>
<?php
$templateId = (is_object($page->getTemplate())) ? $page->getTemplate()->getId() : 0;
foreach($templates as $index => $template){

	$selected = ($page->getTemplate() !== false && $template->getId() == $templateId) ? ' selected="selected"' : '';
?>
									<option value="<?php echo $template->getId();?>"<?php echo $selected;?>><?php echo $template->getName();?></option>
<?php
}
?>
    	                        </select>		
        	                </div>
            	            <div>
                	            <label for="keywords">Keywords:</label>
                    	        <textarea id="keywords" name="keywords" rows="4" cols="40" <?php echo ($disable) ? 'disabled="disabled" ' : '';?>rel="change"><?php echo $page->getKeywords();?></textarea>
                        	     <p>(Enter in specific words which describe the content of this page and its information.)</p>
	                        </div>
	                        <div>
    	                        <label for="description">Description:</label>
        	                    <textarea id="description" name="description" rows="4" cols="40" <?php echo ($disable) ? 'disabled="disabled" ' : '';?>rel="change"><?php echo $page->getDescription();?></textarea>
            	                <p>(Enter in a short paragraph which summarises this page.)</p>	
                	        </div>
        	            </fieldset>
                    </form>
				</div>
				<p class="greyboxbottom">Confirm Changes
					<a id="cancel" href="#" class="button"><img src="/static/images/icons/16/remove.png" alt="" />Cancel Changes</a>
                    <a id="save" href="#" class="button"><img src="/static/images/icons/16/accept.png" alt="" />Confirm Changes</a>
				</p>
			</div>
			<div class="dashfooter"></div>
		</div>     
		<div class="dashcontainer">
			<div class="dashheading">
				<h2><img src="/static/images/icons/24/note_edit.png" align="left" class="icon" />Page Revision History for '<span id="revision-pagename"><?php echo $page->getTitle();?></span>'</h2>
				<p>Rollback this page to any of the following dates</p>
			</div>
			<div class="dashcontent">          
				<table class="dashtable" id="revisionTable">

				</table>     
			</div>
			<div class="dashfooter"></div>
		</div>
	</div>
</div>
<input type="hidden" id="pageId" value="<?php echo $page->getId();?>" />