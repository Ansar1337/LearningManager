<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getSession":
        {
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
                $registeredLogins = read_from_cache("registeredLogins", function () {
                    return ['ansar', 'denis'];
                }, 3600);

                if (in_array($login, $registeredLogins)) {
                    $_SESSION["userId"] = crc32($login);
                    $_SESSION["userName"] = ucfirst($login);
                    $_SESSION["role"] = (($login === "denis") ? ("teacher") : ("student"));
                    $_SESSION["loggedIn"] = true;

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
                $registeredLogins = read_from_cache("registeredLogins", function () {
                    return ['ansar', 'denis'];
                }, 3600);

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

            $registeredLogins = read_from_cache("registeredLogins", function () {
                return ['ansar', 'denis'];
            }, 3600);

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
                $registeredLogins[] = $login;
                write_to_cache("reservedLogins", array_unique($reservedLogins), 60);
                write_to_cache("registeredLogins", array_unique($registeredLogins), 3600);
                $result["status"] = "success";
                $result["data"] = "login registered";
            } else {
                $result["status"] = "error";
                $result["data"] = "login not reserved";
            }
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