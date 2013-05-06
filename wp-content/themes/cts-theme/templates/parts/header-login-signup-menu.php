<?php 
/**
 * CTS Theme: Login Element
 *
 * Uses Superfish to display an interactive login/password reset dialog
 *
 * @author Ben Talberg <ben.talberg@appcanny.com>
 * @link http://appcanny.com
 * @copyright Copyright (C) 2013 Ben Talberg
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package CTS
 * @since 1.0
 */
?>
<?php
  $url = home_url( '/' );
  $login_url = $url . "wp-login.php";
  $pwd_reset_url = $url . "wp-login.php?action=lostpassword&amp;redirect_to=" . $url;
  
  $is_user_logged_in = is_user_logged_in();
  if ( $is_user_logged_in ) {
    global $current_user;
    get_currentuserinfo();
  }
?>

<ul class="sf-menu sf-js-enabled" id="login-menu">	
  <?php if ( !$is_user_logged_in ) : ?>
    <li class="menu-item login-form-link right"><a href=""><span> Login</span></a>
	      <ul style="display: none; visibility: hidden;">
	        <form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo $login_url ?>" method="post">
					  <input type="text" name="log" id="side-user-login" class="input" value="" placeholder="Username">
				
					  <input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" placeholder="Password">
				
					  <div class="sidebar-login-button">
					    <label>
					      <input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever">Remember Me
					    </label>
					  </div>
				
				    <input type="submit" class="button orange" name="wp-submit" id="sidebar-wp-submit" value="Log In" tabindex="100">
					  <input type="hidden" name="testcookie" value="1">
				</form>
			
				<div id="bp-connect-buttons">
				  <a id="lost-password-link" href="<?php echo $pwd_reset_url ?>" title="Lost Password"><span>Lost Password?</span></a>
				</div>
      </ul>
	  </li>
	<?php else : ?>
	  <li class="menu-item user-settings right"><a href="<?php echo( " " . bp_loggedin_user_domain( '/' ) ) ?>"><span><?php echo( " " . $current_user->user_login ) ?></span></a>
	<?php endif; ?>
</ul>