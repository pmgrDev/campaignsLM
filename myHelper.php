<?php

/**
 * * function connectToDatabase
 * ! Opens new mysqli connection to database
 * @param host string
 * @param user string 
 * @param password string
 * @param database string
 * @return mysqli_object
 */
function connectToDatabase($connectionType = null, $connectionOptions = null, $database = null)
{
    if (empty($database) || (empty($connectionType) && empty($connectionOptions))) die("Error in function " . __FUNCTION__ . "! => Empty mandatory parameters...");

    if (!empty($connectionType)) {
        switch ($connectionType) {
            case 'vetmanager':
                $host = 'db.cirjsmu1bwtg.eu-central-1.rds.amazonaws.com';
                $user = 'admin';
                $password = 'vieh8thaiwaeghohxo5ooD2AhRohreim';
                break;
            case 'vetbizzmanager':
                $host = 'vetbizz-manager.com';
                $user = 'vetbizzm_web';
                $password = 'IqbiUXD;$(G!';
                break;
            case 'dbsMatrix':
                $host = 'vetbizz-manager.com';
                $user = 'gfsvetbi_vbm';
                $password = 'BXdY9)5ggweN';
                break;
            case 'dev':
                $host = 'db-dev.cirjsmu1bwtg.eu-central-1.rds.amazonaws.com';
                $user = 'admin';
                $password = 'vieh8thaiwaeghohxo5ooD2AhRohreim';
                break;
            case 'local':
                $host = '127.0.0.1';
                $user = 'root';
                $password = 'localdbpass';
                break;
            default:
                //local
                $host = '127.0.0.1';
                $user = 'root';
                $password = 'localdbpass';
                break;
        }
    }

    if (!empty($connectionOptions)) {
        $host = $connectionOptions->host;
        $user = $connectionOptions->user;
        $password = $connectionOptions->password;
    }

    $connection = new mysqli($host, $user, $password, $database);
    if ($connection->connect_errno) die("Connection failed! \n" . $connection->connect_error);
    $connection->set_charset('utf8mb4');

    return $connection;
}

/**
 * * function isEmpty
 * ! Checks if the parameter is empty or not
 * @param items array
 * @return true/false
 */
function isEmpty($items)
{
    foreach ($items as $item) if (is_string($item) && empty(trim($item))) return true;
    return false;
}

/**
 * * function executeQuery
 * ! Executes the given query with the given mysqli connection
 * @param connection mysqli object
 * @param query string
 * @return mysqli_result object
 */
function executeQuery($connection = null, $query = null)
{
    mysqli_report(MYSQLI_REPORT_OFF); //Turn off mysqli_sql_expection

    if (empty($query)) {
        echo "Error in function " . __FUNCTION__ . "! => Empty required parameter...";
        $connection->close();
        die;
    }

    $result = $connection->query($query);
    if (!$result) {
        echo "\nError executing the following query: " . $query . "\nError: " . $connection->error;
        $connection->close();
        die;
    }

    return $result;
}

function csvToArray($directory = null, $separator = ',')
{
    if (empty($directory)) die("Error opening file. Directory is empty!");

    $rows = array_map(fn ($v) => str_getcsv($v, $separator), file($directory));
    $header = array_shift($rows);
    $content = array();
    foreach ($rows as $row) {
        $row = str_replace(["\n", "\r"], "", $row);
        if (count($header) === count($row)) $content[] = array_combine($header, $row);
    }
    return $content;
}

function allowedConnectionTypes()
{
    return array('vetmanager', 'vetbizzmanager', 'dbsMatrix', 'local');
}
