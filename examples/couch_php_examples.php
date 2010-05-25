<?php
/**
 * CouchDB_PHP example
 * 
 * Here you can finde some examples for using the class. Remember, that you have to
 * change the _id and _rev when trying these examples
 *
 * You can simply call this with php cli: php -f couch_php_examples.php . Remember
 * to uncomment an example to become a little more excited ;-)
 * 
 * Andy Wenk <andy@nms.de> 
 */
include_once('../src/CouchDB_PHP.php');
$res = '';

$couch = new CouchDB_PHP();

/*
// show all db's
$res = $couch->show_all_dbs();
*/

/*
// create a database 
$res = $couch->create_db('php_mag_5_10');
*/

/*
// delete database
$res = $couch->delete_db('php_mag_5_10');
*/

$couch = new CouchDB_PHP('php_mag_5_10');

/*
// create a document
//$couch->set_id('prod_6');
$data = array("name" => "andy", "alter" => 14);
$res = $couch->create_doc($data);
echo $couch->get_last_id();
*/


// update a document 
$couch->set_id('a733607681750a8aa9f14e5f25000c56');
$data = array("_rev" => "2-43c04835d6a4d4ce2635a8e19bfaacbd", "Ausgabe" => "5.2010");
$res = $couch->update_doc($data);


/*
// get a document by id
$couch->set_response_type('json');
$couch->set_id('prod_1');
$res = $couch->get_doc();
*/

/*
// get all documents
$res = $couch->get_all_docs();
*/

/*
// delete document
$couch->set_id('38dd7a1c6b047125c69cd7af730263c8');
$data = array("_rev" => "3-9e2bc7ca3f147d1bd2757887f2ed7ddc");
$res = $couch->delete_doc($data);
*/

echo "\n\n";
print_r($res);
echo "\n\n";
?>