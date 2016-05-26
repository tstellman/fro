<?php

if ( !isAuth() ) {
/* User is not logged in, require login */
?>
<form action="/partner-agencies/partner-agency-login/" method="post">
 Username : <br>
 <input class="fancy_field" type="Text" name="attempt_user" value="<?php if ( isset( $_COOKIE['remember_user'] ) ) { echo($_COOKIE['remember_user']); } ?>"><br>
 Password : <br>
 <input class="fancy_field" type="Password" name="attempt_pass" value=""><br>
 Agency/Program Code : <br>
 <input class="fancy_field" type="Text" name="attempt_prog" value="<?php if ( isset( $_COOKIE['remember_prog'] ) ) { echo($_COOKIE['remember_prog']); } ?>"><br>
 <label><input type="checkbox" checked="checked" name="attempt_remember" /> Remember my username and agency code</label><br/>
 <br/><button class="fancy_button" type="submit" name="submit">SIGN IN</button>
</form>
<p>If you are a partner of Roadrunner Food Bank and need a log in account, or have forgotten your password, please contact your RRFB Program Department representative.  A list of contact information can be found <a href="http://www.rrfb.org/contact/employee-directory/" target="blank_">here.</a></p><br/>

<?php

} else {
	/* User IS logged in */

	/* User has provided a new email, save it */
	if ( isset( $_REQUEST['change_email'] ) ){
		$args = array(
			'ID'         => get_current_user_id(),
			'user_email' => esc_attr( $_REQUEST['change_email'] )
		);
		wp_update_user( $args );
		define( 'EMAIL_UPDATED', true );
		
		echo '<p class="notice"><strong>Thank you!</strong> Your email address on file has been updated.</p>';
	}
	
	if ( isEmail() == false ){
		 /*Email on record is auto-generated, let's force a new one from the user */
?>
		<h2>Action Required</h2>
		<p><strong>Please take a moment to update your email address on file.</strong><br/>You <em>must</em> confirm your email address before you can access this system.</p>
		<br/>
		<form action="" method="post">
		 <label for="change_email">Email Address :</label><br />
		 <input class="fancy_field" type="text" name="change_email" id="change_email" value=""><br>
		 <br/><button class="fancy_button" type="submit" name="submit">SAVE</button>
		</form> 	
<?php

	} else {
		/* User is authenticated and email on file is valid */
		the_content();
	}
}
