

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Майстер налаштування гугл API</title>
    <style>
        .step_1--instruction{
            overflow: hidden;
        }
        .btn{
            padding: 10px 35px;
            background: #fb7844;
            margin: 2em 0;
            text-decoration: none;
            color: #fff;
            display: block;
            text-align: center;
        }

        .btn:hover{
            background: #ffaa87;
        }
        h2{
            margin: 2em 0;
            color: #000000;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px solid #fb7844;
        }
        a{
            color: #1A73E8;
        }
        p{
            font-size: 20px;
            font-weight: 600;
            margin: 2em 0;
        }
        img{
            border: 1px solid #000;
            margin-left: 70px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <?php

    function errors_hendler_started($th,$errstr = '',$errfile = '',$errline = ''){
        echo "<div style=\"color:red\"><h3>Помилка:</h3>";
            if(is_a($th,'Exception')){
                echo $th->getMessage();
            }else{
                echo $errstr."\n".$errfile."\n".$errline;
            }
        echo "<div>";
    }

    set_exception_handler('errors_hendler_started');
    set_error_handler('errors_hendler_started');
    

    if(isset($_GET['step']) && $_GET['step'] == 1){ ?>
        <div class="step_1--instruction">
            <h2>Крок 1 Створення проекта, активація "Google Sheets API", настройка дозволів</h2>
            <p>Створити проект на "Google Cloud Platform" це можна зробити за посиланням <a href="https://console.cloud.google.com/projectcreate" target="_blank">https://console.cloud.google.com/projectcreate</a></p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/create_project.png'?>">
            <p>Включити у проекта API для роботи з гугл-таблицями "Google Sheets API" <a href="https://console.cloud.google.com/apis/library" target="_blank">https://console.cloud.google.com/apis/library</a></p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/search_google_sheetsAPI.png'?>">
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/activate_google_sheetsAPI.png'?>">
            <p>Створити дозвіл "OAuth client ID"</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/search_credentials.png'?>">
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/select_credentials.png'?>">
            <p>Дозволити перенаправлення на "https://mydomain.ru/google_sheets/"</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/set_credentials_redirect.png'?>">
            <p>Завантажити створений ключ (він знадобиться на кроці 2)</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/download_credentials.png'?>">
            <p>Додати користувача від імені якого будемо робити запис в таблиці (це пов'язано з тим що спершу авторизація буде працювати в тестовому режимі і авторизуватись зможуть тільки додані в білий список користувачі)</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/add_test_user.png'?>">
            
            <?php /* ?> 
            <p>Переходим по <a href="https://developers.google.com/sheets/api/quickstart/php" target="_blank">ссылке</a> чтобы создать порект и автоматически включить google sheets api </p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/step_1.png'?>">
            <p>Задем название проекта</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/step_1-2.png'?>">
            <p>Указываем url "https://my_domain.ru/google_sheets/" (Заменяем название домена на свое)</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/step_1-3.png'?>">
            <p>Скачиваем файл настройки "credentials.json"</p>
            <img src="<?=PLUGIN_URL_SHEATS.'image/instruction/step_1-4.png'?>">
            <?php */ ?>
            <a class="btn" href="/google_sheets/?step=2">Крок 2</a>
            
        </div>
    <?php } ?>

    <?php if(isset($_GET['step']) && $_GET['step'] == 2){ ?>
            <?php 
                if(isset($_POST["submit_credentials"])) {
                    if($_FILES["credentials"]["type"] != 'application/json'){
                        echo "<br>Не верный тип файла!";
                    }else{
                        $target_file = googleSheets::$token_path.basename('credentials.json');
                        $result = copy($_FILES["credentials"]["tmp_name"], $target_file);
                        if ($result) {
                            echo "<br>Файл загружен! ";
                            $client = googleSheets::getClient();
                        } else {
                            echo "<br>Извините не удалось загрузить ваш файл!";
                        }
                    }
                }else{ ?>
                    <form method="post" enctype="multipart/form-data"><p>Завантажте створений ключ</p>
                        <input type="file" name="credentials" id="fileToUpload">
                        <input type="submit" value="Загрузить" name="submit_credentials">
                    </form>
                <?php }
        } ?>

    <?php if(!isset($_GET['step'])){
        $client = googleSheets::getClient();
        if(is_a($client,'Google_Client')){
            echo "<br>Токен успешно создан!";
        }else{
            echo "<br>Ошибка создания токена, возможно нету прав на папку в которую пишем токен!";
        };
    } ?>

</body>
</html>

<?php 











// Get the API client and construct the service object.
// $client = googleSheets::getClient();




// // Prints the names and majors of students in a sample spreadsheet:
// // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
// $spreadsheetId = '1Nfh2koOeHuArhddJdAwNnpP7LVxfAAXxwABR437FuoI';
// $range = 'Class Data!A2:E';
// $response = $service->spreadsheets_values->get($spreadsheetId, $range);
// $values = $response->getValues();

// if (empty($values)) {
//     print "No data found.\n";
// } else {
//     print "Name, Major:\n";
//     foreach ($values as $row) {
//         // Print columns A and E, which correspond to indices 0 and 4.
//         printf("%s, %s\n", $row[0], $row[4]);
//     }
// }



// // var_dump($service);
