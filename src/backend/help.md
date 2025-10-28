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

### Actor: `userManager`

- Назначение: обслуживание сессии/состояния
- Доступные `actions`:
    - ### getSession ###
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

    - ### tryToLogOut ###
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

    - ### tryToLogIn ###
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

    - ### reserveLogin ###
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

    - ### registerLogin ###
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

    - ### loadProfileData ###
        - **Описание**: Возвращает объект с информацией о пользователе, если пользователь успешно авторизован и профиль
          существует.
        - **Параметры**: Не требуются. Идентификатор пользователя определяется на основе текущей сессии.

        - **Структура ответа**:
          ```
          {
            "status": STRING, // "success" или "error"
            "data": {
              "firstName": STRING, // Имя пользователя
              "lastName": STRING, // Фамилия пользователя
              "birthDate": STRING, // Дата рождения в формате YYYY-MM-DD
              "gender": STRING, // Пол пользователя, например "male" или "female"
              "phone": STRING, // Телефонный номер в формате "+7XXXXXXXXXX"
              "email": STRING, // Электронная почта
              "mailingSettings": {
                "digest": BOOLEAN, // Настройки рассылки: дайджесты
                "eventsAgenda": BOOLEAN, // Настройки рассылки: агенда мероприятий
                "educationalMaterials": BOOLEAN, // Настройки рассылки: учебные материалы
                "submissionDeadlines": BOOLEAN // Настройки рассылки: сроки сдачи работ
              }
            }         
          }
          ```
        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
              "status": "error",
              "data": "not logged in"
              }
              ```
            - **profile not found**: Идентификатор пользователя отсутствует или профиль с таким идентификатором не
              найден.
                ```
                {
                "status": "error",
                "data": "profile not found"
                }
                ```

    - ### saveProfileData ###
        - **Описание**: Обновляет данные профиля пользователя на основе предоставленной информации. Эта функция
          позволяет
          выполнить частичное обновление данных профиля, обрабатывая только предоставленные поля, и сохраняет
          обновленный
          профиль.
        - **Параметры**:
            - `newProfile`: JSON STRING // JSON-строка с новыми данными профиля. Должна содержать одно или несколько
              полей
              профиля для обновления.
        - **Пример запроса**:
          ```json
          {
            "data": {
              "newProfile": "{\"firstName\": \"Анна\", \"lastName\": \"Иванова\", \"email\": \"anna@example.com\"}"
            }
          }
          ```
        - **Структура ответа**:
          ```json
          {
            "status": "STRING", // "success" или "error"
            "data": "STRING" // Описание результата, например, "profile updated" или сообщение об ошибке
          }
          ```
        - **Особенности**:
            - Поддерживает частичное обновление: только указанные поля в `newProfile` будут обновлены.
            - Проигнорирует несуществующие поля, обеспечивая безопасность и целостность данных профиля.
            - Требует авторизации пользователя для обновления данных профиля, обеспечивая защиту данных от
              несанкционированного доступа.

        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```json
              {
                "status": "error",
                "data": "not logged in"
              }
              ```
            - **invalid profile data**: Предоставленные данные профиля некорректны или отсутствуют.
              ```json
              {
                "status": "error",
                "data": "invalid profile data"
              }
              ```
            - **profile not found**: Профиль пользователя не найден, возможно, из-за неправильного идентификатора
              пользователя.
              ```json
              {
                "status": "error",
                "data": "profile not found"
              }
              ```

### Actor: `coursesManager`

- Назначение: обслуживание курсов/модулей;
- Доступные `actions`:
    - ### getAvailableCourses ###
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

    - ### getCourseInfo ###
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

    - ### getUserCourses ###
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

    - ### getUserCourseModules ###
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

    - ### getUserCourseModuleArticlesTree ###
        - **Описание**: Получает дерево статей модуля конкретного курса для пользователя. Возвращаемое дерево содержит
          группы тем и статьи, включая информацию о выполнении каждой статьи или группы.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
            - `moduleId`: INT // Идентификатор модуля.

        - **Структура ответа**:
            ```
            [
              {
                "status": STRING, // "success" или "error"
                "data": [
                  {
                    "id": INT, // Идентификатор группы тем или статьи
                    "name": STRING, // Название группы тем или статьи
                    "type": STRING, // Тип элемента: "group" для групп тем, "article" для статей
                    "completed": BOOLEAN, // Статус завершения: true или false
                    "content": ARRAY // Для групп тем содержит массив статей или подгрупп
                  },
                  ...
                ]
              }
            ]
            ```
            - Внутри `content` для групп тем повторяется структура верхнего уровня.

        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
                "status": "error",
                "data": "not logged in"
              }
              ```
            - **unknown course**: Идентификатор курса отсутствует или курс с таким идентификатором не найден.
              ```
              {
                "status": "error",
                "data": "unknown course"
              }
              ```
            - **unknown module**: Идентификатор модуля отсутствует или модуль с таким идентификатором не найден.
              ```
              {
                "status": "error",
                "data": "unknown module"
              }
              ```
            - Дополнительно, если файл дерева статей модуля не найден, возвращается ошибка "unknown module", как в
              случае
              отсутствия идентификатора модуля.

    - ### getUserCourseModuleArticle ###
        - **Описание**: Получает содержимое статьи в виде Plain HTML для конкретного модуля и курса, идентифицируемого
          по
          пути статьи. Данная функция предназначена для извлечения и отображения статьи, используя динамически
          сформированный путь к мок-файлу с содержимым статьи.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
            - `moduleId`: INT // Идентификатор модуля.
            - `articlePath`: STRING // Путь к статье, используемый для генерации имени файла, состоящий из
              последовательности чисел, разделенных запятой.

        - **Структура ответа**:
            - В случае успеха, возвращает содержимое статьи в формате Plain HTML.
            ```
            {
              "status": "success",
              "data": "<html>...</html>" // Содержимое статьи в формате HTML
            }
            ```
            - Содержимое возвращается непосредственно из мок-файла, указанного динамически сформированным путем.

        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
                "status": "error",
                "data": "not logged in"
              }
              ```
            - **unknown course**: Идентификатор курса отсутствует или курс с таким идентификатором не найден.
              ```
              {
                "status": "error",
                "data": "unknown course"
              }
              ```
            - **unknown module**: Идентификатор модуля отсутствует или модуль с таким идентификатором не найден.
              ```
              {
                "status": "error",
                "data": "unknown module"
              }
              ```
            - **unknown article**: Путь к статье отсутствует, неверен или статья с таким путем не найдена.
              ```
              {
                "status": "error",
                "data": "unknown article"
              }
              ```

    - ### markMaterialAsCompleted ###
        - **Описание**: Отмечает материал (статью или группу статей) как завершенный или незавершенный в зависимости от
          переданного статуса.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
            - `moduleId`: INT // Идентификатор модуля.
            - `articlePath`: STRING // Путь к статье в формате строки, где каждый уровень вложенности разделен запятой.
            - `status`: BOOLEAN // Статус завершенности материала (true или false).

        - **Структура ответа**:
            ```
            {
              "status": STRING, // "success" или "error"
              "data": "article state updated"
            }
            ```

        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
                "status": "error",
                "data": "not logged in"
              }
              ```
            - **unknown course**: Идентификатор курса отсутствует или курс с таким идентификатором не найден.
              ```
              {
                "status": "error",
                "data": "unknown course"
              }
              ```
            - **unknown module**: Идентификатор модуля отсутствует или модуль с таким идентификатором не найден.
              ```
              {
                "status": "error",
                "data": "unknown module"
              }
              ```
            - **unknown article**: Путь к статье отсутствует, неверен или статья с таким путем не найдена в дереве
              материалов.
              ```
              {
                "status": "error",
                "data": "unknown article"
              }
              ```
            - **unknown status**: Статус завершенности материала не указан или не опознан.
              ```
              {
                "status": "error",
                "data": "unknown status"
              }
              ```

    - ### getUserCourseModuleHomework** ###
        - **Описание**: Получает домашнее задание модуля конкретного курса для пользователя, включая информацию о
          задании, отправленных решениях, оценках и комментариях.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
            - `moduleId`: INT // Идентификатор модуля.
        - **Структура ответа**:
          ```
          {
            "status": STRING, // "success" или "error"
            "data": {
              "task": STRING, // HTML с текстом задания и подсказками
              "submissions": [
                {
                  "date": STRING, // Дата и время отправки задания
                  "fileName": STRING, // Имя файла с решением
                  "hash": STRING, // Хеш файла для скачивания
                  "status": STRING, // Статус проверки задания ("Accepted", "Pending", "Rejected")
                },
                ...
              ],
              "status": STRING, // Статус выполнения задания ("Done","In progress")
              "score": INT, // Оценка за задание (0-100)
              "comments": [
                {
                  "sender": INT, // ID отправителя комментария, 0 для системных сообщений
                  "dateTime": STRING, // Дата и время комментария
                  "message": STRING, // Текст комментария
                  "unread" => BOOLEAN, //статус прочитаности сообщения
                },
                ...
              ]
            }
          }
          ```
        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
              "status": "error",
              "data": "not logged in"
              }
              ```
            - **unknown course**: `courseId` отсутствует или курс с таким `courseId` не найден.
              ```
              {
              "status": "error",
              "data": "unknown course"
              }
              ```
            - **unknown module**: `moduleId` отсутствует или модуль с таким `moduleId` не найден.
              ```
              {
              "status": "error",
              "data": "unknown module"
              }
              ```

    - ### addHomeworkComment ###
        - **Описание**: Добавляет комментарий к домашнему заданию в модуле определенного курса и возвращает объект
          Comment, отражающий состояние комментария в системе.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
            - `moduleId`: INT // Идентификатор модуля.
            - `message`: STRING // Текст комментария.

        - **Структура ответа**:
          ```
          {
            "status": STRING, // "success" или "error"
            "data":{
              "sender": INT, // ID отправителя комментария
              "dateTime": STRING, // Дата и время комментария
              "message": STRING, // Текст комментария
              "unread" => BOOLEAN, //статус прочитаности сообщения
            }         
          }
          ```
        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
              "status": "error",
              "data": "not logged in"
              }
              ```
            - **unknown course**: `courseId` отсутствует или курс с таким `courseId` не найден.
              ```
              {
              "status": "error",
              "data": "unknown course"
              }
              ```
            - **unknown module**: `moduleId` отсутствует или модуль с таким `moduleId` не найден.
              ```
              {
              "status": "error",
              "data": "unknown module"
              }
              ```
            - **empty message**: Текст комментария не предоставлен.
              ```
              {
              "status": "error",
              "data": "empty message"
              }
              ```

    - ### addHomeworkSubmission ###
        - **Описание**: Добавляет отправку домашнего задания студентом для модуля определенного курса. Загружает файл
          решения и возвращает объект Submission, описывающий состояние файла в системе.
        - **Параметры**:
            - `courseId`: INT // Идентификатор курса.
            - `moduleId`: INT // Идентификатор модуля.
            - `file`: FILE // Файл с решением домашнего задания, отправляемый через форму.
        - **Структура ответа**:
          ```
          {
            "status": STRING, // "success" или "error"
            "data": {
              "date": STRING, // Дата и время отправки задания
              "fileName": STRING, // Имя файла с решением
              "hash": STRING, // Хеш файла для скачивания
              "status": STRING, // Статус проверки задания ("Accepted", "Pending", "Rejected")
            }
          }
          ```
        - **Ошибки**:
            - **not logged in**: Пользователь не авторизован.
              ```
              {
              "status": "error",
              "data": "not logged in"
              }
              ```
            - **unknown course**: Если `courseId` отсутствует или курс с таким `courseId` не найден.
              ```
              {
              "status": "error",
              "data": "unknown course"
              }
              ```
            - **unknown module**: Если `moduleId` отсутствует или модуль с таким `moduleId` не найден.
              ```
              {
              "status": "error",
              "data": "unknown module"
              }
              ```
            - **no file specified**: Если файл для отправки не указан или не загружен.
              ```
              {
              "status": "error",
              "data": "no file specified"
              }
              ```

        - ### downloadHomeworkFile ### 
            - **Описание**: Позволяет скачать файл домашнего задания по уникальному хешу файла. Если запрос успешен,
              файл
              будет отправлен в ответе, инициируя загрузку на стороне клиента.
            - **Параметры**:
                - `courseId`: INT // Идентификатор курса.
                - `moduleId`: INT // Идентификатор модуля.
                - `fileHash`: STRING // Уникальный хеш файла, который требуется скачать.

            - **Структура ответа**:
                - В случае успеха: Файл будет отправлен как поток данных, инициируя его загрузку у пользователя. В
                  заголовках ответа будет указано имя файла и тип
                  содержимого (`Content-Disposition: attachment; filename="{fileName}"`,
                  `Content-Type: application/octet-stream`).
                - В случае ошибки: Возвращается стандартный JSON-ответ с `status: "error"` и `data: "описание ошибки"`.

                    - **Ошибки**:
                        - **not logged in**: Пользователь не авторизован.
                          ```
                          {
                          "status": "error",
                          "data": "not logged in"
                          }
                          ```
                        - **unknown course**: Если `courseId` отсутствует или курс с таким `courseId` не найден.
                          ```
                          {
                          "status": "error",
                          "data": "unknown course"
                          }
                          ```
                        - **unknown module**: Если `moduleId` отсутствует или модуль с таким `moduleId` не найден.
                          ```
                          {
                          "status": "error",
                          "data": "unknown module"
                          }
                          ```
                        - **no file specified**: Если не указан хеш файла.
                          ```
                          {
                          "status": "error",
                          "data": "no file specified"
                          }
                          ```
                        - **file not found**: Если файл по данному хешу не найден.
                          ```
                          {
                          "status": "error",
                          "data": "file not found"
                          }
                          ```

        - ### downloadHomeworkFile ###
            - **Описание**: Позволяет скачать файл домашнего задания пользователя. Проверяет, авторизован ли
              пользователь,
              существует ли указанный курс, модуль и файл, и отдает файл на скачивание, если все проверки пройдены
              успешно.
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
                - `fileHash`: STRING, хеш файла для скачивания.
            - **Структура ответа**: Неприменимо (файл отправляется на скачивание).
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Курс не найден.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Модуль не найден.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **no file specified**: Файл не указан.
                    ```
                    {
                    "status": "error",
                    "data": "no file specified"
                    }
                    ```
                - **file not found**: Файл не найден.
                    ```
                    {
                    "status": "error",
                    "data": "file not found"
                    }
                    ```

        - ### getUserCourseModuleTest ###
            - **Описание**: Получает метаинформацию о тесте модуля курса для пользователя. Этот метод проверяет,
              авторизован
              ли пользователь и существуют ли указанный курс и модуль. Возвращает метаинформацию о тесте, если все
              проверки
              пройдены успешно.
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" или "error"
                  "data": {
                    "questionsCount": INT, // Количество вопросов в тесте
                    "currentTry": INT, // Номер текущей попытки прохождения теста
                    "state": STRING, // Статус теста (например, "idle" или "in_progress")
                    "lastQuestion": INT, // Номер последнего заданного вопроса
                    "triesLimit": INT, // Лимит попыток прохождения теста
                    "mistakesLimit": INT, // Лимит допустимых ошибок
                    "lastAttemptTime": INT // Время последней попытки прохождения теста
                  }
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Курс не найден.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Модуль не найден.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **test not found**: Тест не найден.
                    ```
                    {
                    "status": "error",
                    "data": "test not found"
                    }
                    ```

        - ### launchUserCourseModuleTest ###
            - **Описание**: Запускает тест модуля курса для пользователя. Проверяет авторизацию пользователя и наличие
              указанного курса и модуля. Инициализирует тест, если не был достигнут лимит попыток или если предыдущая
              попытка прохождения теста была сделана более 30 дней назад. Возвращает вопросы теста с обнуленными
              вариантами
              ответов для пользователя.
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
            - **Структура ответа**:
                ```
                   {
                    "status": STRING, // "success" в случае успеха, "error" в случае ошибки
                    "data": [
                        {
                            "title": STRING, // Заголовок вопроса
                            "type": STRING, // Тип вопроса ("single" для одного возможного правильного ответа, "many" для нескольких)
                            "options": [
                                {
                                    "option": STRING, // Текст варианта ответа
                                    "selected": BOOLEAN // Всегда false, указывает на неотмеченный ответ (для связывания с v-model)
                                },
                            ]
                        },
                    ]
                  }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Неизвестный курс.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Неизвестный модуль.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **test not found**: Тест не найден.
                    ```
                    {
                    "status": "error",
                    "data": "test not found"
                    }
                    ```
                - **limit reached**: Достигнут лимит попыток.
                    ```
                    {
                    "status": "error",
                    "data": "limit reached"
                    }
                    ```

        - ### updateUserCourseModuleTest ###
            - **Описание**: Обновляет ответы пользователя на вопрос теста модуля курса. Проверяет авторизацию
              пользователя,
              существование курса, модуля и вопроса теста. Затем обновляет ответы на указанный вопрос, если тест
              находится в
              состоянии "в процессе" (`in_progress`).
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
                - `questionId`: INT, идентификатор вопроса.
                - `answers`: JSON, объект с ответами на вопрос, где ключи — это варианты ответа, а значения указывают на
                  выбор пользователя (true/false).
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" при успешном обновлении, "error" в случае ошибки
                  "data": STRING // Описание результата или ошибки, например "question #1 updated" или "test not started"
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Неизвестный курс.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Неизвестный модуль.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **unknown question**: Неизвестный вопрос.
                    ```
                    {
                    "status": "error",
                    "data": "unknown question"
                    }
                    ```
                - **test not found**: Тест не найден.
                    ```
                    {
                    "status": "error",
                    "data": "test not found"
                    }
                    ```
                - **test not started**: Тест не начат.
                    ```
                    {
                    "status": "error",
                    "data": "test not started"
                    }
                    ```
                - **unknown option**: Неизвестный вариант ответа.
                    ```
                    {
                    "status": "error",
                    "data": "unknown option: [option]"
                    }
                    ```
                - **unknown question#**: Неизвестный идентификатор вопроса.
                    ```
                    {
                    "status": "error",
                    "data": "unknown question#: [questionId]"
                    }
                    ```

        - ### finishUserCourseModuleTest ###
            - **Описание**: Завершает тест модуля курса для пользователя. Проверяет, авторизован ли пользователь и
              существуют ли указанный курс и модуль. Если все условия удовлетворены и тест находится в процессе
              выполнения,
              метод завершает тест, обновляя его статус и фиксируя время последней попытки.
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" при успешном завершении, "error" в случае ошибки
                  "data": STRING // Описание результата или ошибки, например, "test finished" или "test not started"
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Неизвестный курс.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Неизвестный модуль.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **test not found**: Тест не найден.
                    ```
                    {
                    "status": "error",
                    "data": "test not found"
                    }
                    ```
                - **test not started**: Тест не начат.
                    ```
                    {
                    "status": "error",
                    "data": "test not started"
                    }
                    ```

        - ### reviewUserCourseModuleTest ###
            - **Описание**: Проводит оценку завершенного теста модуля курса для пользователя. Проверяет, авторизован ли
              пользователь и существуют ли указанный курс и модуль. Если все условия удовлетворены и тест находится в
              состоянии "завершен" (`idle`), метод оценивает тест, вычисляя итоговый балл, количество ошибок и статус
              прохождения теста.
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" при успешной оценке, "error" в случае ошибки
                  "data": {
                    "score": INT, // Итоговый балл за тест
                    "passed": BOOLEAN, // Индикатор прохождения теста: true - успешно, false - не успешно
                    "mistakes": INT, // Количество ошибок
                    "structure": ARRAY // Массив с результатами по каждому вопросу: true - вопрос решен правильно, false - неправильно
                  }
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Неизвестный курс.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Неизвестный модуль.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **test not found**: Тест не найден.
                    ```
                    {
                    "status": "error",
                    "data": "test not found"
                    }
                    ```
                - **test in progress**: Тест еще не завершен.
                    ```
                    {
                    "status": "error",
                    "data": "test in progress"
                    }
                    ```

        - ### getUnreadMessages ###
            - **Описание**: Получает список непрочитанных сообщений для пользователя, включая уведомления о сроках
              выполнения заданий и непрочитанные комментарии. Проверяет авторизацию пользователя, затем итерирует по
              курсам
              и модулям, на которые пользователь подписан, и генерирует сообщения на основе заданных критериев.
            - **Параметры**: Отсутствуют. Вся информация берется из сессии пользователя и сохраненных данных.
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" при успешном получении данных, "error" в случае ошибки
                  "data": [
                      {
                          "course": INT, // Идентификатор курса
                          "module": INT, // Идентификатор модуля
                          "type": STRING, // Тип сообщения ("deadline" или "comment")
                          "content": MIXED, // Количество дней до дедлайна для "deadline" или текст комментария для "comment"
                          "hash": STRING // Хеш сообщения для идентификации прочитанных сообщений
                      },
                      ...
                  ]
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **can't fetch user subscriptions**: Не удается получить информацию о подписках пользователя на курсы
                  или
                  модули.
                    ```
                    {
                    "status": "error",
                    "data": "can't fetch user subscriptions"
                    }
                    ```

        - ### markMessageAsRead ###
            - **Описание**: Помечает сообщение как прочитанное для пользователя. Метод проверяет авторизацию
              пользователя и
              наличие хеша сообщения. Если проверки пройдены успешно, хеш сообщения добавляется в список прочитанных
              сообщений пользователя, обновляя его статус.
            - **Параметры**:
                - `messageHash`: STRING, хеш сообщения, которое необходимо пометить как прочитанное.
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" при успешном обновлении статуса сообщения, "error" в случае ошибки
                  "data": STRING // Описание результата операции, например, "message status updated" или "unknown message"
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown message**: Неизвестный хеш сообщения.
                    ```
                    {
                    "status": "error",
                    "data": "unknown message"
                    }
                    ```

        - ### markCommentAsRead ###
            - **Описание**: Помечает комментарий в домашнем задании как прочитанный для пользователя. Проверяет
              авторизацию
              пользователя и наличие идентификаторов курса, модуля и комментария. Если все условия удовлетворены,
              комментарий помечается как прочитанный, обновляя его статус в хранилище.
            - **Параметры**:
                - `courseId`: INT, идентификатор курса.
                - `moduleId`: INT, идентификатор модуля.
                - `commentId`: INT, идентификатор комментария.
            - **Структура ответа**:
                ```
                {
                  "status": STRING, // "success" при успешном обновлении статуса комментария, "error" в случае ошибки
                  "data": STRING // Описание результата операции, например, "comment status updated" или "unknown comment"
                }
                ```
            - **Ошибки**:
                - **not logged in**: Пользователь не авторизован.
                    ```
                    {
                    "status": "error",
                    "data": "not logged in"
                    }
                    ```
                - **unknown course**: Неизвестный курс.
                    ```
                    {
                    "status": "error",
                    "data": "unknown course"
                    }
                    ```
                - **unknown module**: Неизвестный модуль.
                    ```
                    {
                    "status": "error",
                    "data": "unknown module"
                    }
                    ```
                - **unknown comment**: Неизвестный комментарий.
                    ```
                    {
                    "status": "error",
                    "data": "unknown comment"
                    }
                    ```