<?php

namespace app\Services;

class WebCrawlerService {
    const deep_regex_pattern = '/\b(?:(?:https?):\/\/|www\.\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i';
    public static function crawl_page($url, $depth)
    {
        $already_visted_urls = [];
        if ( $depth  > 0) {
            $html_code = file_get_contents($url);
            $matches = self::get_links_in_code($html_code);
            echo "<-------------------- in depth: $depth , in site : $url -----------------------------> ";
            echo "<br>";
            foreach ($matches as $urls_array) {
                foreach ($urls_array as $url) {
                    if (!in_array($url,$already_visted_urls)) {
                        echo htmlspecialchars_decode($url);
                    echo "<br>";
                    $already_visted_urls[] = $url;
                        self::crawl_page($url, $depth - 1);
                    }
                }
            }
        }

    }

    private static function validate_links() {
    }
    private static function get_links_in_code($html_code) {
        preg_match_all(self::regular_regex_pattern, $html_code, $matches);
        // could convert to ternary condition, but really would make it unreadble.
        if (isset($matches)) {
            return $matches;
        }
        else {
            return [];
        }
    }
}