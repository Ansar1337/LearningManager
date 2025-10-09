<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getAvailableCourses":
        {
            $result["status"] = "success";
            $result["data"] = [
                ["id" => "0", "title" => "Kotlin", "description" => "Разработка для устройств Android", "icon" => "kotlin-logo.png"],
                ["id" => "1", "title" => "Java", "description" => "Универсальный и практичный язык", "icon" => "java-logo.png"],
                ["id" => "2", "title" => "Swift", "description" => "Разработка для устройств Apple", "icon" => "swift-logo.png"],
                ["id" => "3", "title" => "JavaScript", "description" => "Фронтенд для веб-приложений", "icon" => "js-logo.png"],
            ];
            break;
        }

        case "getCourseInfo":
        {

            $courseFilePath = __DIR__ . "/compiledTemplates/courses/" . ($_POST["data"]["courseId"] ?? "") . ".php";

            if ((!isset($_POST["data"])) ||
                (!isset($_POST["data"]["courseId"])) ||
                (!(file_exists($courseFilePath)))
            ) {
                $result["status"] = "error";
                $result["data"] = "unknown courseId";
                break;
            }

            $result["status"] = "success";
            $result["data"] = include($courseFilePath);
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