<?php 
/*
  Plugin Name: Wishlist
  Plugin URI: https://github.com/farhankk360/osclass-wishlist-plugin/
  Description: This plugin lets you add items / ads to your wishlist to keep track of them.
  Version: 1.0.0
  Author: Farhan Ullah
  Author URI: http://www.github.com/farhankk360
  Author Email: farhankk360@hotmail.com
  Short Name: WishList
  Plugin update URI: https://github.com/farhankk360/osclass-wishlist-plugin/
 */

  define('WISHLIST_VERSION', '1.0.0');
  
  //Functions
  //plugin installation
  function wishlist_call_after_install(){
    $db   = getConnection();
    $path = osc_plugin_resource('fk_wishlist/struct.sql');
    $sql  = file_get_contents($path);
    $db->osc_dbImportSQL($sql);
  };

  function wishlist_uninstall(){
    $db = getConnection();
    $db->osc_dbExec('DROP TABLE %st_item_wishlist', DB_TABLE_PREFIX);
  };

  //add to wishlist button
  function wishlist($id, $item_added = false) {
    if($item_added) {
      //materialize classes has been used by default, but you can use your custom classes. 
      echo '<a href="javascript://" class="btn-flat waves-effect btn-flat wishlist added" id="' . $id . '" title="Remove item from your wishlist">';
      echo '<i class="fas fa-heart"></i>';
    }else {
      echo '<a href="javascript://" class="btn-flat waves-effect btn-flat wishlist" id="' . $id . '" title="Add item to your wishlist">';
      echo '<i class="far fa-heart"></i>';
    }
    echo '</a>';
  };

  //check wishlist item
  function wishlist_item_check($id){
    //check if user is logged in
    if ( osc_is_web_user_logged_in() ) {
     //check if the item is not already in the wishlist
     $db   = getConnection();
     $data = $db->osc_dbFetchResult("SELECT * FROM %st_item_wishlist WHERE fk_i_item_id = %d and fk_i_user_id = %d", DB_TABLE_PREFIX, $id, osc_logged_user_id());

     //If nothing return false
     if (!isset($data['fk_i_item_id'])) {

        wishlist($id);
     } else {

      //item already in wishlist
         wishlist($id, $item_added = true);
     }
    } else {
      wishlist($id);
    }
  };


  //Js Scripts
  //if osclass version is < 3.1.1
  function wishlist_footer() {
    echo '<!-- Wishlist js -->';
    echo '<script type="text/javascript">';
    echo 'var wishlist_url = "' . osc_ajax_plugin_url('fk_wishlist/ajax_wishlist.php') . '";';
    echo '</script>';
    echo '<script type="text/javascript" src="' . osc_plugin_url('fk_wishlist/js/wishlist.js') . 'wishlist.js"></script>';
    echo '<!-- Wishlist js end -->';
  };

  //else
  function wishlist_scripts_loaded() {
    echo '<!-- Wishlist js -->';
    echo '<script type="text/javascript">';
    echo 'var wishlist_url = "' . osc_ajax_plugin_url('fk_wishlist/ajax_wishlist.php') . '";';
    echo '</script>';
    echo '<!-- Wishlist js end -->';
  };

  if(osc_version() < 311) {
    osc_add_hook('footer', 'wishlist_footer');
  } else {
    osc_add_hook('scripts_loaded', 'wishlist_scripts_loaded');
    osc_register_script('wishlist', osc_plugin_url('fk_wishlist/js/wishlist.js') . 'wishlist.js', array('jquery'));
    osc_enqueue_script('wishlist');
  }


  //Hooks
  //install wishlist plugin
  osc_register_plugin(osc_plugin_path(__FILE__), 'wishlist_call_after_install');

  //uninstall wishlist plugin
  osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'wishlist_uninstall');

  //delete item
  osc_add_hook('delete_item', 'wishlist_delete_item');
?>