<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
$app->response->headers->set('Access-Control-Allow-Origin', '*');
//$client = new GuzzleHttp\Client(['base_uri' => 'https://ac.khoslalabs.com/hackgate/hackathon/']);

require 'config.php';

ORM::configure("mysql:host=$dbhost;dbname=$dbname");
ORM::configure('username', $dbuser);
ORM::configure('password', $dbpass);
ORM::configure('logging',true);
ORM::configure('return_result_sets',true);

ORM::configure('id_column_overrides', array(
    'records' => 'rid',
    'doctors' => 'did',
    'allergies'=>'aid',
    'users'=>'uid',
    'vaccines'=>'vid',
    'vitals'=>'uid',
    'pharmacists' => 'phid'
));

//$dbconn=mysql_connect($OPENSHIFT_MYSQL_DB_HOST,$dbuser,$dbpass,"",$OPENSHIFT_MYSQL_DB_PORT);
$dbconn=mysql_connect($dbhost,$dbuser,$dbpass);
if(! $dbconn)
{
	die('Could not connect' .mysql_error());
}
mysql_selectdb($dbname);

function query($sql) {
	GLOBAL $dbconn;
	$result=mysql_query($sql,$dbconn);
	if(! $result)
	{
		die('Could not get data' .mysql_error());
	}
    $rarray = array();
    while($row =mysql_fetch_assoc($result)) {
        $rarray[] = $row;
    }
	echo json_encode($rarray);
}

function insert_query($sql){
	GLOBAL $dbconn;
	$result=mysql_query($sql,$dbconn);
	if(! $result)
	{
		die('Could not insert data' .mysql_error());
	}
}

function return_json($sql){
	GLOBAL $dbconn;
	$rarray = array();
	$pid= mysql_query($sql,$dbconn);
	if(! $pid)
	{
		die('Could not return json' .mysql_error());
	}
	while($row =mysql_fetch_assoc($pid)) {
        $rarray[] = $row;
    }
	echo json_encode($rarray[0]);
}

function update_query($sql) {
	GLOBAL $dbconn;
	$result=mysql_query($sql,$dbconn);
	if(mysql_affected_rows($dbconn) >= 0)
	{
		echo "{\"success\":\"true\"}";
	}
	elseif (mysql_affected_rows($dbconn) == -1) {
		echo "{\"failure\":\"false\"}";
	}
    else{
    	echo("unknown error");
    }
}

$app->get("/getuser/:uid(/)",function($uid) use ($app,$dbconn) {
	query("SELECT `users`.* FROM `users` WHERE (`users`.`uid` =$uid)");
});

$app->get('/getpid(/)',function() use ($app) {
  $result=ORM::for_table('users')
              ->where('email',$app->request->params('email'))
              ->find_one();
  echo json_encode([
      "pid" => $result->uid
    ]);
});

