<?php

$users = [
    ["name" => "ansar",
        "role" => "student",
    ],
    ["name" => "denis",
        "role" => "teacher",
    ],
];

$userList = [];

foreach ($users as $user) {
    $userList[] = [
        "userId" => crc32($user["name"]),
        "userName" => ucfirst($user["name"]),
        "role" => $user["role"],
        "loggedIn" => false,
    ];
}

return $userList;