<?php
    use app\Services\WebCrawlerService;
    require_once ("includes/init.inc.php");
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
    $dotenv->load();

    WebCrawlerService::crawl_page("http://www.lordbingo.co.uk/",3);
?>