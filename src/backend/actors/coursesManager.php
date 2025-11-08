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
            }, 3600);


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
            }, 3600);

            $articlePath = explode(",", $articlePath);
            $node = &$moduleTree;
            for ($i = 0; $i < sizeof($articlePath); $i++) {
                $node = &$node[intval($articlePath[$i])];
                if ($node["type"] === "group") {
                    $node = &$node["content"];
                }
            }

            $node["completed"] = $status;

            write_to_cache($mockFilePath, $moduleTree, 3600);
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

                    write_to_cache($mockFilePath . $submission["hash"], $file, 3600);
                }

                return $homeworkData;
            }, 3600);

            $result["status"] = "success";
            $result["data"] = $homework;
            break;
        }

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
            $messageList = $data["message"] ?? null;

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


            if (empty($messageList)) {
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

                    write_to_cache($mockFilePath . $submission["hash"], $file, 3600);
                }

                return $homeworkData;
            }, 3600);

            $comment = [
                "sender" => $_SESSION["userId"], //userId в системе
                "dateTime" => gmdate("Y-m-d\TH:i:s\Z"),
                "message" => $messageList,
                "unread" => false,
            ];
            $homework["comments"][] = $comment;

            write_to_cache($mockFilePath, $homework, 3600);

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

                    write_to_cache($mockFilePath . $submission["hash"], $file, 3600);
                }

                return $homeworkData;
            }, 3600);

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

            write_to_cache($mockFilePath . $hash, $file, 3600);
            write_to_cache($mockFilePath, $homework, 3600);

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

                    write_to_cache($mockFilePath . $submission["hash"], $file, 3600);
                }

                return $homeworkData;
            }, 3600);

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

//            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";
            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . 0 . "_" . 0 . ".php";
            $mockFilePathCacheKey = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = read_from_cache($mockFilePathCacheKey, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 3600);


            $result["status"] = "success";
            $result["data"] = $test["meta"];
            break;
        }

        case "launchUserCourseModuleTest":
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

//            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";
            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . 0 . "_" . 0 . ".php";
            $mockFilePathCacheKey = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = read_from_cache($mockFilePathCacheKey, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 3600);

            $dayLimit = 30;
            $reset = ($test["meta"]["lastAttemptTime"] <= (time() - ($dayLimit * 24 * 60 * 60)));
            if (($test["meta"]["currentTry"] > $test["meta"]["triesLimit"]) && (!$reset)) {
                $result["status"] = "error";
                $result["data"] = "limit reached";
                break;
            }

            if (($test["meta"]["currentTry"] > $test["meta"]["triesLimit"]) && ($reset)) {
                $test["meta"]["currentTry"] = 0;
                $test["meta"]["lastAttemptTime"] = time();
            }

            $test["meta"]["state"] = "in_progress";

            write_to_cache($mockFilePathCacheKey, $test, 3600);

            $result["status"] = "success";
            $result["data"] = "test launched";
            break;
        }

        case "getUserCourseModuleTestQuestions":
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

