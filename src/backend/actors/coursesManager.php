<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getAvailableCourses":
        {
            $result["status"] = "success";
            $result["data"] = [
                 ["id" => "0", "title" => "Kotlin", "description" => "Разработка для устройств Android", "icon" => ""],
                 ["id" => "1", "title" => "Java", "description" => "Универсальный и практичный язык", "icon" => ""],
                 ["id" => "2", "title" => "Swift", "description" => "Разработка для устройств Apple", "icon" => ""],
                 ["id" => "3", "title" => "JavaScript", "description" => "Фронтенд для веб-приложений", "icon" => ""],
            ];
            break;
        }

        default:
        {
            $result["status"] = "error";
            $result["data"] = "unknown action";
        }
    }
} else {
    $result["status"] = "error";
    $result["data"] = "unknown action";
}

return $result;