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
    static $token_path;
    static $is_token = false;
    static $first_start = true;

    static function init(){
        self::$token_path = PLUGIN_DIR_SHEATS . '/tokens/';

        register_activation_hook(PLUGIN_FILE_SHEATS,array(PLUGIN_CLASS_NAME_SHEATS,'activation_hook'));
        self::set_default_options();
        require_once PLUGIN_DIR_SHEATS . '/modules/settings_page/settings_page.php';
        require_once PLUGIN_DIR_SHEATS . '/includes/google-api-php-client-master/vendor/autoload.php';
        add_filter( 'plugin_action_links', array(PLUGIN_CLASS_NAME_SHEATS,'plugin_action_links'), 10, 2 );

        self::add_route_init_google();
    }

    static function getClient()
    {
        
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes([
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Sheets::DRIVE,
            Google_Service_Sheets::DRIVE_FILE
        ]);
        $client->setIncludeGrantedScopes(true);
        $client->setAuthConfig(self::$token_path.'credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = self::$token_path.'token.json';
        
        if (file_exists($tokenPath)){
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        if ($client->isAccessTokenExpired()) {

            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {

                $authUrl = $client->createAuthUrl();
                if(isset($_GET['step'])){
                    printf("Перейдите по <a href=\"%s\">ссылке</a> чтобы разрешить приложению доступ к таблицам!", $authUrl);
                }

                $authCode = (isset($_GET['code']))?$_GET['code']:'';
                if(empty($authCode)) return;

                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }

            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }


    /**
     * [insert_row description]
     *
     * @param   [type]  $table_id  [ID таблиці в яку вставляємо ряд]
     * @param   [type]  $data      [Масив данних які будуть записані в ряд таблиці]
     *
     * @return  [type]             [return відповідь від гугла]
     */
    static function insert_row($table_id,$data){
        $client = googleSheets::getClient(); 
        if(is_a($client,'Google_Client')){
            $service = new Google_Service_Sheets($client);
            $spreadsheetId = $table_id;
            $range = 'A1';
            $requestBody = new Google_Service_Sheets_ValueRange();
            $requestBody->setValues(['values'=>$data]);
            $response = $service->spreadsheets_values->append($spreadsheetId, $range, $requestBody, ['valueInputOption'=>'RAW']);
            return $response;
        }else{
            return "Помилка запису в таблицю!";
        };
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
