<?php
/*
Plugin Name: Responsive Background by DJJMZ
Plugin URI:
Description: Easy way change background image/color to fully responsive image. Compatible with all browsers: computers, all phone and tablets.
Version: 1.2
Author: djjmz
Author URI:
*/
add_action('wp_head', 'rb_custom_css') ;
function rb_custom_css()
{
  echo "<style type=\"text/css\">
body {
background-image:url('" . get_option('rb_background') . "');
background-repeat: no-repeat;
background-position: center center;
background-attachment: fixed;
background-size: cover;
}
</style>\r\n" ;
}
add_action('admin_menu', 'rb_add_menu') ;
function rb_add_menu()
{
  add_options_page('RB Settings', 'RB Settings', 'manage_options', 'rb_settings', 'rb_settings') ;
}
function rb_settings($current = 'external')
{
  $active_tab = isset ($_GET['tab']) ? $_GET['tab'] : 'external' ;
  if ( isset ($_GET['tab']))
    $active_tab = $_GET['tab'];
  $message = '' ;
  if ( isset ($_POST['add']))
  {
    if ( isset ($_POST['url']))
    {
      if ( rb_checkRemoteFile($_POST['url']))
      {
        $option_exists = ( get_option('rb_background', null) !== null ) ;
        if ($option_exists)
        {
          update_option( 'rb_background', sanitize_text_field($_POST['url'])) ;
        }
        else
        {
          add_option( 'rb_background', sanitize_text_field($_POST['url'])) ;
        }
        $message = 'The background has been changed' ;
      }
      else
      {
        $message = 'Incorrect file format' ;
      }
    }
  }
  if ( isset ($_POST['upload']))
  {
    if ($_FILES['file']['name'] != '')
    {
      $file = $_FILES['file']['name'];
      $upload_dir = wp_upload_dir() ;
      $upload_path = $upload_dir['path'] . '/' ;
      $allowed_filetypes = array('.jpg', '.png', '.gif', '.bmp') ;
      $ext = substr( $file, strpos($file, '.'), strlen($file) - 1 ) ;
      if ( !in_array($ext, $allowed_filetypes))
      {
        $message = 'Incorrect file format' ;
      }
      else
      {
        $file = str_replace(' ', '_', $file) ;
        $file = strtolower($file) ;
        if ( move_uploaded_file($_FILES['file']['tmp_name'], $upload_path . $file))
        {
          $message = 'The background has been changed' ;
          $option_exists = ( get_option('rb_background', null) !== null ) ;
          if ($option_exists)
          {
            update_option( 'rb_background', sanitize_text_field($upload_dir['url'] . '/' . $file)) ;
          }
          else
          {
            add_option( 'rb_background', sanitize_text_field($upload_dir['url'] . '/' . $file)) ;
          }
        }
        else
        {
          $message = 'Upload Failed. Please Check Your File Type!' ;
        }
      }
    }
  }
  $active_tab = isset ($_GET['tab']) ? $_GET['tab'] : 'external' ;
  if ( isset ($_GET['tab']))
    $active_tab = $_GET['tab'];
  $tabs = array('external' => 'External image', 'upload' => 'Upload image') ;
  echo '<h2>' ;
  foreach ($tabs as $tab => $name)
  {
    $class = ($active_tab == $tab) ? ' nav-tab-active' : '' ;
    echo '<a class="nav-tab' . $class . '" href="?page=rb_settings&tab=' . $tab . '">' . $name . '</a>' ;
  }
  echo '</h2><div style="background:#ffffff;text-align:center;margin-top: -15px;max-width:1250px;min-width:500px;padding-top: 25px;padding-bottom: 25px;">' ;
  if ($message != NULL)
    echo '<b>' . $message . '</b>' ;
  if ($active_tab == 'external')
  {
    echo '<form method="post" action = "">
		Background URL:<br>
        <input type="text" name="url" value="' . get_option('rb_background') . '" size="50"/><br>
		<input type="submit" name="add" value="Add">
		</form>' ;
  }
  elseif ($active_tab == 'upload')
  {
    echo '<form method="post" action = "" enctype="multipart/form-data">
        Choose file:<br>
        <input type="file" name="file" id="file"><br>
		<input type="submit" name="upload" value="Upload">
		</form>' ;
  }
  echo '</div>' ;
}
function rb_checkRemoteFile($url)
{
  $ch = curl_init() ;
  curl_setopt($ch, CURLOPT_URL, $url) ;
  curl_setopt($ch, CURLOPT_NOBODY, 1) ;
  curl_setopt($ch, CURLOPT_FAILONERROR, 1) ;
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
  $content = curl_exec($ch) ;
  if ($content !== FALSE)
  {
    if ( rb_is_image($url))
      return true ;
    else
      return false ;
  }
  else
  {
    return false ;
  }
}
function rb_is_image($path)
{
  $a = getimagesize($path) ;
  $image_type = $a[2];
  if ( in_array( $image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP)))
  {
    return true ;
  }
  return false ;
}