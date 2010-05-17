<?php
/**
 * CouchDB_PHP example
 *
 * Andy Wenk <andy@nms.de>
 */
include_once('../src/CouchDB_PHP.php');

$cphp = new CouchDB_PHP();
$res = $cphp->create_db('hanswurst');
echo "\n\n";
print_r($res);
echo "\n\n";
?>