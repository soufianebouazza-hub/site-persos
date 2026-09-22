<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit;
$name=$argv[2]??'';if(!preg_match('/^evitrine_test_[a-f0-9]{12}$/D',$name))exit(1);
$db=new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
if(($argv[1]??'')==='create')$db->exec('CREATE DATABASE '.$name.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
elseif(($argv[1]??'')==='drop')$db->exec('DROP DATABASE '.$name);
else exit(1);
