# Документация API Мок-Сервера

## Эндпоинты

API обрабатывает HTTP-запросы через один эндпоинт. URL эндпоинта - `https://rtlm.tableer.com`

## Методы запросов

- **POST**: сервер игнорирует любые запросы, кроме POST-запросов.
- **COOKIE**: сервер может обрабатывать установленные клиентом COOKIE, сервер также устанавливает собственные COOKIE для
  личных нужд.

## Формат данных

- **Входные данные** передаются в теле запроса в формате `application/x-www-form-urlencoded` или `multipart/form-data`.
- **Ответы** отправляются в формате JSON ([JSend-структура](https://github.com/omniti-labs/jsend)).

## Ошибки

При ошибке в поле `actor` будет возвращена ошибка:

```
{
  "status": "error",
  "data": "unknown actor"
}
```

При ошибке в поле `action`, специфичного для конкретного `actor` будет возвращена ошибка:

```
{
  "status": "error",
  "data": "unknown action"
}
```

Это единственные ошибки на данном уровне, все остальные являются специфичными для отдельных `action` и описаны ниже.

## Параметры запроса

- **actor**: Указывает на обработчик запроса.
- **action**: Определяет действие.
- **data**: JSON-объект с данными для обработки, опционально.

## Доступные вызовы

### Actor: `sessionManager`

- Назначение: обслуживание сессии/состояния
- Доступные `actions`:
    - **getSession**:
        - Описание: Возвращает текущее состояние сессии.
        - Параметры: Не требует дополнительных параметров.
        - Структура ответа:
          ```
          {
            "status": "success",
            "data": {
              "userId": BIGINT, // идентификатор пользователя, по умолчанию -1
              "loggedIn": BOOLEAN, // флаг наличия сессии, по умолчанию 'false'
              "userName": STRING, // имя пользователя, по умолчанию 'Guest'
              "role": STRING // роль пользователя в системе, по умолчанию 'unknown'
            }
          }
          ```
        - Ошибки: Не предусмотрены.
    - **tryToLogOut**:
        - Описание: Очищает текущую сессию, отзывает аутентификацию.
        - Параметры: Не требует дополнительных параметров.
        - Структура ответа:
          ```
          {
            "status": "success",
            "data": "session flushed"
          }
          ```
        - Ошибки: Не предусмотрены.
    - **tryToLogIn**:
        - Описание: Аутентификация пользователя.
        - Параметры:
            - `login`: Логин пользователя.
            - `password`: Пароль пользователя.
        - Структура ответа:
          ```
          {
            "status": "success",
            "data": "access granted"
          }
          ```
        - Ошибки:
            - **unknown user**: Пользователь не найден.
              ```
              {
                "status": "error",
                "data": "unknown user"
              }
              ```
            - **corrupted data**: Неполные или некорректные данные.
              ```
              {
                "status": "error",
                "data": "corrupted data"
              }
              ```
            - **already logged in**: Попытка входа после входа в систему.
              ```
              {
                "status": "error",
                "data": "already logged in"
              }
              ```
    - **reserveLogin**:
        - Описание: Резервирует логин за пользователем перед регистрацией.
        - Параметры:
            - `login`: Логин пользователя.
            - Структура ответа:
              ```
              {
                "status": "success",
                "data": "login reserved"
              }
              ```
        - Ошибки:
            - **login occupied**: Логин уже занят.
               ```
               {
                 "status": "error",
                 "data": "login occupied"
               }
               ```
            - **empty login**: Передан пустой логин.
              ```
              {
                "status": "error",
                "data": "empty login"
              }
              ```
            - **already logged in**: Попытка резервирования после входа в систему.
              ```
              {
                "status": "error",
                "data": "already logged in"
              }
              ```
    - **registerLogin**:
        - Описание: Регистрирует логин в системе.
        - Параметры:
            - `login`: Логин пользователя.
            - `password`: Пароль пользователя.
            - Структура ответа:
              ```
              {
                "status": "success",
                "data": "login registered"
              }
              ```
        - Ошибки:
            - **login occupiedr**: Логин уже занят.
               ```
               {
                 "status": "error",
                 "data": "login occupied"
               }
               ```
            - **empty login**: Передан пустой логин.
              ```
              {
                "status": "error",
                "data": "empty login"
              }
              ```
            - **empty password**: Передан пустой пароль.
              ```
              {
                "status": "error",
                "data": "empty password"
              }
              ```
            - **login not reserved**: Попытка регистрации незарезервированного логина.
              ```
              {
                "status": "error",
                "data": "login not reserved"
              }
              ```
            - **already logged in**: Попытка регистрации после входа в систему.
              ```
              {
                "status": "error",
                "data": "already logged in"
              }
              ```

### Actor: `coursesManager`

- Назначение: обслуживание курсов/модулей
- Доступные `actions`:
    - **getAvailableCourses**
        - Описание: выгружает список с краткой информацией по доступным курсам
        - Параметры: Не требует дополнительных параметров.
        - Структура ответа:
            ```
            {
              "status": "success",
              "data": [{
                "id": INT, // идентификатор курса
                "title": STRING, // Название курса
                "description": STRING // Краткое описание курса?
                "icon": URL(?)|STRING // Путь к иконке или имя иконки (уточнить)
              }, ...]
            }
            ```
        - Ошибки: Не предусмотрены.

    - **getCourseInfo**
        - Описание: Получает подробную информацию о конкретном курсе
        - Параметры:
            - `courseId`: INT // идентификатор курса
        - Структура ответа:
            ```
            {
              "status": STRING, // "success" или "error"
              "data": {
                "dateStart": STRING, // Дата начала курса
                "dateEnd": STRING, // Дата окончания курса
                "timeEstimation": INT, // Примерное время на изучение курса в часах
                "modules": [STRING, ...], // Список модулей курса
                "longDescription": STRING // Подробное описание курса
              }
            }
            ```
        - Ошибки:
            - **unknown courseId**: `courseId` отсутствует или курс с таким `courseId` не найден.
                ```
                {
                  "status": "error",
                  "data": "unknown courseId"
                }
                ```

    - **getUserCourses**
        - **Описание**: Получает список курсов, на которые зарегистрирован пользователь.
        - **Параметры**: Нет параметров, использует идентификатор пользователя из сессии.
        - **Структура ответа**:
            ```
            [
              {
                "status": STRING, // "success" или "error"
                "data": [
                  {
                    "id": INT, // Идентификатор курса
                    "completeness": INT, // Процент завершенности курса пользователем
                    "modules": ARRAY // Массив модулей курса, в данном методе будет пустым
                  },
                  ...
                ]
              }
            ]
            ```
        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
              "status": "error",
              "data": "not logged in"
              }
              ```

    - **getUserCourseModules**
    - **Описание**: Получает информацию о модулях конкретного курса для пользователя.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
        - **Структура ответа**:
            ```
            [
              {
                "status": STRING, // "success" или "error"
                "data": [
                  {
                    "name": STRING, // Название модуля
                    "lessonsCompleted": INT, // Количество завершенных уроков
                    "lessonsTotal": INT, // Общее количество уроков
                    "deadline": STRING, // Срок сдачи модуля
                    "estimatedTime": INT, // Оценочное время на изучение модуля в миллисекундах
                    "performance": INT // Процент выполнения модуля
                  },
                  ...
                ]
              }
            ]
            ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                  ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                  ```
                - **unknown courseId**: `courseId` отсутствует или курс с таким `courseId` не найден.
                  ```
                  {
                  "status": "error",
                  "data": "unknown courseId"
                  }
                  ```