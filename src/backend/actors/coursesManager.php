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
            $mockFilePath = dirname(__DIR__) . "/mockFiles/courses/info_mock_" . ($_POST["data"]["courseId"] ?? "") . ".php";
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

            $mockFilePath = dirname(__DIR__) . "/mockFiles/userCourses/user_" . $_SESSION["userId"] . ".php";

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

            $mockFilePath = dirname(__DIR__) . "/mockFiles/modules/module_mock_" . $_SESSION["userId"] . "_" . ($_POST["data"]["courseId"] ?? "") . ".php";

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

        case "getUserCourseModuleArticleTree":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;

            if (is_null($courseId)) {
                $result["status"] = "error";
                $result["data"] = "unknown course";
                break;
            }

            if (is_null($moduleId)) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            $mockFilePath = dirname(__DIR__) . "/mockFiles/materials/trees/article_tree_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            $moduleTree = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 600);


            $treeWalker = function (&$node) use (&$treeWalker) {
                $result = true;
                foreach ($node as &$item) {
                    if ($item["type"] === "group") {
                        $item["completed"] = $treeWalker($item["content"]);
                    }
                    $result = $result && $item["completed"];
                }
                return $result;
            };

            $treeWalker($moduleTree);
            $result["status"] = "success";
            $result["data"] = $moduleTree;

            break;
        }

        case "getUserCourseModuleArticle":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $articlePath = $data["articlePath"] ?? null;

            if (is_null($courseId)) {
                $result["status"] = "error";
                $result["data"] = "unknown course";
                break;
            }

            if (is_null($moduleId)) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            if (is_null($articlePath)) {
                $result["status"] = "error";
                $result["data"] = "unknown article";
                break;
            }

            $articlePath = implode('_',
                array_merge([$courseId, $moduleId], explode(",", $articlePath))
            );

            $mockFilePath = dirname(__DIR__) . "/mockFiles/materials/articles/article_mock_" . $articlePath . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown article";
                break;
            }

            $result["status"] = "success";
            $result["data"] = include($mockFilePath);
            break;
        }

        case "markMaterialAsCompleted":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $articlePath = $data["articlePath"] ?? null;
            $status = $data["status"] ?? null;

            if (is_null($courseId)) {
                $result["status"] = "error";
                $result["data"] = "unknown course";
                break;
            }

            if (is_null($moduleId)) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            if (is_null($articlePath)) {
                $result["status"] = "error";
                $result["data"] = "unknown article";
                break;
            }

            if (is_null($status)) {
                $result["status"] = "error";
                $result["data"] = "unknown status";
                break;
            }

            $mockFilePath = dirname(__DIR__) . "/mockFiles/materials/trees/article_tree_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown article";
                break;
            }

            $moduleTree = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 600);

            $articlePath = explode(",", $articlePath);
            $node = &$moduleTree;
            for ($i = 0; $i < sizeof($articlePath); $i++) {
                $node = &$node[intval($articlePath[$i])];
                if ($node["type"] === "group") {
                    $node = &$node["content"];
                }
            }

            $node["completed"] = $status;

            write_to_cache($mockFilePath, $moduleTree, 600);
            $result["status"] = "success";
            $result["data"] = "article state updated";
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