//            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";
            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . 0 . "_" . 0 . ".php";
            $mockFilePathCacheKey = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = read_from_cache($mockFilePathCacheKey, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 3600);

            $testState = read_from_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], function () use ($test) {
                $result = $test["questions"];
                array_walk($result, function (&$item) {
                    $item["options"] = array_fill_keys(array_keys($item["options"]), false);
                });
                return $result;
            }, 3600);

            if ($test["meta"]["state"] !== "in_progress") {
                $result["status"] = "error";
                $result["data"] = "test not started";
                break;
            }

            write_to_cache($mockFilePathCacheKey, $test, 3600);
            write_to_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], $testState, 3600);

            $result["status"] = "success";
            $result["data"] = $testState;
            break;
        }

        case "updateUserCourseModuleTest":
        {

            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $questionId = $data["questionId"] ?? null;
            $answers = $data["answers"] ?? null;

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

            if (is_null($questionId)) {
                $result["status"] = "error";
                $result["data"] = "unknown question";
                break;
            }

//            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";
            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . 0 . "_" . 0 . ".php";
            $mockFilePathCacheKey = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = read_from_cache($mockFilePathCacheKey, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 3600);

            $testState = read_from_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], function () use ($test) {
                $result = $test["questions"];
                array_walk($result, function (&$item) {
                    $item["options"] = array_fill_keys(array_keys($item["options"]), false);
                });
                return $result;
            }, 3600);

            if ($test["meta"]["state"] !== "in_progress") {
                $result["status"] = "error";
                $result["data"] = "test not started";
                break;
            }

            $answers = json_decode($answers, true);
            if (isset($testState[$questionId])) {
                foreach ($testState[$questionId]["options"] as $option => $_) {
                    if (isset($answers[$option])) {
                        $testState[$questionId]["options"][$option] = $answers[$option];
                    } else {
                        $result["status"] = "error";
                        $result["data"] = "unknown option: $option";
                        break;
                    }
                }
            } else {
                $result["status"] = "error";
                $result["data"] = "unknown question#: $questionId";
                break;
            }

            $test["meta"]["lastQuestion"] = intval($questionId);

            write_to_cache($mockFilePathCacheKey, $test, 3600);
            write_to_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], $testState, 3600);

            $result["status"] = "success";
            $result["data"] = "question #$questionId updated";
            break;
        }

        case "finishUserCourseModuleTest":
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

//            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";
            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . 0 . "_" . 0 . ".php";
            $mockFilePathCacheKey = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = read_from_cache($mockFilePathCacheKey, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 3600);

            $testState = read_from_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], function () use ($test) {
                $result = $test["questions"];
                array_walk($result, function (&$item) {
                    $item["options"] = array_fill_keys(array_keys($item["options"]), false);
                });
                return $result;
            }, 3600);

            if ($test["meta"]["state"] !== "in_progress") {
                $result["status"] = "error";
                $result["data"] = "test not started";
                break;
            }

            $test["meta"]["currentTry"]++;
            $test["meta"]["lastAttemptTime"] = time();
            $test["meta"]["state"] = "idle";

            write_to_cache($mockFilePathCacheKey, $test, 3600);
            write_to_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], $testState, 3600);

            $result["status"] = "success";
            $result["data"] = "test finished";
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

            //            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";
            $mockFilePath = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . 0 . "_" . 0 . ".php";
            $mockFilePathCacheKey = dirname(__DIR__) . "/mockFiles/tests/test_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "test not found";
                break;
            }

            $test = read_from_cache($mockFilePathCacheKey, function () use ($mockFilePath) {
                return include($mockFilePath);
            }, 3600);

            $testState = read_from_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], function () use ($test) {
                $result = $test["questions"];
                array_walk($result, function (&$item) {
                    $item["options"] = array_fill_keys(array_keys($item["options"]), false);
                });
                return $result;
            }, 3600);

            if ($test["meta"]["state"] !== "idle") {
                $result["status"] = "error";
                $result["data"] = "test in progress";
                break;
            }