$app->group("/doctor/:uid",function() use($app,$dbconn) {
	$app->group("/vaccine",function() use($app,$dbconn) {
		$app->get("/:vid(/)",function($uid,$vid) use($app,$dbconn){
			//echo "Hi".$uid."vid".$vid."getvid";
			//if(jwt_verify($app->request->params('token')))
			query("SELECT `vaccines`.* FROM `vaccines` WHERE ((`vaccines`.`vid` =$vid) AND (`vaccines`.`uid` =$uid))");
		});
		$app->get("/",function($uid) use($app,$dbconn){
			//echo "Hi".$uid."get";
			query("SELECT `vaccines`.* FROM `vaccines` WHERE (`vaccines`.`uid` =$uid)");
		});
		$app->post("/",function($uid) use($app,$dbconn){
			//echo "Hi".$uid."post";
			$vaccine=$app->request->params('vaccine');
			$place=$app->request->params('place');
			insert_query("INSERT INTO `vaccines`(`uid`, `vaccine`, `date`, `place`)
				VALUES ($uid,\"$vaccine\",CURDATE(),\"$place\")");
			return_json("SELECT * FROM `vaccines` WHERE `vid`=LAST_INSERT_ID()");
		});
	});

	$app->group("/record",function() use($app,$dbconn) {
		$app->get("/:rid",function($uid,$rid) use($app,$dbconn) {
			//echo "Hi".$uid."rid".$rid."getrid";
			query("SELECT `records`.* FROM `records` WHERE ((`records`.`rid` =$rid) AND (`records`.`uid` =$uid))");
		});
		$app->get("/",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."get";
			query("SELECT `records`.* FROM `records` WHERE (`records`.`uid` =$uid)");
		});
		$app->post("/",function($uid) use($app,$dbconn) {				//not working----------------------------------
			//echo "Hi".$uid."post";
      if(!jwt_verify($app->request->params('token'))) die("Not doctor");
      $token=jwt_verify($app->request->params('token'));
			$diagnosis=$app->request->params('diagnosis');
      $medicine=$app->request->params('medicine');
			$did=$token['did'];
			insert_query("INSERT INTO `records`(`uid`,`diagnosis`,`medicine`,`date`,`did`)
				VALUES ($uid,\"$diagnosis\",\"$medicine\",CURDATE(),$did)");
			return_json("SELECT * FROM `records` WHERE `rid`=LAST_INSERT_ID()");
		});
	});

	$app->group("/allergy",function() use ($app,$dbconn) {
		$app->get("/:aid",function($uid,$aid) use($app,$dbconn){
			//echo "Hi".$uid."aid".$aid."getaid";
			query("SELECT `allergies`.* FROM `allergies` WHERE ((`allergies`.`aid` =$aid) AND (`allergies`.`uid` =$uid))");
		});
		$app->get("(/)",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."get";
			query("SELECT `allergies`.* FROM `allergies` WHERE (`allergies`.`uid` =$uid)");
		});
		$app->post("(/)",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."post";
			$allergen=$app->request->params('allergen');
			$reaction=$app->request->params('reaction');
			$severity=$app->request->params('severity');
			$comment=$app->request->params('comment');
			$actions=$app->request->params('actions');
			$lastupdated=$app->request->params('lastupdated');
			insert_query("INSERT INTO `allergies`(`uid`,`allergen`, `reaction`, `severity`, `comment`
				, `actions`, `lastupdated`) VALUES ($uid,\"$allergen\",\"$reaction\",\"$severity\",
				\"$comment\",\"$actions\", \"$lastupdated\")");
			return_json("SELECT * FROM `allergies` WHERE `aid`=LAST_INSERT_ID()");
		});
		/*$app->post("/edit/:aid",function($uid,$aid) use($app,$dbconn) {		//not working
			//echo "Hi".$uid."aid".$aid."putaid";
			$allergen=$app->request->params('allergen');
			$reaction=$app->request->params('reaction');
			$severity=$app->request->params('severity');
			$comment=$app->request->params('comment');
			$actions=$app->request->params('actions');
			$lastupdated=$app->request->params('lastupdated');
			update_query("UPDATE `allergies` SET `aid`=$aid,`uid`=$uid,`allergen`=\"$allergen\",`reaction`=\"$reaction\",
				`severity`=\"$severity\",`comment`=\"$comment\",`actions`=\"$actions\",
				`lastupdated`=\"$lastupdated\" WHERE 1");
			});*/
		$app->post("/delete/:aid",function($uid,$aid) use($app,$dbconn) {
			//echo "Hi".$uid."aid".$aid."deleteaid";
			update_query("DELETE FROM `allergies` WHERE `aid`=$aid");
		});
	});

	$app->group("/vital",function() use($app,$dbconn) {
		$app->get("/",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."get";
			query("SELECT `vitals`.* FROM `vitals` WHERE (`vitals`.`uid` =$uid)");
		});
		$app->post("/edit(/)",function($uid) use($app,$dbconn){
			//echo "Hi".$uid."put";
			$height=$app->request->params('height');
			$weight=$app->request->params('weight');
			$bmi=$app->request->params('bmi');
			$pulse=$app->request->params('pulse');
			$bp=$app->request->params('bp');
			update_query("INSERT INTO `vitals` (uid,height,weight,bmi,pulse,bp)
										VALUES ($uid,$height,$weight,$bmi,$pulse,$bp)
										ON DUPLICATE KEY
										UPDATE `height`=$height,`weight`=$weight,
										`bmi`=$bmi,`pulse`=$pulse,`bp`=$bp");
		});
	});
});

