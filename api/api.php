<?php

if (explode("/", $page)[1] != "method") notFound();

$method = explode(".", explode("/", $page)[2]);

if (file_exists('api/method/' . $method[0] . '.php')) 
	include 'api/method/'. $method[0].'.php';
