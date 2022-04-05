# Чат-бот Гаряча лінія
![](https://img.shields.io/badge/Version-1.3-green) ![](https://img.shields.io/badge/Base-CodeIgniter-orange) [![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

Сервіс "Гаряча лінія" для управління зверненнями надісланими у Telegram та Viber чат бот на основі CodeIgniter. Адміністратори мають адміністративний розділ для керування зверненнями, користувачами та повідомленнями.



## Вимоги
- PHP version 7.3+
- MySQL 5.1+
- cURL



## Встановлення

### 1. Реєстрація чат-ботів

Реєструємо чат-боти у Telergam та Viber із однаковою назвою. Наприклад HotLineTestBot. Отримуємо токени доступу.


### 2. Налаштування конфігурації

Перейменовуємо файл **env** в **.env** та відкриваємо для редагування. Прописуємо наступні обов'язкові параметри:

**Параметри бота**

| Параметр | Опис |
| ---- | ---- |
| TelegramKey | Токен зареєстрованого Telegram бота |
| TelegramSticker | Код стикера для Telegram. Виводиться при стартовій команді |
| ViberKey | Токен зареєстрованого Viber бота |
| BotName | Назва бота |

**Авторизація**
| Параметр | Опис |
| ---- | ---- |
| AuthVerification | Двоетапна перевірка авторизації. **0** - вимкнена, **1** - увімкнена. На пошту користувачу приходить код із авторизацією. |

**Налаштування надсилання пошти**
| Параметр | Опис |
| ---- | ---- |
| EmailFrom | Пошта відправника |
| EmailName | Назва відправника |
| EmailProtocol | Протокол (smtp) |
| EmailHost | Поштовий сервер, хост |
| EmailPort | Порт (465) |
| EmailUser | Логін пористувача |
| EmailPass | Пароль користувача |
| EmailCrypto | Шифрування (ssl) |

**URL сервісу**
| Параметр | Опис |
| ---- | ---- |
| app.baseURL | URL вашого сервісу |

**Налаштування доступу до бази даних**
| Параметр | Опис |
| ---- | ---- |
| database.default.hostname |Сервер (localhost) |
| database.default.database | Назва бази |
| database.default.username | Користувач |
| database.default.password | Пароль |


### 3. База даних

Імпортуємо дамп бази з файлу `database/database.sql`


### 4. Вебхуки чат-ботів

Для реєстрації Webhook переходимо за посиланнями
| Бот | Посилання активаці Webhook |
| ---- | ---- |
|**Telegram**|https://testbot.com/telegram/setwebhook|
|**Viber**|https://testbot.com/viber/setwebhook|

> де `https://testbot.com/` доменна назва вашого сервісу

### 4. Завдання CRON
Для розсилки повідомлень усім користувачам чат-боту необхідно налаштувати CRON завдання.
Посилання для CRON завдання `https://testbot.com/cron`
Наприклад для щохвилиного запуску завдання `***** /usr/bin/curl -m 120 -s https://testbot.com/cron &>/dev/null`


## Тестовий адміністративний аккаунт

Сторінка авторизації: `https://testbot.com/login`

Логін: `test@test.com`

Пароль: `12345`

***

## Ліцензія
[license agreement](https://github.com/vasylzavalko/hotline/blob/main/LICENSE).