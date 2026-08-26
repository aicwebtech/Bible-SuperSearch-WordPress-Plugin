<?php

namespace BibleSuperSearch\Common;

class QueryStringParser
{
    public static function parseToFormData($query_string)
    {
        if($query_string) {
            $query_string = urldecode($query_string);
            $query_string = str_replace('.', ' ', $query_string);
            $parts = explode('/', $query_string);
            $mode  = array_shift($parts);

            if($mode == '') {
                $mode = array_shift($parts);
            }

            // Covering every case from the UI, however, some are not used here
            switch($mode) {
                case 'c':   // Cache uuid (hash) 
                    return self::hashCache($parts);
                    break;
                case 'cr':   // Cross reference
                    return self::hashReference($parts, true);
                    break;
                case 'p':   // Passage
                    return self::hashPassage($parts);
                    break;                   
                case 'r':   // Reference string
                    return self::hashReference($parts);
                    break;                
                case 'q':   // Request string
                    return self::hashRequest($parts);
                    break;
                case 's':   // Search string
                    return self::hashSearch($parts);
                    break;                      
                case 'sl':   // Link to passage within search results list
                    return self::hashSearchLink($parts);
                    break;                
                case 'context': // Contextual lookup
                    return self::hashContext($parts);
                    break;
                case 'strongs': // Strongs lookup
                    return self::hashSearch($parts);
                    break;
                case 'f': // JSON-endoded form data
                    return self::hashForm($parts);
                    break;
            }
        }

        return [];
    }

    public static function buildTitle($formData, $baseTitle = '', $baseFirst = false)
    {
        $mainSep = ' - ';

        $fields = [
            'request','reference','search','search_all','search_any',
            'search_one','search_none','search_phrase', 'page'
        ];

        $values = [];

        foreach($fields as $field) {
            if(isset($formData[$field]) && $formData[$field] != '') {
                if($field == 'page') {
                    $values[] = 'Page' . ' ' . $formData[$field];
                } else {
                    $values[] = $formData[$field];
                }
            }
        }

        if(isset($formData['context']) && $formData['context'] == true) {
            $values[] = 'In Context';
        }

        $bssTitle = implode(' | ', $values);
        
        if(!$baseTitle) {
            $newTitle = $bssTitle;
        } else {
            if(!$bssTitle) {
                $newTitle = $baseTitle;
            } else {
                if($baseFirst) {
                    $newTitle = $baseTitle . $mainSep . $bssTitle;
                } else {
                    $newTitle = $bssTitle . $mainSep . $baseTitle;
                }
            }
        }

        return $newTitle;
    }

    public static function hashCache($parts) 
    {
        $hash = $parts[0] ?? null;
        $page = $parts[1] ?? null;
    
        return [
            'hash' => $hash,
            'page' => $page
        ];
    }

    public static function hashPassage($parts) 
    {
        $partsObj = self::_explodeHashPassage($parts);
        $formData = self::_assembleHashPassage($partsObj);
        return $formData;
    }

    public static function hashSearchLink($parts) 
    {
        $uuid = array_shift($parts);
        $partsObj = self::_explodeHashPassage($parts);
        $formData = self::_assembleHashPassage($partsObj);
        $formData['results_list_cache_id'] = $uuid;
        return $formData;
    }

    public static function hashStrongs($parts) 
    {
        $strongsNum = $parts[0] ?? null;
        $formData = ['search' => $strongsNum];
        return $formData;
    }

    public static function hashContext($parts) 
    {
        $partsObj = self::_explodeHashPassage($parts);

        if(!$partsObj['chap'] || !$partsObj['verse'] || strpos($partsObj['chap'], '-') !== false || strpos($partsObj['verse'], '-') !== false) {
            return;
        }

        $formData = self::_assembleHashPassage($partsObj);
        $formData['context'] = true;
        return $formData;
    }
    
    public static function hashReference($parts, $isCrossReference = false) 
    {
        $partsObj = self::_explodeHashPassage($parts);

        $partsObj['chap']  = null;
        $partsObj['verse'] = null;

        return self::_assembleHashPassage($partsObj);
    }
    
    public static function hashRequest($parts) 
    {
        return self::hashSearch($parts, true);
    }

    public static function hashSearch($parts, $forceUseRequestField = false) 
    {

        $bible  = $parts[0] ?? null;
        $search = $parts[1] ?? null;
        $page = $parts[2] ?? null;
        $searchType = $parts[3] ?? null;
        $reference = $parts[4] ?? null;
        $useRequestField = ($forceUseRequestField || self::formHasField('request')) ? true : false;

        $formData = [
            // search: str_replace('%20', ' ', $search),
            'bible' => $bible ? explode(',', $bible) : null,
            'search_type' => $searchType,
            'reference' => $reference,
            'page' => $page
        ];
        
        if($useRequestField) {
            $formData['request'] = str_replace('%20', ' ', $search);
        }
        else {
            $formData['search'] = str_replace('%20', ' ', $search);
        }

        return $formData;
    }

    public static function hashForm($parts) 
    {
        $formData = [];

        // parts[0] comes straight from the URL hash (attacker-controllable). Don't let
        // malformed JSON throw out of the route handler - just ignore an invalid payload.
        if($parts[0] ?? null) {
            try {
                $formData = json_decode($parts[0], true);
            }
            catch(Exception $e) {
                return;
            }
        }

        return $formData;
    }

    private static function _explodeHashPassage($parts) 
    {
        $exploded = [
            'bible' => $parts[0] ?? null,
            'book'  => $parts[1] ?? null,
            'chap'  => $parts[2] ?? null,
            'verse' => $parts[3] ?? null
        ];

        return $exploded;
    }

    private static function _assembleHashPassage($partsObj) 
    {
        if(!$partsObj['book']) {
            return [];
        }

        // A form may opt to prefer its reference field (and book/chapter/verse selector)
        // over the combined request field when populating a passage from the URL hash.
        $useRequestField = true;

        $ref = str_replace('%20', ' ', $partsObj['book']);

        if($partsObj['chap']) {
            $ref .= ' ' . $partsObj['chap'];

            if($partsObj['verse'] && strpos($partsObj['chap'], '-') === false) {
                $ref .= ':' . $partsObj['verse'];
            }
        }

        $formData = [
            'bible' => $partsObj['bible'] ? explode(',', $partsObj['bible']) : null
        ];

        if($useRequestField) {
            $formData['request'] = $ref;
        }
        else {
            $formData['reference'] = $ref;
        }

        return $formData;
    }

    private static function formHasField($fieldName) 
    {
        return true;
    }
}