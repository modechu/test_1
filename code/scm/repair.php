<?php

$dbh = mysql_connect('192.168.1.23', 'mode', '6699');

// $resource = mysql_query('SHOW DATABASES', $dbh);
// echo 'Found ' . mysql_num_rows($resource) . " databases.\n";

// while ($db_t = mysql_fetch_assoc($resource)) {
    // $db_name = $db_t['Database'];
    $db_name = 'scm_smpl';
    echo "Database : $db_name <br>\n";

    /*
    if ($db_name == 'information_schema')
        continue;
    */

    mysql_select_db($db_name, $dbh);

    $r = mysql_query('SHOW TABLE STATUS', $dbh);
    while ($t = mysql_fetch_assoc($r)) {
        // echo $t['Name'] . ' Collation: ' . $t['Collation'] . "<br>\n"; // 查此 Table 的編碼
        echo $t['Name'] . ' Collation: ' . $t['Comment'] . "<br>\n"; // 查此 Table 的編碼
// print_r($t);
        if ($t['Data_free'] > 0) {
            echo $t['Name'] . " optimization.<br>\n";
            mysql_query('OPTIMIZE TABLE ' . $t['Name'], $dbh) or die(mysql_error());
        }
// exit;
        mysql_query('REPAIR TABLE ' . $t['Name'], $dbh) or die('Optimize failed - ' . mysql_error());
    }

    echo "\n";
// }
?>