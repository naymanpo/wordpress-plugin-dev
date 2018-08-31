<?php
/*
 * 
 * Plugin Name: WPA User Management
 * Plugin URI:
 * Description: User Management Module 
 * Author: Nay Man
 * Version: 1.0
 * Author URI:
 */

 class WPA_User_Manager{
     public function __construct(){
        register_activation_hook(__FILE__,array($this,'add_application_user_roles'));   
        register_activation_hook(__FILE__,array($this,'remove_application_user_roles'));
    }


     public function add_application_user_roles(){
        /*
        add rule
        $result = add_role( 'role_name', 'Display Name', array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => false,
            ) );
        */
         //add new user roles
         add_role('follower','Follwer',array('read',true));
         add_role('developer','Developer',array('read',true));
         add_role('member','Member',array('member',true));
     }


     /**
      * remove application user roles
      */
     public function remove_application_user_roles(){
        remove_role('author');
        remove_role('editor');
        remove_role('contributor');
        remove_role('subscriber');
    }
 }

 $wpa_user_manager=new WPA_User_Manager();