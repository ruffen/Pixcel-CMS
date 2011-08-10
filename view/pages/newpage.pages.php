<div class="prompt">

<style>
/* Form Styles and Formatting */

.forms form								{ margin: 0 0 0; padding:5px 0 10px 0; font-size:12px; }
.forms form * 							{ margin: 0; }

	.forms form fieldset 				{ border: none; margin: 0 1em 1em; padding:5px; color:#000; margin-top:5px; padding-top:8px; }
	.forms form legend span 			{ display: block; width: 130px; font-size:80%; padding: 5px 0 5px; color: #FFF; border: 1px solid #000; background:#333; text-transform:uppercase; display:none; }
	.forms form div 					{ float: left; width: 599px; clear: both; padding: 5px 0; color:#666; }
	.forms form label					{ display: block; float: left; width: 180px; margin: 0 10px 0 0; padding: 3px 0 0; text-align: left; color:#666; }
		.forms form ul label			{ font-weight:100; }
		.forms form label span 			{ color: #dc6900; font-size: 80%; text-transform: uppercase; font-weight:bold; }
	
	.forms form input#pagename,
	.forms form input#email,
	.forms form input#phonenumber 		{ float: left; width: 274px; margin: 0; padding: 3px; height: 14px; border: 1px solid #999; }
	
	#statusunp 		{ float: left; width: 274px; margin: 0; padding: 3px; height: 14px; border: 1px solid #fc8686; background:#ffe5e5; color:#fc8686; }
	#statusp 			{ float: left; width: 274px; margin: 0; padding: 3px; height: 14px; border: 1px solid #86aafc; background: #dfe9ff; color:#86aafc; }

	.forms form select					{ float: left; width: 280px; }
	.forms form textarea				{ float: left; width: 274px; padding: 3px; border: 1px solid #999; font-family:Arial, Helvetica, sans-serif; font-size:11px; }
	.forms form #submit 				{ width: 400px; padding-left: 10px; }
	

	/*#forms form div.required 			{ background: #fae1cd; }
	#forms form div.required input#fullname,
	#forms form div.required input#email,
	#forms form div.required input#phonenumber 		{ border: 1px solid #dc6900; }*/

	.forms form p 						{ float: left; width: 110px; margin: 0 0 0 10px; padding: 3px 0 0; color: #999; font-size:9px; }
	.forms form ul 						{ float:left; margin: 0; padding: 5px 0 0 0px; }
		.forms form ul li 				{ margin: 0; padding: 0 0 5px; list-style-type: none; }
			.forms form ul li label 	{ display: inline; float: none; width: auto; margin: 0 0 0 5px; padding: 0; text-align: left; }
			
			.forms form h2 				{ margin-top:-3px; margin-bottom:20px; }
			.forms form h2 .icon 		{ margin-right:5px; }
			
			
			.formheading 				{ padding:0px; margin:0; background: url(../images/dash-heading.png) repeat-x; padding-top:-3px; }
			.forms form table a			{ border-bottom:1px dashed; }
			
/* --- End --- */
</style>

	<div class="editordashheading">
        <h2><img align="left" class="icon" src="/static/images/icons/24/page_add.png">Create New Page</h2>
        <p>Fill in the details to create the new page.</p>
    </div>
                    <div class="forms">	
	                    <form action="#" method="get">
	                    <fieldset>
	                        <legend><span>Page Information</span></legend>
    	                    <div style="display:none;">
        	                    <label for="basic_statusp">Page Status:</label>
            	                <input name="statusp" id="basic_statusp" type="text" value="Draft" disabled="disabled" />
                	        </div>
                    	    <div>
                        	    <label for="basic_pagename">Page Name:</label>
                            	<input name="pagename" id="basic_pagename" type="text" value="" />
	                        </div>
    	                    <div>
				                <label for="template">Page Template</label>
            	                <select id="basic_template" name="template">								
                	                <option value="0">Choose a Template</option>
<?php
foreach($templates as $index => $template){
?>
									<option value="<?php echo $template->getId();?>"><?php echo $template->getName();?></option>
<?php
}
?>
    	                        </select>		
        	                </div>
            	            <div>
                	            <label for="basic_keywords">Keywords:</label>
                    	        <textarea id="basic_keywords" name="keywords" rows="4" cols="40"></textarea>
                        	     <p>(Enter in specific words which describe the content of this page and its information.)</p>
	                        </div>
	                        <div>
    	                        <label for="basic_description">Description:</label>
        	                    <textarea id="basic_description" name="description" rows="4" cols="40"></textarea>
            	                <p>(Enter in a short paragraph which summarises this page.)</p>	
                	        </div>		
	                        <div>
	                            <label for="basic_enquiry">User Access:</label>
    	                        <select id="basic_enquiry" name="enquiry">
        	                        <option selected="selected">Choose a group</option>
            	                    <option>Administrator</option>
                	                <option>Authors</option>
                    	            <option>Media</option>
	                            </select>		
    	                    </div>
        	            </fieldset>
                    </form>
                    </div>
				<p class="greyboxbottom">Confirm Page<span class="greyboxredbutton" id="basic_cancel">Cancel</span><span class="greyboxgreenbutton" id="basic_save">Save</span></p>
				
</div>                
