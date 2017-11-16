# avtoclssika.com

## Запуск
Домен для разработки `avtoclassika.uwinart.local`.
Домен на staging-сервере `avtoclassika.uwinart.com`.
Домен на production `avtoclassika.com.ua`.

Запуск происходит с помощью Docker и настраивается в `fig/*.yml`. Для запуска и инициализации develop версии следует выполнить следующую комманду:
```
fig -f fig/develop.yml -p avtoclassika up -d
```
Так как база данных не инициирована, контейнер sphinx запущен не будет.
Создаем и инициируем базу данных с основными модулями:
```
./database/createdb --initdb --with-modules
```
Если требуется создать базу данных и восстановить с бекапа:
```
./database/createdb
./database/restoredb
```

Далее следует перезапустить docker:
```
fig -f fig/develop.yml -p avtoclassika stop
fig -f fig/develop.yml -p avtoclassika up -d
```

Если до этого не был запущен nginx-proxy, который проксирует 80 порт для всех docker контейнеров, запускаем его:
```
docker run -d -p 80:80 -v /var/run/docker.sock:/tmp/docker.sock -t uwinart/proxy80
```

*enjoy*
