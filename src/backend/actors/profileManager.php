<?php

$result = $result ?? [];

if ($_POST["action"] ?? false) {
    switch ($_POST["action"]) {
        case "getSession":
        {
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