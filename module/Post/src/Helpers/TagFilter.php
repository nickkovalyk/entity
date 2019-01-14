<?php

namespace Post\Helpers;

class TagFilter
{

    public static function closeAndStrip(string $html, string $allowableTags) : string
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $html =  $doc->saveHTML();
        $html = strip_tags($html, $allowableTags);
        return $html;
    }

    public static function closeTags($str) {

    }

}