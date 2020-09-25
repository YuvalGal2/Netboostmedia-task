<?php

// connections
require_once ("mysql.inc.php");

// services
require_once ("app/services/WebCrawlerService.php");
require_once __DIR__.'/../vendor/autoload.php';
set_time_limit(getenv("SET_TIME_LIMIT"));