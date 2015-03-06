<?php
	/*
		Plugin Name: Responsive Background by DJJMZ
		Plugin URI: 
		Description: Easy way chabge background image/color to fully responsive image. Compatible with all browsers: compoters, all phone and tablets.
		Version: 1.0
		Author: djjmz
		Author URI: 
	*/
	add_action('wp_head', 'rb_custom_css');
	function rb_custom_css() {
    echo "<style type=\"text/css\">
body {	
background-image:url('".get_option('rb_background')."');	
background-repeat: no-repeat;
background-position: center center;
background-attachment: fixed;
background-size: cover;	
}
</style>\r\n";
}
	add_action('admin_menu', 'rb_add_menu');
	function rb_add_menu() {
		add_menu_page('RB Settings', 'RB Settings', 'manage_options', 'rb_settings', 'rb_settings', plugins_url('responsive-background/img/logo.jpg'), 6);
	}
	function rb_settings() {
		if(isset($_POST['change']) and !empty($_POST['url'])){
			if(checkRemoteFile($_POST['url'])){
			$option_exists = (get_option('rb_background', null) !== null);			
			if ($option_exists) {
				update_option('rb_background', $_POST['url']);
				} else {
				add_option('rb_background', $_POST['url']);
			}
			}
		}
		echo '<div class="wrap">
		<div class="metabox-holder has-center-sidebar"> 
		<div id="post-body">
		<div id="post-body-content">
		<div class="postbox">
		<div class="inside">
		<div align="center">
		<form method="post" action = "">
		Background URL:<br>
		<input name="url" type="text" value="'.get_option('rb_background').'" size="60"/><br>
		<input type="submit" name="change" value="Change">
		</form>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>';	
	}	
function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($ch);
    if($content!==FALSE)
    {
		if(is_image($url))
        return true;
	else
		return false;
    }
    else
    {
        return false;
    }
}
function is_image($path)
{
    $a = getimagesize($path);
    $image_type = $a[2];
     
    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
    {
        return true;
    }
    return false;
}