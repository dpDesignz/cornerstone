<?php

// $csdb = new CornerstoneDBH;

// $csdb->dbh->debugOn();

// $userID = 1;

// $result = $csdb->dbh->selecting(DB_PREFIX . "login_log", "login_id", where(groupWhere(eq("login_user_type", "1", _AND), eq("login_user_id", $userID), _AND), eq("login_status", "3", _AND), ));

// echo $csdb->dbh->getNum_Rows();

// $csdb->dbh->debug();

// $csdb->dbh->varDump($result);

$subject = "New order created";
$text = "A new order has been placed";

$array = array(
  'subject' => $subject,
  'text' => $text
);

$res = json_encode($array);
echo "Convert from array to json : " . $res . "<br>";

echo "<br>From json to array:<br>";
echo '<pre>';
print_r(json_decode($res));
echo '</pre>';

exit;
