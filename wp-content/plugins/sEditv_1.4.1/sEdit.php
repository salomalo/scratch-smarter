<?php 
/*
Plugin Name: sEditor
Plugin URI: http://s-coder.com
Description: Online image editor
Version: 1.4.1
Author: Igor Simic
Author URI: http://s-coder.com
*/


/* ++++++++++++++++++++++++ Check WP version +++++++++++++++++++++++ */
 global $wp_version;
 if(!version_compare($wp_version,'3.0','>='))
 {
 	die('You need at least Wordpress 3.0 to use jsPlayer plugin');
 }
/* ++++++++++++++++++++++++ /Check WP version ++++++++++++++++++++++++ */


/**************************************** /HOOKS *******************************************************************/

global $pagenow;
//include functions
include('inc/functions.php');
	/*attach css and javascripts from functions file*/
		


//create dashboard menu
include('inc/create-dashboard-menu.php');

//display sEditor
include('sEdit-display-editor.php');

//seditor scripts
if(isset($_GET['page']) && $_GET['page']=='sEdit_plugin_editor_ID'){
    
	add_action('admin_enqueue_scripts', 'sEdit_register_jquery_only');
    add_action('admin_enqueue_scripts', 'sEdit_load_admin_scripts');
}

//seditor options script
if(isset($_GET['page']) && $_GET['page']=='sEdit_plugin_options_ID'){

	add_action('admin_enqueue_scripts', 'sEdit_register_jquery_only');
    add_action('admin_enqueue_scripts', 'sEdit_load_admin_settings_scripts');

}

//add sEdit button on media page
if($pagenow=='media.php'){
add_action('admin_enqueue_scripts', 'sEdit_admin_css');
    //add_action('admin_footer','add_my_media_button');
    add_action('admin_enqueue_scripts', 'sEdit_attach_sEdit_buttons_scripts');
    add_action('admin_enqueue_scripts', 'sendVarsTosEditInsertButtons');
 
    function sendVarsTosEditInsertButtons(){
         global $wp_version;
        
        //get current attachment ID
        $sEdit_current_id=$_GET['attachment_id'];

        //get attacjment mime type
        $sEdit_mime_type=get_post_mime_type($sEdit_current_id);

        //version check
        if(version_compare($wp_version,'3.2.1','<=')){
                     $sEdit_version_check="0";
                    }else{
                        $sEdit_version_check="1";
                    }

        //send vars to sEditInsertButtons.js
         wp_localize_script(
                'sEditorAttachButtons', 
                'sEditVars', array(
                    'sEdit_attachmentID'            =>$sEdit_current_id,
                    'sEdit_current_MIMEtype'        =>$sEdit_mime_type,
                    'sEdit_version_check'           =>$sEdit_version_check
                    ));
    }
    
 
	
}

if($pagenow=='upload.php'){
     add_action('admin_enqueue_scripts', 'sEdit_attach_sEdit_buttons_scripts');
	add_action('admin_enqueue_scripts', 'sEdit_admin_css');
}


//add sEdit button on media page when upload is finished
// add_filter( 'attachment_fields_to_edit', 'add_sEdit_button_when_image_uploaded_is_finished', null, 2 );
 
//  if($pagenow=='admin.php'){
// // //sEdit admin css
//    add_action('admin_head', 'sEdit_admin_css');
//}
add_action('admin_head', 'sEdit_admin_css');