$app->group("/user/:uid",function() use ($app) {
	$app->group("/vaccine",function() use ($app) {
		$app->get("/:vid",function($uid,$vid) {
			//echo "Hi".$uid."vid".$vid."getvid";
			query("SELECT `vaccines`.* FROM `vaccines` WHERE ((`vaccines`.`vid` =$vid) AND (`vaccines`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `vaccines`.* FROM `vaccines` WHERE (`vaccines`.`uid` =$uid)");
		});
	});

	$app->group("/record",function() use ($app) {
		$app->get("/:rid",function($uid,$rid) {
			//echo "Hi".$uid."rid".$rid."getrid";
			query("SELECT `records`.* FROM `records` WHERE ((`records`.`rid` =$rid) AND (`records`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `records`.* FROM `records` WHERE (`records`.`uid` =$uid)");
		});
	});

	$app->group("/allergy",function() use ($app) {
		$app->get("/:aid",function($uid,$aid) {
			//echo "Hi".$uid."aid".$aid."getaid";
			query("SELECT `allergies`.* FROM `allergies` WHERE ((`allergies`.`aid` =$aid) AND (`allergies`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `allergies`.* FROM `allergies` WHERE (`allergies`.`uid` =$uid)");
		});
	});

	$app->group("/vital",function() use ($app) {
		$app->get("/:vitalid",function($uid,$vitalid) {
			//echo "Hi".$uid."vitalid".$vitalid."getvitalid";
			query("SELECT `vitals`.* FROM `vitals` WHERE ((`vitals`.`vitalid` =$vitalid) AND (`vitals`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `vitals`.* FROM `vitals` WHERE (`vitals`.`uid` =$uid)");
		});
	});
});

/*Megh api starts here*/

$app->post('/login/user(/)',function () use ($app,$dbconn,$client,$KEY) {
  $user=$app->request->params("email");
	$password=hash('sha256',$app->request->params("password"));

	$usrcheck = ORM::for_table('users')->where('email', $user)->find_one();
	if($usrcheck==NULL)
	{
	  $final=["success"=>false];
	}
	else
	{
		$pswcheck=ORM::for_table('users')->where('password', $password)
	  					->where('email',$user)->find_one();
 	if($pswcheck==NULL)
  	{
	    //echo "Invalid Password";
	    $final=["success"=>false];
	  }
	  else
	  {
	    //echo "Logged In";
	    $token= jwt_create($pswcheck->uid);
	    $final=[
				"success"=>true,
				"token"=>$token,
				"uid"=>$pswcheck->uid,
				"name"=>$pswcheck->name
			];
	  }
	}
	echo json_encode($final);

});

$app->post('/login/doctor(/)',function () use ($app,$dbconn,$client,$KEY) {
  $user=$app->request->params("email");
	$password 	=hash('sha256',$app->request->params("password"));

	$usrcheck = ORM::for_table('users')->where('email', $user)->find_one();
	if($usrcheck==NULL)
	{
	  $final=["success"=>false];
	}
	else
	{
	  $pswcheck=ORM::for_table('users')->where('password', $password)
	  					->where('email',$user)->find_one();
	  if($pswcheck==NULL)
	  {
	  	$final=["success"=>false];
	  }
	  else
	  {
	  	$getdid=mysql_query("SELECT `doctors`.`did` FROM `doctors` WHERE (`doctors`.`uid` ={$pswcheck->uid})");
		if(! $getdid) {
			die('Could not get data' .mysql_error());
		} else if(mysql_num_rows($getdid)==0){
		  $final=["success"=>false];
		} else {

			$row =mysql_fetch_assoc($getdid);
			$did=$row['did'];
			$token= jwt_create_doctor($pswcheck->uid, $did);
		    $final=[
					"success"=>true,
					"token"=>$token,
					"uid"=>$pswcheck->uid,
					"name"=>$pswcheck->name,
					"did"=>$did
				];
		}
	  }
	}
	echo json_encode($final);
});

