<?php

    if (!isset($_GET['name']) || !isset($_GET['privacy']) || !isset($_GET['status']) || !isset($_GET['fullness'])) {
        echo "error";
        exit();
    }
    $privacy = intval($_GET['privacy']);
    $status = intval($_GET['status']);
    $fullness = intval($_GET['fullness']);

    $name = "%".$_GET['name']."%";
    $logicalOperator = "AND";
    switch ($privacy) {
        case 1:
            $privacy = "(privacy != 3) $logicalOperator ";
        break;
        case 2:
            $privacy = "(privacy = 1) $logicalOperator ";
        break;
        case 3:
            $privacy = "(privacy = 2) $logicalOperator ";
        break;
        default:
            echo "error";
            exit();
        break;
    }
    switch ($status) {
        case 1:
            $status = "(status != 6) $logicalOperator ";
        break;
        case 2:
            $status = "(status = 1) $logicalOperator ";
        break;
        case 3:
            $status = "(status = 2) $logicalOperator ";
        break;
        case 4:
            $status = "(status = 3) $logicalOperator ";
        break;        
        default:
            echo "error";
            exit();
        break;
    }
    switch ($fullness) {
        case 1:
            $fullness = "(players != 3)";
        break;
        case 2:
            $fullness = "(players = 2)";
        break;
        case 3:
            $fullness = "(players < 2)";
        break;
        case 4:
            $fullness = "(players = 1)";
        break;
        case 5:
            $fullness = "(players = 0)";
        break;  
        default:
            echo "error";
            exit();
        break;
    }
    @include_once "../../base.php";
    $connection = @new mysqli($db_host, $db_user, $db_password, $db_name);
    $sql = "SELECT name, privacy, status, players FROM reversi WHERE name LIKE ? $logicalOperator ".$privacy.$status.$fullness;
    $stmt = $connection -> prepare($sql);
    $stmt -> bind_param("s", $name);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($nameR, $privacyR, $statusR, $playersR);
    while ($stmt -> fetch()) {
        echo $nameR.";;".$privacyR.";;".$statusR.";;".$playersR.";;;";
    }
    $stmt -> close();
    mysqli_close($connection);
?>