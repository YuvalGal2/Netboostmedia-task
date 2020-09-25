<?php
    use app\Services\WebCrawlerService;
    require_once ("includes/init.inc.php");
    if(isset($argv[0])) {
        platformCrawlerTest($argv[0]);
    }
    if(isset($_GET['web_url'])) {
        platformCrawlerTest($_GET['web_url']);
    }

    function initialize() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
        $dotenv->load();
        error_reporting(E_ERROR);
        set_time_limit(getenv($_ENV['SET_TIME_LIMIT']));
    }

    function platformCrawlerTest($url) {
        initialize();
        echo "Starting!...<br>";
        WebCrawlerService::crawl_page($url,$url, $_ENV['MAX_DEPTH']);
        echo "Finished!";
    }

?>