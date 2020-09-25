<?php

namespace app\Services;
use \DOMDocument;
use \app\includes;
class WebCrawlerService {
    const deep_regex_pattern = '/\b(?:(?:https?):\/\/|www\.\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i';

    public static function crawl_page($dest, $url, $depth) {
        self::validate_table_existnce();
        if ( $depth  > 0) {
            if (isset($dest) && strlen($dest) >= 3) { // minimum length possible of link
                $html_code = file_get_contents($url, false, stream_context_create(['http' => ['ignore_errors' => true]]));
                $urls_array = self::get_links_in_code($url, $html_code);
                foreach ($urls_array as $url) {
                    if (self::is_validated_link($dest,$url, $http_response_header[0])) {
                        self::insert_link($url);
                        self::crawl_page($dest, $url, $depth - 1);
                    }
                }
            }
        }
    }

    private static function insert_link($url) {
        $conn = self::get_connection();
        $sql = "INSERT INTO logs (link) VALUES (?)";
        $stmt= $conn->prepare($sql);
        $stmt->execute([$url]);
    }

    private static function validate_table_existnce() {
        $conn = self::get_connection();
        $sql ="
    SET sql_notes = 0; 
     CREATE TABLE IF NOT EXISTS logs (
     ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
     LINK TEXT NOT NULL UNIQUE);
     SET sql_notes = 1;" ;
        $conn->exec($sql);
    }

    private static function  get_connection() {
        $instance = includes\MySqlConnect::getInstance();
        return $instance->getConnection();
    }
    private static function urlToDomain($url) {
        return implode(array_slice(explode('/', preg_replace('/https?:\/\/(www\.)?/', '', $url)), 0, 1));
    }

    private static function is_validated_link($dest, $url,$http_status) {
        $conn = self::get_connection();
        if ($_ENV['ONLY_ALLOW_SAME_DOMAIN'] === "1" ) {
            if (strpos(self::urlToDomain($url), self::urlToDomain($dest)) === false) {
                return false;
            }
        }
        if (isset($url) && strlen($url) >= 3) { // minimum length possible of link
            if(substr($url, -1) == '/') {
                $url = substr($url, 0, -1);
            }
            if (strpos($http_status, '200') !== false) {
                $link = $conn->query("SELECT * FROM logs where LINK = '$url'")->fetch();
                if (!$link) {
                    return true;
                }
            }
        }
    }


    private static function platformDeepScan($html_code) {
        preg_match_all(self::deep_regex_pattern, $html_code, $matches);
        $urls = [];
        // could convert to ternary condition, but really would make it unreadble.
        if (isset($matches)) {
            foreach ($matches as $match) {
                foreach ($match as $link) {
                    $urls[] = $link;
                }
            }
            return $urls;
        } else {
            return [];
        }
    }

    private static function platformLightScan($url){
        $links = [];
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);
        $anchors = $dom->getElementsByTagName('a');
        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            $links[] = $href;
        }
        return $links;
    }

    private static function get_links_in_code($url,$html_code)
    {
        if ($_ENV["ENABLE_DEEP_SCAN"] === "1") {
            return self::platformDeepScan($html_code);
        }
        return self::platformLightScan($url);
    }
}