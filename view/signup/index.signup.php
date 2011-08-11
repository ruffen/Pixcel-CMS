   <div id="content">
    	<div id="leftcol">
        </div>
        
        
        <div id="rightcol">
		    <div class="forms">	
				<form action="/?rt=signup/signup" method="POST" id="signupform">
					<h2>we have a really cool CMS, check it out!! (it totally has styling as well)</h2>
					<fieldset>
						<legend>Setup your account</legend>
						<div style="float:left; width: 100%">
							<label for="Firstname">Firstname:</label>
							<input type="text" id="Firstname" name="Firstname" />
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="Lastname">Lastname:</label>
							<input type="text" id="Name" name="Lastname"/>
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="Email">Email:</label>
							<input type="text" id="Email" name="Email" />
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="Username">Username:</label>
							<input type="text" id="Username" name="Username" />
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="Password">Password:</label>
							<input type="password" id="Password" name="Password" />
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="PasswordRepeat">PasswordRepeat:</label>
							<input type="password" id="PasswordRepeat" name="PasswordRepeat" />
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="Company">Company:</label>
							<input type="text" id="Company" name="Company" />
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
						<div style="float:left; width: 100%">
							<label for="TimeZone">TimeZone:</label>
							<select id="Timezone" name="Timezone">
								<option value="0">my timezone</option>
								<option value="10">your timezone</option>
							</select>
							<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						</div>
					</fieldset>
					<fieldset>
						<legend>Select your pixelCMS site address</legend>
						<label for="Subdomain">Site Address: </label>
						<input type="text" id="Subdomain" name="Subdomain" />
						<span class="label">.pixelcms.com.au</span>
						<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
					</fieldset>
					<fieldset>
						<legend>Create your account</legend>
						<p>By clicking the button below you totally agree to pay us a sheiiiitloads of cash to do some really cool stuff!!</p>
						<input type="checkbox" id="Licenseagreement" name="Licenseagreement" /><label for="licenseagreement">Click this, its sooooo coool!</label>
						<input type="checkbox" id="Newsletter" name="Newsletter" checked="checked" /><label for="newsletter">We are probably gonna get a newsletter that you are not going to read, but keep this checked anyway!</label>
						<span class="errorvalidation hidden"><img src="/static/images/icons/16/remove.png" /></span>
						<div>
							<input type="submit" name="Submit" id="Submit" value="submitted" />
						</div>
					</fieldset>
				</form>
			</div>
        </div>
    </div>