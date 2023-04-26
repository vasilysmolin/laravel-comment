
# About Project

- Система комментариев rest-full api + vue

## Требования

* PHP >= 8.1
* Composer >= 2
* make >= 4

[Что такое "Менеджер версий"](https://guides.hexlet.io/version_managers/)

## Стандарты кодирования и прочие правила

* Пулреквесты должны быть настолько маленькими, насколько это возможно с точки зрения здравого смысла
* Весь код должен соответствовать стандартам кодирования PSR12 и Laravel (используем немного кастома, чтобы усложнить жизнь разработчика)
* Пулреквест должен проходить все проверки CI
* Все экшены контроллеров должны быть покрыты тестами ([Начинаем писать тесты (правильно)](https://ru.hexlet.io/blog/posts/how-to-test-code))
* Формы делаются с помощью [laraeast/laravel-bootstrap-forms](https://github.com/laraeast/laravel-bootstrap-forms)
* В подавляющем большинстве используется ресурсный роутинг. Что под него не подходит, сначала обсуждается (такое бывает крайне редко)
* Тексты только через локали
* Чтобы включить логирование Rollbar, необходимо установить переменную `LOG_CHANNEL=rollbar` и `ROLLBAR_TOKEN=` ([docs](https://docs.rollbar.com/docs/laravel))
* Для генераций файлов-помощников (для автодополнения) используйте `make ide-helper`
* Изучите список доступных команд `php artisan`!



## Install and start project
Установка докера и docker-compose

curl -sSL get.docker.com | sh
sudo apt-get install docker-compose

### Для прода

sudo apt install -y python3-certbot-nginx
certbot --nginx

### Для локалки
openssl req -x509 -nodes -days 365 \
-subj '/C=CA/ST=QC/O=Company, Inc./CN=laravel-comment.ru' \
-addext 'subjectAltName=DNS:'laravel-comment.ru \
-newkey rsa:4096 \
-keyout /etc/nginx/ssl/laravel-comment.ru.key -out /etc/nginx/ssl/laravel-comment.ru.cer

```
hosts 127.0.0.1 laravel-comment.ru
```

# Запус проекта
Необходимо настроить локальный nginx.
Конфигурация лежит в папке images/local-nginx

make start или docker-compose up -d

## Tests and lint

* `make lint`
* `make test`