$app->post('/login/pharmacist(/)',function () use ($app,$dbconn,$client,$KEY) {
  $user=$app->request->params("email");
	$password 	=hash('sha256',$app->request->params("password"));

	$usrcheck = ORM::for_table('users')->where('email', $user)->find_one();
	if($usrcheck==NULL) {
    echo "!;";
	  $final=["success"=>false];
	} else {
	  $pswcheck=ORM::for_table('users')->where('password', $password)
	  					->where('email',$user)->find_one();
	  if($pswcheck==NULL) {
	  	$final=["success"=>false];
	  } else {
	  	$getdid=mysql_query("SELECT `phid` FROM `pharmacists`
                            WHERE `uid` ={$pswcheck->uid}");
  		if(! $getdid) {
  			die('Could not get data' .mysql_error());
  		} else if(mysql_num_rows($getdid)==0) {
  		  $final=["success"=>false];
  		} else {

  			$row =mysql_fetch_assoc($getdid);
  			$did=$row['phid'];
  			$token= jwt_create_pharmacist($pswcheck->uid, $did);
  		    $final=[
  					"success"=>true,
  					"token"=>$token,
  					"uid"=>$pswcheck->uid,
  					"name"=>$pswcheck->name,
  					"phid"=>$did
  				];
  		}
	  }
	}
	echo json_encode($final);
});

$app->post('/register/user(/)',function () use ($app,$dbconn,$client,$KEY) {
  $email=$app->request->params("email");
	$password=hash('sha256',$app->request->params("password"));
	$name=$app->request->params("name");
	$gender=$app->request->params("gender");
	$dob=$app->request->params("dob");

	$addper = ORM::for_table('users')->create();

	$addper->name = $name;
	$addper->email= $email;
	$addper->password=$password;
	$addper->gender = $gender;
	$addper->dob = $dob;

	$addper->save();

	$token= jwt_create($addper->id());
	    $final=[
				"success"=>true,
				"token"=>$token,
				"uid"=>$addper->id(),
				"name"=>$name
			];
	echo json_encode($final);

});

$app->post('/register/doctor(/)',function () use ($app,$dbconn,$client,$KEY) {
	$email=$app->request->params("email");
	$password=hash('sha256',$app->request->params("password"));
	$name=$app->request->params("name");
	$gender=$app->request->params("gender");
	$dob=$app->request->params("dob");

	$addper = ORM::for_table('users')->create();

	$addper->name = $name;
	$addper->email= $email;
	$addper->password=$password;
	$addper->gender = $gender;
	$addper->dob = $dob;

	$addper->save();
	$uid=$addper->id();
	$did=$app->request->params("did");

	$addper = ORM::for_table('doctors')->create();

	$addper->uid=$uid;
	$addper->did=$did;

	$addper->save();

	$token= jwt_create_doctor($addper->id(),$did);
	    $final=[
				"success"=>true,
				"token"=>$token,
				"uid"=>$addper->id(),
				"name"=>$name,
				"did"=>$did
			];
	echo json_encode($final);
});

$app->post('/register/pharmacist(/)',function () use ($app,$dbconn,$client,$KEY) {
	$email=$app->request->params("email");
	$password=hash('sha256',$app->request->params("password"));
	$name=$app->request->params("name");
	$gender=$app->request->params("gender");
	$dob=$app->request->params("dob");

	$addper = ORM::for_table('users')->create();

	$addper->name = $name;
	$addper->email= $email;
	$addper->password=$password;
	$addper->gender = $gender;
	$addper->dob = $dob;

	$addper->save();
	$uid=$addper->id();
	$did=$app->request->params("phid");

	$addper = ORM::for_table('pharmacists')->create();

	$addper->uid=$uid;
	$addper->phid=$did;

	$addper->save();

	$token= jwt_create_pharmacist($addper->id(),$did);
	    $final=[
				"success"=>true,
				"token"=>$token,
				"uid"=>$addper->id(),
				"name"=>$name,
				"phid"=>$did
			];
	echo json_encode($final);
});

function jwt_verify($token){
	GLOBAL $KEY;
	$decoded = JWT::decode($token, $KEY, array('HS256'));
	$decoded_array = (array) $decoded;
	if($decoded_array['expires']<time()) return false;
	return $decoded_array;
}

function jwt_create($uid) {
	GLOBAL $KEY;
	$token = array(
			    "uid" => $uid,
			    "expires" => time()+(24*60*60)
			);
	$jwt = JWT::encode($token, $KEY);
	return $jwt;
}

function jwt_create_doctor($uid,$did) {
	GLOBAL $KEY;
	$token = array(
			    "uid" => $uid,
			    "did"=>$did,
			    "expires" => time()+(24*60*60)
			);
	$jwt = JWT::encode($token, $KEY);
	return $jwt;
}

function jwt_create_pharmacist($uid,$did) {
	GLOBAL $KEY;
	$token = array(
			    "uid" => $uid,
			    "phid"=>$did,
			    "expires" => time()+(24*60*60)
			);
	$jwt = JWT::encode($token, $KEY);
	return $jwt;
}

$app->run();
?>
