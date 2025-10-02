<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getAvailableCourses":
        {
            $result["status"] = "success";
            $result["data"] = [
                ["title" => "Kotlin", "description" => "Разработка для устройств Android"],
                ["title" => "Java", "description" => "Универсальный и практичный язык"],
                ["title" => "Swift", "description" => "Разработка для устройств Apple"],
                ["title" => "JavaScript", "description" => "Фронтенд для веб-приложений"],
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