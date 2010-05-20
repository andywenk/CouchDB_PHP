<?php
/**
 * CouchDB_PHP example
 * 
 * Here you can finde some examples for using the class. Remember, that you have to
 * change the _id and _rev when trying these examples
 * 
 * Andy Wenk <andy@nms.de>
 */
include_once('../src/CouchDB_PHP.php');

$cphp = new CouchDB_PHP('hanswurst');

/*
// create a document
$data = array("name" => "andy", "alter" => 14);
$res = $cphp->create_doc($data);
*/

/*
// update a document 
$cphp->set_id('38dd7a1c6b047125c69cd7af730292a7');
$data = array("_rev" => "2-2b657242b821689922c052ab344c35cc", "name" => "anna", "alter" => 60);
$res = $cphp->update_doc($data);
*/

/*
// get a document by id
$cphp->set_response_type('json');
$cphp->set_id('38dd7a1c6b047125c69cd7af730292a7');
$res = $cphp->get_doc();
*/

/*
// get all documents
$res = $cphp->get_all_docs();
*/

/*
// delete document
$cphp->set_id('38dd7a1c6b047125c69cd7af730263c8');
$data = array("_rev" => "3-9e2bc7ca3f147d1bd2757887f2ed7ddc");
$res = $cphp->delete_doc($data);
*/

echo "\n\n";
print_r($res);
echo "\n\n";
?>