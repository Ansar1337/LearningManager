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
            $mockFilePath = __DIR__ . "/mockFiles/courses/info_mock_" . ($_POST["data"]["courseId"] ?? "") . ".php";
            error_log($mockFilePath);
            if ((!isset($_POST["data"])) ||
                (!isset($_POST["data"]["courseId"])) ||
                (!(file_exists($mockFilePath)))
            ) {
                $result["status"] = "error";
                $result["data"] = "unknown courseId";
                break;
            }

            $result["status"] = "success";
            $result["data"] = include($mockFilePath);
            break;
        }

        case "getUserCourses":
        {

            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $mockFilePath = __DIR__ . "/mockFiles/userCourses/user_" . $_SESSION["userId"] . ".php";

            $result["status"] = "success";
            if (!(file_exists($mockFilePath))) {
                $result["data"] = [];
            } else {
                $result["data"] = include($mockFilePath);
            }

            break;
        }

        case "getUserCourseModules":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $mockFilePath = __DIR__ . "/mockFiles/modules/module_mock_" . $_SESSION["userId"] . "_" . ($_POST["data"]["courseId"] ?? "") . ".php";

            if ((!isset($_POST["data"])) ||
                (!isset($_POST["data"]["courseId"])) ||
                (!(file_exists($mockFilePath)))
            ) {
                $result["status"] = "error";
                $result["data"] = "unknown courseId";
                break;
            }

            $result["status"] = "success";
            $result["data"] = include($mockFilePath);
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