<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getSession":
        {
            error_log(var_export($_SESSION, true));

            $result["status"] = "success";
            $result["data"] = [
                "userId" => $_SESSION["userId"],
                "loggedIn" => $_SESSION["loggedIn"],
                "userName" => $_SESSION["userName"],
                "role" => $_SESSION["role"],
            ];
            break;
        }

        case "tryToLogOut":
        {
            $_SESSION = [];
            $result["status"] = "success";
            $result["data"] = "session flushed";
            break;
        }

        case "tryToLogIn":
        {
            $login = strtolower($_POST['data']['login'] ?? '');

            if ($_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "already logged in";
                break;
            }

            if (($login !== '') && ($_POST["data"]["password"] ?? false)) {
                $registeredUsers = read_from_cache("registeredUsers", function () {
                    return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                }, 3600);

                $registeredLogins = array_map(function ($item) {
                    return strtolower($item);
                }, array_column($registeredUsers, "userName"));

                if (($userIndex = array_search($login, $registeredLogins)) !== false) {
                    $registeredUsers[$userIndex]["loggedIn"] = true;
                    $_SESSION = array_merge($_SESSION, $registeredUsers[$userIndex]);
                    write_to_cache("registeredUsers", $registeredUsers, 3600);

                    error_log(var_export($_SESSION, true));

                    $result["status"] = "success";
                    $result["data"] = "access granted";
                } else {
                    $result["status"] = "error";
                    $result["data"] = "unknown user";
                }

            } else {
                $result["status"] = "error";
                $result["data"] = "corrupted data";
            }
            break;
        }

        case "reserveLogin":
        {
            $login = strtolower($_POST['data']['login'] ?? '');
            $_SESSION['reservedLogins'] = $_SESSION['reservedLogins'] ?? [];

            if ($_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "already logged in";
                break;
            }

            if ($login !== '') {
                $registeredUsers = read_from_cache("registeredUsers", function () {
                    return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
                }, 3600);

                $registeredLogins = array_map(function ($item) {
                    return strtolower($item);
                }, array_column($registeredUsers, "userName"));

                $reservedLogins = read_from_cache("reservedLogins", function () {
                    return [];
                }, 60);

                if (
                    (!in_array($login, array_merge($registeredLogins, $reservedLogins))) ||
                    ((in_array($login, $reservedLogins)) && (in_array($login, $_SESSION['reservedLogins'])))
                ) {
                    $_SESSION['reservedLogins'][] = $login;
                    $reservedLogins[] = $login;
                    write_to_cache("reservedLogins", array_unique($reservedLogins), 60);
                    $result["status"] = "success";
                    $result["data"] = "login reserved";
                } else {
                    $result["status"] = "error";
                    $result["data"] = "login occupied";
                }
            } else {
                $result["status"] = "error";
                $result["data"] = "empty login";
            }
            break;
        }

        case "registerLogin":
        {
            $login = strtolower($_POST['data']['login'] ?? '');
            $password = $_POST['data']['login'] ?? false;
            $_SESSION['reservedLogins'] = $_SESSION['reservedLogins'] ?? [];

            if ($_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "already logged in";
                break;
            }

            if (!$password) {
                $result["status"] = "error";
                $result["data"] = "empty password";
                break;
            }

            if ($login === '') {
                $result["status"] = "error";
                $result["data"] = "empty login";
                break;
            }

            $registeredUsers = read_from_cache("registeredUsers", function () {
                return include dirname(__DIR__) . "/mockFiles/users/users_list.php";
            }, 3600);

            $registeredLogins = array_map(function ($item) {
                return strtolower($item);
            }, array_column($registeredUsers, "userName"));

            $reservedLogins = read_from_cache("reservedLogins", function () {
                return [];
            }, 60);

            if (
                (!in_array($login, $registeredLogins)) &&
                ((in_array($login, $reservedLogins)) && (in_array($login, $_SESSION['reservedLogins'])))
            ) {
                $_SESSION['reservedLogins'] = [];
                if (($key = array_search($login, $reservedLogins)) !== false) {
                    unset($reservedLogins[$key]);
                }

                $registeredUsers[] = [
                    "userId" => crc32($login),
                    "userName" => ucfirst($login),
                    "role" => "student",
                    "loggedIn" => false,
                ];

                error_log(var_export($registeredUsers, true));

                write_to_cache("reservedLogins", array_unique($reservedLogins), 60);
                write_to_cache("registeredUsers", $registeredUsers, 3600);

                $result["status"] = "success";
                $result["data"] = "login registered";
            } else {
                $result["status"] = "error";
                $result["data"] = "login not reserved";
            }
            break;
        }

        case "loadProfileData":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $mockFilePath = dirname(__DIR__) . "/mockFiles/profiles/user_" . $_SESSION["userId"] . "_profile.php";

            $profile = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                return include $mockFilePath;
            }, 3600);

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "profile not found";
                break;
            }

            $result["status"] = "success";
            $result["data"] = $profile;

            break;
        }

        case "saveProfileData":
        {
            if (!$_SESSION["loggedIn"]) {
                $result["status"] = "error";
                $result["data"] = "not logged in";
                break;
            }

            $data = $_POST["data"] ?? [];
            $newProfile = $data["newProfile"] ?? null;

            if (is_null($newProfile)) {
                $result["status"] = "error";
                $result["data"] = "invalid profile data";
                break;
            }

            $newProfile = json_decode($newProfile, true);
            error_log(var_export($newProfile, true));

            $mockFilePath = dirname(__DIR__) . "/mockFiles/profiles/user_" . $_SESSION["userId"] . "_profile.php";

            $profile = read_from_cache($mockFilePath, function () use ($mockFilePath) {
                return include $mockFilePath;
            }, 3600);

            $updateProfileTree = function (&$profileTreeNode, &$newProfileNode) use (&$updateProfileTree) {
                foreach ($profileTreeNode as $key => &$node) {
                    if (isset($newProfileNode[$key])) {
                        if (is_array($node)) {
                            $updateProfileTree($node, $newProfileNode[$key]);
                        } else {
                            $profileTreeNode[$key] = $newProfileNode[$key];
                        }
                    }
                }
            };

            $updateProfileTree($profile, $newProfile);

            write_to_cache($mockFilePath, $profile, 3600);

            if (!(file_exists($mockFilePath))) {
                $result["status"] = "error";
                $result["data"] = "profile not found";
                break;
            }

            $result["status"] = "success";
            $result["data"] = "profile updated";

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