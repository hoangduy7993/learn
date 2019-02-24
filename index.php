<?php

// $data = [
// 	(object)[
// 		'name'	=> 'Duy',
// 		'age'	=> 25,
// 		'dob'	=> '12/12/1993'
// 	],
// 	(object)[
// 		'name'	=> 'Tâm',
// 		'age'	=> 26,
// 		'dob'	=> '15/12/1993'
// 	],
// ];

// foreach ($data as $value):
// 	print_r($value->name . ' ' . $value->age);
// 	echo '<br>';
// endforeach;

// exit;

// $test=1;
// $data = [
// 	'ID'   => $taisans->ID,
// 	'Name' => $taisans->Name
// ];
use Phalcon\Mvc\Model\MetaData\Memory;

use Phalcon\Mvc\Model\Manager as ModelsManager;

use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;
//use QLtaisanDB\Relia;
//use Phalcon\Mvc\Model;
//use Phalcon\QLtaisanDB\Relia;
// Use Loader() to autoload our model
$loader = new Loader();
$loader->registerNamespaces(
	[
		'QLtaisanDB\Relia' => __DIR__ . '/models/',
	]
);

$loader->register();

$di = new FactoryDefault();
$di->set(
    "modelsManager",
    function() {
        return new ModelsManager();
    }
);
// Set up the database service
$di->set(
	'db',
	function () {
		return new PdoMysql(
			[
				'host'     => 'localhost',
				'dbname'   => 'qltaisandb',
				'username' => 'root',
				'password' => ''
			]
		);
	}
);

// Create and bind the DI to the application
$app = new Micro($di);


//$app = new Micro();

$app->get('/QLtaisan/taisans',
	function () use ($app) {

		$phql = 'SELECT * FROM QLtaisanDB\Relia\taisans ORDER BY Name';
		$taisans = $app->modelsManager->executeQuery($phql);
		
		foreach ($taisans as $taisan) {
			$data[] = [
				'ID'   => $taisan->ID, 
				'Name' => $taisan->Name
			];
		}
		
		echo json_encode($data);
}
);

// tiem kiem san pham theo ten
$taisans = new \QLtaisanDB\Relia\taisans();
$app->get('/QLtaisan/taisans/tiemkiem/{Name1}',
	function ($Name1) use ($taisans) {
		// $phql = 'SELECT * FROM QLtaisanDB\Relia\taisans WHERE Name LIKE :Name1: ORDER BY Name';
 
		// $taisans = $app->modelsManager->executeQuery(
		// 	$phql,
		// 		[
		// 			'Name1' => $Name
		// 		]
		// );//nhap id de tiem kiem
		// foreach ($taisans as $taisan) {
		// 	$data = [
		// 		'ID'   => $taisan->ID,
		// 		'Name' => $taisan->Name,
		// 	];
		// }

		echo json_encode($taisans::find("Name = '$Name1'"));
}
);
$app->get('/QLtaisan/taisans/{id:[0-9]+}',
	function ($id) use($taisans){
		$ts=$taisans::find("ID='$id'");
		echo json_encode($ts);
	}

);
$app->post('/QLtaisan/taisans',
	function () use ($app,$taisans) {
		$Post_taisan = (object)$app->request->GetPost();
		// $phql = 'INSERT INTO QLtaisanDB\Relia\taisans (ID,Name) VALUES (Postid, Postname)';
		// $status = $app->modelsManager->executeQuery(
		// 	$phql, 
		// 	[
		// 		'Postid' => $taisan->ID,
		// 		'Postname' => $taisan->Name,
		// 	]
		// );
		
		$taisans->ID = $Post_taisan->ID;
		$taisans->Name = $Post_taisan->Name;
		if($taisans->save() == true):
			 // Change the HTTP status
			 $app->response->setStatusCode(201, 'Created');

			 $Post_taisan->ID = $taisans->ID;
			 $app->response->setJsonContent(
				 [
					 'status' => 'OK',
					 'data'   => $Post_taisan,
				 ]
			 );		 
		else:
			//Change the HTTP status
            $app->response->setStatusCode(409, 'Conflict');

            // Send errors to the client
            $errors = [];

            foreach ($taisans->getMessages() as $message) {
                $errors[] = $message->getMessage();
           	}

			$app->response->setJsonContent(
                [  'status'   => 'ERROR',
					'messages' => $errors,
				]
            );
		
		endif;
        $app->response->send();
	}
);
// // lay tai san theo ID
// $app->get('/QLtaisan/taisans/{ID:[0-9]+}',
// 	function($ID) use ($app){
// 		$phql='SELECT * FROM QLtaisanDB\Relia\taisans WHERE ID = :ID:';
// 		$taisans = $app->modelsManager->executeQuery($phql, [
// 			'ID'=> $ID,
// 		])->getFirst();
// 		if($taisans === false):
// 			$app->response->setJsonContent([
// 				'status' => 'NOT-FOUND'
// 			]);
// 		else:
// 			$app->response->setJsonContent([
// 				'status' => 'FOUND',
// 				'data' => [
// 					'ID'   => $taisans->ID,
// 					'Name' => $taisans->Name
// 				]
// 			]);
// 		endif;

// 		$app->response->send();
// 	}

// );

// //tao moi tai san
// $app->post('/QLtaisan/taisan',
// 	function () use($app) {
// 		$taisans = $app->request->GetJsonRawBody();
// 		$phql = 'INSERT INTO QLtaisanDB\Relia\taisans'(ID,Name) VALUES (:ID:,:Name:);
// 		echo "cai dmm sao ko chay";
// 	}
// );

//cap nhat taisan theo ID
$app->delete('/QLtaisan/taisans/{id:[0-9]+}',
	function ($id) use ($app){
		$taisan = new \QLtaisanDB\Relia\taisans;
		$taisan = $taisan::findfirst($id);
		
		if($taisan !== false):
			if($taisan->delete() == false):
				// Change the HTTP status
				$errors = [];
	
				foreach ($taisan->getMessages() as $message) {
					$errors[] = $message->getMessage();
				}
	
				$app->response->setJsonContent(
					[
						'status'   => 'ERROR',
						'messages' => $errors,
					]
				);
			
			else:
				$app->response->setJsonContent(
					['status' => 'OK']
					);
			endif;
		else:
			$app->response->setJsonContent(
				[
					'status' => 'ERROR',
					'messages' => 'ID khong ton tai'
				]
			);
		endif;
		$app->response->send();
	}

);

// //xoa tai asn theo ID
// $app->put('/QLtaisan/taisan/{id:[0-9]+}',
// 	function(){
// 		echo "cai dmm sao ko chay id";
// 	}

// );
$app->handle($_SERVER['REQUEST_URI']);