<?php
/*
Plugin Name: google_sheets
Version: 1.0
Author: Ivanochko Andrii
Description: Використання гугл таблиць
*/

defined('ABSPATH') or die("Hey! this not allowed");

define( 'PLUGIN_NAME_SHEATS', 'google_sheets');
define( 'PLUGIN_CLASS_NAME_SHEATS', 'googleSheets');
define( 'PLUGIN_FILE_SHEATS', __FILE__);
define( 'PLUGIN_DIR_SHEATS', untrailingslashit( dirname( __FILE__ ) ) );
define( 'PLUGIN_URL_SHEATS', plugins_url().'/'.(PLUGIN_NAME_SHEATS).'/');


class googleSheets {
    static $debug = true;
    static $settings = [];
    static $is_token = false;
    static $token_path;
    static $first_start = true;
    static $google_sheets_API;

    static function init(){
        require_once PLUGIN_DIR_SHEATS . '/modules/API/API.php';
        self::$token_path = PLUGIN_DIR_SHEATS . '/modules/API/tokens/';
        self::$google_sheets_API = new google_sheets_API(self::$token_path);
        register_activation_hook(PLUGIN_FILE_SHEATS,array(PLUGIN_CLASS_NAME_SHEATS,'activation_hook'));
        self::set_default_options();
        require_once PLUGIN_DIR_SHEATS . '/modules/settings_page/settings_page.php';
        add_filter( 'plugin_action_links', array(PLUGIN_CLASS_NAME_SHEATS,'plugin_action_links'), 10, 2 );

        self::add_route_init_google();
    }

    static function getClient()
    {
        return self::$google_sheets_API->getClient();
    }

    static function add_route_init_google(){

        add_filter( 'init', function(){

            if(self::$is_token == false){
                add_rewrite_rule(
                    '^google_sheets$',
                    'index.php?google_sheets="true"',
                    'top'
                );
                add_filter( 'query_vars', function($vars){
                    $vars[] = 'google_sheets';
                    return $vars;
                });
                add_action( 'template_redirect', function(){
                    if( $google_sheets = get_query_var( 'google_sheets' ) ){
                        $path = PLUGIN_DIR_SHEATS . '/modules/started.php';
                        if(file_exists($path)){
                            include_once $path;
                            exit();
                        }else{
                            status_header( 404 ); nocache_headers(); include( get_query_template( '404' ) ); die();
                        }
                    }
                    
                });

                if(self::$first_start){
                    flush_rewrite_rules(); self::$first_start = false;
                }
            }

        });

    }

    static function activation_hook(){
        self::add_route_init_google();
    }

    static function set_default_options(){
        $settings = array(
            'key_opt1' => 40,
        );
        $opts = get_option(PLUGIN_CLASS_NAME_SHEATS);
        if(is_array($opts)) $settings = array_merge($settings,$opts);
        self::$settings = $settings;
    }

    static function get_option($name){
        if(!isset(self::$settings[$name])) return false;
        if(empty(self::$settings[$name])) return false;
        return self::$settings[$name];
    }

    static function plugin_action_links($actions, $plugin_file){
        if(false === strpos( $plugin_file, PLUGIN_NAME_SHEATS )) return $actions;
        $settings_link = '<a href="options-general.php?page='.PLUGIN_CLASS_NAME_SHEATS.'">Налаштування</a>'; array_unshift( $actions, $settings_link ); 
        return $actions; 
    }
}

if(class_exists(PLUGIN_CLASS_NAME_SHEATS)){
    (PLUGIN_CLASS_NAME_SHEATS)::init();
}
