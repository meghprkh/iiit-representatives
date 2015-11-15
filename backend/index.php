<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
$app->response->headers->set('Access-Control-Allow-Origin', '*');

require 'config.php';

ORM::configure("mysql:host=$dbhost;dbname=$dbname");
ORM::configure('username', $dbuser);
ORM::configure('password', $dbpass);
ORM::configure('logging',true);
ORM::configure('return_result_sets',true);

ORM::configure('id_column_overrides', array(
    'poschild' => 'child_id'
));

$dbconn=mysql_connect($dbhost,$dbuser,$dbpass);
if(! $dbconn)
{
	die('Could not connect' .mysql_error());
}
mysql_selectdb($dbname);

$app->get("/position/:id(/)",function($id) use ($app,$dbconn) {
	$result = ORM::for_table('positions')
                ->join('people', 'positions.held_by = people.id')
                ->where('id', $id)
                ->find_array();
  echo json_encode($result[0]);
});

$app->get("/children/:id(/)",function($id) use ($app,$dbconn) {
	$result = ORM::for_table('poschild')
                ->join('positions', 'child_id = positions.id')
                ->join('people', 'positions.held_by = people.id')
                ->where('parent_id', $id)
                ->find_array();
  echo json_encode($result);
});

$app->run();
?>
