<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getAvailableCourses":
        {
            $result["status"] = "success";
            $result["data"] = include dirname(__DIR__) . "/mockFiles/courses/course_list.php";
            break;
        }

        case "getCourseInfo":
        {
            $mockFilePath = dirname(__DIR__) . "/mockFiles/courses/details/info_mock_" . ($_POST["data"]["courseId"] ?? "") . ".php";
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

        case "getUserCourseModuleArticlesTree":
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

            error_log(var_export($_COOKIE, true));
            error_log("***********************************");
            error_log(var_export($moduleTree, true));

            $treeWalker($moduleTree);
            error_log("$---------------------------------$");

            error_log(var_export($moduleTree, true));
            error_log("***********************************");

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

            $status = ($status === "true");
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

        case "getUserCourseModuleHomework":
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

            $mockFilePath = dirname(__DIR__) . "/mockFiles/homework/trees/homework_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            $homework = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                $registeredUsers = read_from_cache("registeredUsers", function () {
                    return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                }, 3600);

                $registeredUsersIds = array_column($registeredUsers, "userName", "userId");
                $homeworkData = include($mockFilePath);
                foreach ($homeworkData["comments"] as &$comment) {
                    $comment["sender"] = $registeredUsersIds[$comment["sender"]] ?? "System";
                }

                foreach ($homeworkData["submissions"] as $submission) {
                    $HWFilePath = dirname(__DIR__) . "/mockFiles/homework/files/" . $submission["fileName"];
                    $fileContent = file_get_contents($HWFilePath);

                    $file = [
                        "name" => $submission["fileName"],
                        "body" => $fileContent
                    ];

                    write_to_cache($mockFilePath . $submission["hash"], $file, 600);
                }

                return $homeworkData;
            }, 600);

            $result["status"] = "success";
            $result["data"] = $homework;
            break;
        }
//
        case "addHomeworkComment":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $message = $data["message"] ?? null;

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


            if (empty($message)) {
                $result["status"] = "error";
                $result["data"] = "empty message";
                break;
            }

            $mockFilePath = dirname(__DIR__) . "/mockFiles/homework/trees/homework_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            $homework = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                $registeredUsers = read_from_cache("registeredUsers", function () {
                    return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                }, 3600);

                $registeredUsersIds = array_column($registeredUsers, "userName", "userId");
                $homeworkData = include($mockFilePath);
                foreach ($homeworkData["comments"] as &$comment) {
                    $comment["sender"] = $registeredUsersIds[$comment["sender"]] ?? "System";
                }

                foreach ($homeworkData["submissions"] as $submission) {
                    $HWFilePath = dirname(__DIR__) . "/mockFiles/homework/files/" . $submission["fileName"];
                    $fileContent = file_get_contents($HWFilePath);

                    $file = [
                        "name" => $submission["fileName"],
                        "body" => $fileContent
                    ];

                    write_to_cache($mockFilePath . $submission["hash"], $file, 600);
                }

                return $homeworkData;
            }, 600);

            $comment = [
                "sender" => $_SESSION["userId"], //userId в системе
                "dateTime" => gmdate("Y-m-d\TH:i:s\Z"),
                "message" => $message,
                "unread" => false,
            ];
            $homework["comments"][] = $comment;

            write_to_cache($mockFilePath, $homework, 600);

            $result["status"] = "success";
            $result["data"] = $comment;
            break;
        }

        case "addHomeworkSubmission":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $filePath = $_FILES['data']['tmp_name']['file'] ?? null;

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


            if (is_null($filePath)) {
                $result["status"] = "error";
                $result["data"] = "no file specified";
                break;
            }

            $mockFilePath = dirname(__DIR__) . "/mockFiles/homework/trees/homework_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            $homework = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                $registeredUsers = read_from_cache("registeredUsers", function () {
                    return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                }, 3600);

                $registeredUsersIds = array_column($registeredUsers, "userName", "userId");
                $homeworkData = include($mockFilePath);
                foreach ($homeworkData["comments"] as &$comment) {
                    $comment["sender"] = $registeredUsersIds[$comment["sender"]] ?? "System";
                }

                foreach ($homeworkData["submissions"] as $submission) {
                    $HWFilePath = dirname(__DIR__) . "/mockFiles/homework/files/" . $submission["fileName"];
                    $fileContent = file_get_contents($HWFilePath);

                    $file = [
                        "name" => $submission["fileName"],
                        "body" => $fileContent
                    ];

                    write_to_cache($mockFilePath . $submission["hash"], $file, 600);
                }

                return $homeworkData;
            }, 600);

            $uploadedFileTempName = $_FILES['data']['tmp_name']['file'];
            $uploadedFileName = $_FILES['data']['name']['file'];
            $fileContent = file_get_contents($uploadedFileTempName);
            $hash = md5(
                $uploadedFileName .
                gmdate("Y-m-d\TH:i:s\Z") .
                $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId
            );

            $submission = [
                "date" => "2024-10-11T10:12:33",
                "fileName" => $uploadedFileName,
                "hash" => $hash,
                "status" => "Pending"
            ];

            $homework["submissions"][] = $submission;

            $file = [
                "name" => $uploadedFileName,
                "body" => $fileContent
            ];

            write_to_cache($mockFilePath . $hash, $file, 600);
            write_to_cache($mockFilePath, $homework, 600);

            $result["status"] = "success";
            $result["data"] = $submission;
            break;
        }

        case "downloadHomeworkFile":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $fileHash = $data["fileHash"] ?? null;

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


            if (is_null($fileHash)) {
                $result["status"] = "error";
                $result["data"] = "no file specified";
                break;
            }

            $mockFilePath = dirname(__DIR__) . "/mockFiles/homework/trees/homework_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "unknown module";
                break;
            }

            $homework = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                $registeredUsers = read_from_cache("registeredUsers", function () {
                    return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                }, 3600);

                $registeredUsersIds = array_column($registeredUsers, "userName", "userId");
                $homeworkData = include($mockFilePath);
                foreach ($homeworkData["comments"] as &$comment) {
                    $comment["sender"] = $registeredUsersIds[$comment["sender"]] ?? "System";
                }

                foreach ($homeworkData["submissions"] as $submission) {
                    $HWFilePath = dirname(__DIR__) . "/mockFiles/homework/files/" . $submission["fileName"];
                    $fileContent = file_get_contents($HWFilePath);

                    $file = [
                        "name" => $submission["fileName"],
                        "body" => $fileContent
                    ];

                    write_to_cache($mockFilePath . $submission["hash"], $file, 600);
                }

                return $homeworkData;
            }, 600);

            $file = read_from_cache($mockFilePath . $fileHash);

            if (!$file) {
                $result["status"] = "error";
                $result["data"] = "file not found";
                break;
            }

            header('Access-Control-Expose-Headers: Content-Disposition');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file["name"]);
            error_log("Sending file:" . $file["name"]);
            exit($file["body"]);
        }

        case "getUserCourseModuleTest":
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

            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = include($mockFilePath);

            array_walk($test["questions"], function (&$item) {
                $item["options"] = array_fill_keys(array_keys($item["options"]), false);
            });

            $result["status"] = "success";
            $result["data"] = $test;
            break;
        }

        case "reviewUserCourseModuleTest":
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

            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = include($mockFilePath);

            array_walk($test["questions"], function (&$item) {
                $item["options"] = array_keys($item["options"]);
            });

            $result["status"] = "success";
            $result["data"] = $test;
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