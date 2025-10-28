<?php

return [
    "task" => "HTML с текстом задания и хинтами и тд",
    "submissions" => [
        [
            "date" => "2024-10-11T10:12:33Z",
            "fileName" => "Homework.zip",
            "hash" => md5("Homework.zip" . "2024-10-11T10:12:33Z" . "768849199_0_0"), //name+date+fullModuleId //54e4025f10c35998e7eadbfcee94eb72
            "status" => "Accepted", // "Pending" || "Rejected"
        ]
    ],
    "status" => "Done", // "In progress"
    "score" => 100,
    "comments" => [
        [
            "id" => 0,
            "sender" => 0,// 0 id - это системные сообщения
            "dateTime" => "2024-10-11T12:24:32Z",
            "message" => "Файл Homework.zip загружен, ожидается ревью.",
            "unread" => false
        ],
        [
            "id" => 1,
            "sender" => 2568108003, //userId в системе
            "dateTime" => "2024-10-12T12:24:32Z",
            "message" => "Добрый день! Всё отлично. Попробуйте добавить возможность ввода нового числа.",
            "unread" => false
        ],
        [
            "id" => 2,
            "sender" => 768849199,
            "dateTime" => "2024-10-12T12:26:45Z",
            "message" => "Хорошо, спасибо!",
            "unread" => false
        ],
        [
            "id" => 3,
            "sender" => 2568108003,
            "dateTime" => "2024-10-12T12:27:12Z",
            "message" => "Если что, дз по факту готово, так что я поменял его статус, но если сделаете то, что я попросил, это будет хороший бонус к опыту.",
            "unread" => true // в таком случае необходимо сделать пометку во входящих сообщениях пользователя
        ],
    ]
];