//            if ($test["currentTry"] === 1) {
//                $result["status"] = "error";
//                $result["data"] = "test in progress";
//                break;
//            }

            $review = [
                "score" => 100,
                "passed" => true,
                "mistakes" => 0,
                "structure" => []
            ];

            foreach (array_column($test["questions"], "options") as $questionId => $options) {
                $questionResult = true;
                foreach ($options as $question => $answer) {
                    $sample = $testState[$questionId]["options"];
                    if ($sample[$question] !== $answer) {
                        $questionResult = false;
                        break;
                    }
                }
                $review["structure"][] = $questionResult;

                if (!$questionResult) {
                    $review["mistakes"]++;
                    $review["score"] -= 100 / $test["meta"]["questionsCount"];
                }
            }

            $review["score"] = max(0, min(100, intval($review["score"])));
            $review["passed"] = $review["mistakes"] <= $test["meta"]["mistakesLimit"];

            write_to_cache($mockFilePathCacheKey, $test, 3600);
            write_to_cache($mockFilePathCacheKey . "state" . $test["meta"]["currentTry"], $testState, 3600);

            $result["status"] = "success";
            $result["data"] = $review;
            break;
        }

        case "getUnreadMessages":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $coursesMockFilePath = dirname(__DIR__) . "/mockFiles/userCourses/user_" . $_SESSION["userId"] . ".php";
            $userCourses = [];

            if (!(file_exists($coursesMockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "can't fetch user subscriptions";
            } else {
                $userCourses = include($coursesMockFilePath);
            }

            $messageList = [];
            foreach ($userCourses as $courseId => $userCourse) {
                $modulesMockFilePath = dirname(__DIR__) . "/mockFiles/modules/module_mock_" . $_SESSION["userId"] . "_" . $userCourse["id"] . ".php";
                $userCourseModules = [];
                if (!(file_exists($modulesMockFilePath))) {
                    $result["status"] = "error";
                    $result["data"] = "can't fetch user subscriptions";
                } else {
                    $userCourseModules = include($modulesMockFilePath);
                }

                foreach ($userCourseModules as $moduleId => $userCourseModule) {
                    //Проверка сроков
                    $targetDate = $userCourseModule["deadline"];
                    $today = date("Y-m-d");
                    $targetDateTime = new DateTime($targetDate);
                    $todayDateTime = new DateTime($today);
                    $interval = $todayDateTime->diff($targetDateTime);
                    $daysUntilTarget = (int)$interval->format("%r%a");
                    if ($daysUntilTarget < 10) {
                        $messageList[] = [
                            "course" => $courseId,
                            "module" => $moduleId,
                            "type" => "deadline",
                            "content" => $daysUntilTarget
                        ];
                    }

                    //проверка комментариев
                    $HomeworkFilePath = dirname(__DIR__) . "/mockFiles/homework/trees/homework_mock_" . $_SESSION["userId"] . "_" . $courseId . "_" . $moduleId . ".php";

                    if (!(file_exists($HomeworkFilePath))) {
                        $homework = [];
                        break;
                    }

                    $homework = read_from_cache($HomeworkFilePath, function () use ($HomeworkFilePath) {
                        $registeredUsers = read_from_cache("registeredUsers", function () {
                            return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                        }, 3600);

                        $registeredUsersIds = array_column($registeredUsers, "userName", "userId");
                        $homeworkData = include($HomeworkFilePath);
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

                            write_to_cache($HomeworkFilePath . $submission["hash"], $file, 3600);
                        }

                        return $homeworkData;
                    }, 3600);

                    foreach ($homework["comments"] as $comment) {
                        if ($comment["unread"]) {
                            $messageList[] = [
                                "course" => $courseId,
                                "module" => $moduleId,
                                "type" => "comment",
                                "content" => $comment["message"]
                            ];
                        }
                    }
                }
            }

            $messagesReadStatus = read_from_cache($_SESSION["userId"] . "messagesTracker", function () {
                return [];
            });

            foreach ($messageList as $key => $message) {
                $hash = md5(json_encode($message));
                if (in_array($hash, $messagesReadStatus)) {
                    unset($messageList[$key]);
                } else {
                    $messageList[$key]["hash"] = $hash;
                }
            }

            $result["status"] = "success";
            $result["data"] = array_values($messageList);
            break;
        }

        case "markMessageAsRead":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $messageHash = $data["messageHash"] ?? null;

            if (is_null($messageHash)) {
                $result["status"] = "error";
                $result["data"] = "unknown message";
                break;
            }

            $messagesReadStatus = read_from_cache($_SESSION["userId"] . "messagesTracker", function () {
                return [];
            });

            $messagesReadStatus[] = $messageHash;

            write_to_cache($_SESSION["userId"] . "messagesTracker", $messagesReadStatus, 3600);

            $result["status"] = "success";
            $result["data"] = "message status updated";
            break;
        }

        case "markCommentAsRead":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $courseId = $data["courseId"] ?? null;
            $moduleId = $data["moduleId"] ?? null;
            $commentId = $data["commentId"] ?? null;

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

            if (is_null($commentId)) {
                $result["status"] = "error";
                $result["data"] = "unknown comment";
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

            $homework["comments"][$commentId]["unread"] = false;

            write_to_cache($mockFilePath, $homework, 3600);

            $result["status"] = "success";
            $result["data"] = "comment status updated";
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