## Плагін `CMS Wordpress` для використання `Google Sheets API`

### Інструкція :

1. Скопіювати в папку wp-content/plugins

2. Створити проект на "Google Cloud Platform" це можна зробити за посиланням https://console.cloud.google.com/projectcreate

![](image/instruction/create_project.png)

3. Включити "Google Sheets API" https://console.cloud.google.com/apis/library

![](image/instruction/search_google_sheetsAPI.png)
![](image/instruction/activate_google_sheetsAPI.png)

4. Створити дозвіл "OAuth client ID"

![](image/instruction/search_credentials.png)
![](image/instruction/select_credentials.png)

5. Дозволити перенаправлення на "https://mydomain.ru/google_sheets/"

![](image/instruction/set_credentials_redirect.png)

6. Завантажити створений ключ
![](image/instruction/download_credentials.png)

7. Додати користувача від імені якого будемо робити запис в таблиці (це пов'язано з тим що спершу авторизація буде працювати в тестовому режимі і авторизуватись зможуть тільки додані в білий список користувачі)

![](image/instruction/add_test_user.png)

8. Перейти до майстра налаштування плагіна і слідувати інструкціям

![](image/instruction/link_settings.png)
![](image/instruction/link_settings2.png)