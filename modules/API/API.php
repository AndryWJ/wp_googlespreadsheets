<?php
require_once 'google-api-php-client-master/vendor/autoload.php';

class google_sheets_API{
    private $token_path;
    public $client;

    function __construct($token_path)
    {
        $this->token_path = $token_path;
    }

    function getClient()
    {
        if(is_a($this->client,'Google_Client')){ return $this->client; };
        $this->client = new Google_Client();
        
        $this->client->setApplicationName('Google Sheets API PHP Quickstart');
        $this->client->setScopes([
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Sheets::DRIVE,
            Google_Service_Sheets::DRIVE_FILE
        ]);
        $this->client->setIncludeGrantedScopes(true);
        $this->client->setAuthConfig($this->token_path.'credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        
        
        $tokenPath = $this->token_path.'token.json';
        
        if (file_exists($tokenPath)){
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }else if(!isset($_GET['step'])){ echo "Токен не існує!"; }

        if ($this->client->isAccessTokenExpired()) {

            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {

                $authUrl = $this->client->createAuthUrl();
                if(isset($_GET['step'])){
                    printf("Перейдите по <a href=\"%s\">ссылке</a> чтобы разрешить приложению доступ к таблицам!", $authUrl);
                }

                $authCode = (isset($_GET['code']))?$_GET['code']:'';
                if(empty($authCode)) return;

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }

            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }
        return $this->client;
    }


    /**
     * [insert_row description]
     *
     * @param   [type]  $table_id  [ID таблиці в яку вставляємо ряд]
     * @param   [type]  $data      [Масив данних які будуть записані в ряд таблиці]
     *
     * @return  [type]             [return відповідь від гугла]
     */
    function insert_row($table_id,$data){
        $client = $this->getClient(); 
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

}