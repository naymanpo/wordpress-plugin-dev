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


        //create rewrite rule route
        register_activation_hook( __FILE__, array( $this,'flush_application_rewrite_rules' ) );

        //add query variable
        add_filter( 'query_vars', array( $this,'manage_user_routes_query_vars' ) );

        //add front controller
        add_action( 'template_redirect', array( $this, 'front_controller') );

        add_action('wpwa_register_user',array($this,'register_user'));

        add_action( 'wpwa_activate_user', array( $this, 'activate_user' ));
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

    /**
     * add application user capabilities
     */
    public function add_application_user_capabilities(){
        $role = get_role( 'follower' );
        $role->add_cap( 'follow_developer_activities' );
    }


    /**
     * create route
     */

     public function manage_user_route(){
        add_rewrite_rule( '^user/([^/]+)/?','index.php?wpa_user=$matches[1]', 'top' );
     }
     /**
      * add query variable
      */
     public function manage_user_routes_query_vars( $query_vars ) {
        $query_vars[] = 'wpa_user';
        return $query_vars;
    }

    public function flush_application_rewrite_rules() {
        $this->manage_user_route();
        flush_rewrite_rules();
    }

    public function front_controller() {
        global $wp_query;
        $wpa_user = isset ( $wp_query->query_vars['wpa_user'] ) ? $wp_query->query_vars['wpa_user'] : ''; ;
        switch ( $wpa_user ) {
            case 'register':
                do_action( 'wpwa_register_user' );
                break;
            case 'activate':
                do_action( 'wpwa_activate_user' );
        }
    }
    public function register_user() {
        if ( !is_user_logged_in() ) {
            include dirname(__FILE__) . '/templates/register.php';
            exit;
        }
    }

    public function activate_user() {
        $activation_code = isset( $_GET['activation_code'] ) ?
        $_GET['activation_code'] : '';
        $message = '';
        // Get activation record for the user
        $user_query = new WP_User_Query(
            array(
                'meta_key' => 'activation_code',
                'meta_value' => $activation_code
                )
            );
            $users = $user_query->get_results();
            // Check and update activation status
            if ( !empty($users) ) {
                $user_id = $users[0]->ID;
                update_user_meta( $user_id, 'activation_status', 'active' );
                $message = 'Account activated successfully. ';
            } else {
                $message = 'Invalid Activation Code';
            }
                include dirname(__FILE__) . '/templates/info.php';
                exit;
    }

   public function random_string() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
}

 $wpa_user_manager=new WPA_User_Manager();