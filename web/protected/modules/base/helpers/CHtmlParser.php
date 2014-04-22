<?php

class CHtmlParser 
{
    public static function parseTags($content, $selectors)
    {
        if(!$content || !$selectors){
            return '';
        }
        $doc = phpQuery::newDocument($content);
        $result = array();
        if(!is_array($selectors)){
            $selectors = array($selectors);
        }
        foreach($selectors as $selector){
            $result[$selector] = self::processtags($doc->find($selector));
        }
        return count($result > 1) ? $result : $result[0];
    }
    
    protected static function processTags($tags)
    {
        $result = array();
        if(!$tags){
            return $result;
        }
        foreach($tags as $tag){
            switch($tag->tagName){
                case 'img'      : $result[] = $tag->getAttribute('src');
                    break;
                case 'a'        : $result[] = $tag->getAttribute('href');
                    break;
                case 'iframe'   : $result[] = $tag->getAttribute('src');
                default : $result[] = pq($tag)->html();
            }
        }
        return $result;
    }
}