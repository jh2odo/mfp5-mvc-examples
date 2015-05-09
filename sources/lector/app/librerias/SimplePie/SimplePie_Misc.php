<?php

class SimplePie_Misc
{
    function time_hms($seconds)
    {
        $time = '';

        $hours = floor($seconds / 3600);
        $remainder = $seconds % 3600;
        if ($hours > 0) {
            $time .= $hours . ':';
        }

        $minutes = floor($remainder / 60);
        $seconds = $remainder % 60;
        if ($minutes < 10 && $hours > 0) {
            $minutes = '0' . $minutes;
        }
        if ($seconds < 10) {
            $seconds = '0' . $seconds;
        }

        $time .= $minutes . ':';
        $time .= $seconds;

        return $time;
    }

    function absolutize_url($relative, $base)
    {
        if ($relative !== '') {
            $relative = SimplePie_Misc::parse_url($relative);
            if ($relative['scheme'] !== '') {
                $target = $relative;
            } elseif ($base !== '') {
                $base = SimplePie_Misc::parse_url($base);
                $target = SimplePie_Misc::parse_url('');
                if ($relative['authority'] !== '') {
                    $target = $relative;
                    $target['scheme'] = $base['scheme'];
                } else {
                    $target['scheme'] = $base['scheme'];
                    $target['authority'] = $base['authority'];
                    if ($relative['path'] !== '') {
                        if (strpos($relative['path'], '/') === 0) {
                            $target['path'] = $relative['path'];
                        } elseif ($base['authority'] !== '' && $base['path'] === '') {
                            $target['path'] = '/' . $relative['path'];
                        } elseif (($last_segment = strrpos($base['path'], '/')) !== false) {
                            $target['path'] = substr($base['path'], 0, $last_segment + 1) . $relative['path'];
                        } else {
                            $target['path'] = $relative['path'];
                        }
                        $target['query'] = $relative['query'];
                    } else {
                        $target['path'] = $base['path'];
                        if ($relative['query'] !== '') {
                            $target['query'] = $relative['query'];
                        } elseif ($base['query'] !== '') {
                            $target['query'] = $base['query'];
                        }
                    }
                }
                $target['fragment'] = $relative['fragment'];
            } else {
                // No base URL, just return the relative URL
                $target = $relative;
            }
            $return = SimplePie_Misc::compress_parse_url($target['scheme'], $target['authority'], $target['path'], $target['query'], $target['fragment']);
        } else {
            $return = $base;
        }
        $return = SimplePie_Misc::normalize_url($return);
        return $return;
    }

    function remove_dot_segments($input)
    {
        $output = '';
        while (strpos($input, './') !== false || strpos($input, '/.') !== false || $input == '.' || $input == '..') {
            // A: If the input buffer begins with a prefix of "../" or "./", then remove that prefix from the input buffer; otherwise,
            if (strpos($input, '../') === 0) {
                $input = substr($input, 3);
            } elseif (strpos($input, './') === 0) {
                $input = substr($input, 2);
            } // B: if the input buffer begins with a prefix of "/./" or "/.", where "." is a complete path segment, then replace that prefix with "/" in the input buffer; otherwise,
            elseif (strpos($input, '/./') === 0) {
                $input = substr_replace($input, '/', 0, 3);
            } elseif ($input == '/.') {
                $input = '/';
            } // C: if the input buffer begins with a prefix of "/../" or "/..", where ".." is a complete path segment, then replace that prefix with "/" in the input buffer and remove the last segment and its preceding "/" (if any) from the output buffer; otherwise,
            elseif (strpos($input, '/../') === 0) {
                $input = substr_replace($input, '/', 0, 4);
                $output = substr_replace($output, '', strrpos($output, '/'));
            } elseif ($input == '/..') {
                $input = '/';
                $output = substr_replace($output, '', strrpos($output, '/'));
            } // D: if the input buffer consists only of "." or "..", then remove that from the input buffer; otherwise,
            elseif ($input == '.' || $input == '..') {
                $input = '';
            } // E: move the first path segment in the input buffer to the end of the output buffer, including the initial "/" character (if any) and any subsequent characters up to, but not including, the next "/" character or the end of the input buffer
            elseif (($pos = strpos($input, '/', 1)) !== false) {
                $output .= substr($input, 0, $pos);
                $input = substr_replace($input, '', 0, $pos);
            } else {
                $output .= $input;
                $input = '';
            }
        }
        return $output . $input;
    }

    function get_element($realname, $string)
    {
        $return = array();
        $name = preg_quote($realname, '/');
        if (preg_match_all("/<($name)" . SIMPLEPIE_PCRE_HTML_ATTRIBUTE . "(>(.*)<\/$name>|(\/)?>)/siU", $string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            for ($i = 0, $total_matches = count($matches); $i < $total_matches; $i++) {
                $return[$i]['tag'] = $realname;
                $return[$i]['full'] = $matches[$i][0][0];
                $return[$i]['offset'] = $matches[$i][0][1];
                if (strlen($matches[$i][3][0]) <= 2) {
                    $return[$i]['self_closing'] = true;
                } else {
                    $return[$i]['self_closing'] = false;
                    $return[$i]['content'] = $matches[$i][4][0];
                }
                $return[$i]['attribs'] = array();
                if (isset($matches[$i][2][0]) && preg_match_all('/[\x09\x0A\x0B\x0C\x0D\x20]+([^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3D\x3E]*)(?:[\x09\x0A\x0B\x0C\x0D\x20]*=[\x09\x0A\x0B\x0C\x0D\x20]*(?:"([^"]*)"|\'([^\']*)\'|([^\x09\x0A\x0B\x0C\x0D\x20\x22\x27\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x3E]*)?))?/', ' ' . $matches[$i][2][0] . ' ', $attribs, PREG_SET_ORDER)) {
                    for ($j = 0, $total_attribs = count($attribs); $j < $total_attribs; $j++) {
                        if (count($attribs[$j]) == 2) {
                            $attribs[$j][2] = $attribs[$j][1];
                        }
                        $return[$i]['attribs'][strtolower($attribs[$j][1])]['data'] = SimplePie_Misc::entities_decode(end($attribs[$j]), 'UTF-8');
                    }
                }
            }
        }
        return $return;
    }

    function element_implode($element)
    {
        $full = "<$element[tag]";
        foreach ($element['attribs'] as $key => $value) {
            $key = strtolower($key);
            $full .= " $key=\"" . htmlspecialchars($value['data']) . '"';
        }
        if ($element['self_closing']) {
            $full .= ' />';
        } else {
            $full .= ">$element[content]</$element[tag]>";
        }
        return $full;
    }

    function error($message, $level, $file, $line)
    {
        switch ($level) {
            case E_USER_ERROR:
                $note = 'PHP Error';
                break;
            case E_USER_WARNING:
                $note = 'PHP Warning';
                break;
            case E_USER_NOTICE:
                $note = 'PHP Notice';
                break;
            default:
                $note = 'Unknown Error';
                break;
        }
        error_log("$note: $message in $file on line $line", 0);
        return $message;
    }

    /**
     * If a file has been cached, retrieve and display it.
     *
     * This is most useful for caching images (get_favicon(), etc.),
     * however it works for all cached files.  This WILL NOT display ANY
     * file/image/page/whatever, but rather only display what has already
     * been cached by SimplePie.
     *
     * @access public
     * @see SimplePie::get_favicon()
     * @param str $identifier_url URL that is used to identify the content.
     * This may or may not be the actual URL of the live content.
     * @param str $cache_location Location of SimplePie's cache.  Defaults
     * to './cache'.
     * @param str $cache_extension The file extension that the file was
     * cached with.  Defaults to 'spc'.
     * @param str $cache_class Name of the cache-handling class being used
     * in SimplePie.  Defaults to 'SimplePie_Cache', and should be left
     * as-is unless you've overloaded the class.
     * @param str $cache_name_function Obsolete. Exists for backwards
     * compatibility reasons only.
     */
    function display_cached_file($identifier_url, $cache_location = './cache', $cache_extension = 'spc', $cache_class = 'SimplePie_Cache', $cache_name_function = 'md5')
    {
        $cache = call_user_func(array($cache_class, 'create'), $cache_location, $identifier_url, $cache_extension);

        if ($file = $cache->load()) {
            // A�adido
            /*
            $headers = apache_request_headers();
            if(($headers['If-None-Match'] ==  $file['headers']['etag']) &&
                ($file['headers']['last-modified'] == $headers['If-Modified-Since'])) {

                    header('HTTP/1.1 304 Not Modified');
                    header('Content-type:' . $file['headers']['content-type']);
                    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT'); // 7 days
                    header('Etag: '.$file['headers']['etag']);
                    header('Last-Modified: '.$file['headers']['last-modified']);

                    exit;
            }*/

            if (isset($file['headers']['content-type'])) {
                header('Content-type:' . $file['headers']['content-type']);
            } else {
                header('Content-type: application/octet-stream');
            }

            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT'); // 7 days

            // A�adidos
            header('Etag: ' . $file['headers']['etag']);
            header('Last-Modified: ' . $file['headers']['last-modified']);

            echo $file['body'];
            exit;
        }

        die('Cached file for ' . $identifier_url . ' cannot be found.');
    }

    function fix_protocol($url, $http = 1)
    {
        $url = SimplePie_Misc::normalize_url($url);
        $parsed = SimplePie_Misc::parse_url($url);
        if ($parsed['scheme'] !== '' && $parsed['scheme'] != 'http' && $parsed['scheme'] != 'https') {
            return SimplePie_Misc::fix_protocol(SimplePie_Misc::compress_parse_url('http', $parsed['authority'], $parsed['path'], $parsed['query'], $parsed['fragment']), $http);
        }

        if ($parsed['scheme'] === '' && $parsed['authority'] === '' && !file_exists($url)) {
            return SimplePie_Misc::fix_protocol(SimplePie_Misc::compress_parse_url('http', $parsed['path'], '', $parsed['query'], $parsed['fragment']), $http);
        }

        if ($http == 2 && $parsed['scheme'] !== '') {
            return "feed:$url";
        } elseif ($http == 3 && strtolower($parsed['scheme']) == 'http') {
            return substr_replace($url, 'podcast', 0, 4);
        } elseif ($http == 4 && strtolower($parsed['scheme']) == 'http') {
            return substr_replace($url, 'itpc', 0, 4);
        } else {
            return $url;
        }
    }

    function parse_url($url)
    {
        static $cache = array();
        if (isset($cache[$url])) {
            return $cache[$url];
        } elseif (preg_match('/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/', $url, $match)) {
            for ($i = count($match); $i <= 9; $i++) {
                $match[$i] = '';
            }
            return $cache[$url] = array('scheme' => $match[2], 'authority' => $match[4], 'path' => $match[5], 'query' => $match[7], 'fragment' => $match[9]);
        } else {
            return $cache[$url] = array('scheme' => '', 'authority' => '', 'path' => '', 'query' => '', 'fragment' => '');
        }
    }

    function compress_parse_url($scheme = '', $authority = '', $path = '', $query = '', $fragment = '')
    {
        $return = '';
        if ($scheme !== '') {
            $return .= "$scheme:";
        }
        if ($authority !== '') {
            $return .= "//$authority";
        }
        if ($path !== '') {
            $return .= $path;
        }
        if ($query !== '') {
            $return .= "?$query";
        }
        if ($fragment !== '') {
            $return .= "#$fragment";
        }
        return $return;
    }

    function normalize_url($url)
    {
        $url = preg_replace_callback('/%([0-9A-Fa-f]{2})/', array('SimplePie_Misc', 'percent_encoding_normalization'), $url);
        $url = SimplePie_Misc::parse_url($url);
        $url['scheme'] = strtolower($url['scheme']);
        if ($url['authority'] !== '') {
            $url['authority'] = strtolower($url['authority']);
            $url['path'] = SimplePie_Misc::remove_dot_segments($url['path']);
        }
        return SimplePie_Misc::compress_parse_url($url['scheme'], $url['authority'], $url['path'], $url['query'], $url['fragment']);
    }

    function percent_encoding_normalization($match)
    {
        $integer = hexdec($match[1]);
        if ($integer >= 0x41 && $integer <= 0x5A || $integer >= 0x61 && $integer <= 0x7A || $integer >= 0x30 && $integer <= 0x39 || $integer == 0x2D || $integer == 0x2E || $integer == 0x5F || $integer == 0x7E) {
            return chr($integer);
        } else {
            return strtoupper($match[0]);
        }
    }

    /**
     * Remove bad UTF-8 bytes
     *
     * PCRE Pattern to locate bad bytes in a UTF-8 string comes from W3C
     * FAQ: Multilingual Forms (modified to include full ASCII range)
     *
     * @author Geoffrey Sneddon
     * @see http://www.w3.org/International/questions/qa-forms-utf-8
     * @param string $str String to remove bad UTF-8 bytes from
     * @return string UTF-8 string
     */
    function utf8_bad_replace($str)
    {
        if (function_exists('iconv') && ($return = @iconv('UTF-8', 'UTF-8//IGNORE', $str))) {
            return $return;
        } elseif (function_exists('mb_convert_encoding') && ($return = @mb_convert_encoding($str, 'UTF-8', 'UTF-8'))) {
            return $return;
        } elseif (preg_match_all('/(?:[\x00-\x7F]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/', $str, $matches)) {
            return implode("\xEF\xBF\xBD", $matches[0]);
        } elseif ($str !== '') {
            return "\xEF\xBF\xBD";
        } else {
            return '';
        }
    }

    /**
     * Converts a Windows-1252 encoded string to a UTF-8 encoded string
     *
     * @static
     * @access public
     * @param string $string Windows-1252 encoded string
     * @return string UTF-8 encoded string
     */
    function windows_1252_to_utf8($string)
    {
        static $convert_table = array("\x80" => "\xE2\x82\xAC", "\x81" => "\xEF\xBF\xBD", "\x82" => "\xE2\x80\x9A", "\x83" => "\xC6\x92", "\x84" => "\xE2\x80\x9E", "\x85" => "\xE2\x80\xA6", "\x86" => "\xE2\x80\xA0", "\x87" => "\xE2\x80\xA1", "\x88" => "\xCB\x86", "\x89" => "\xE2\x80\xB0", "\x8A" => "\xC5\xA0", "\x8B" => "\xE2\x80\xB9", "\x8C" => "\xC5\x92", "\x8D" => "\xEF\xBF\xBD", "\x8E" => "\xC5\xBD", "\x8F" => "\xEF\xBF\xBD", "\x90" => "\xEF\xBF\xBD", "\x91" => "\xE2\x80\x98", "\x92" => "\xE2\x80\x99", "\x93" => "\xE2\x80\x9C", "\x94" => "\xE2\x80\x9D", "\x95" => "\xE2\x80\xA2", "\x96" => "\xE2\x80\x93", "\x97" => "\xE2\x80\x94", "\x98" => "\xCB\x9C", "\x99" => "\xE2\x84\xA2", "\x9A" => "\xC5\xA1", "\x9B" => "\xE2\x80\xBA", "\x9C" => "\xC5\x93", "\x9D" => "\xEF\xBF\xBD", "\x9E" => "\xC5\xBE", "\x9F" => "\xC5\xB8", "\xA0" => "\xC2\xA0", "\xA1" => "\xC2\xA1", "\xA2" => "\xC2\xA2", "\xA3" => "\xC2\xA3", "\xA4" => "\xC2\xA4", "\xA5" => "\xC2\xA5", "\xA6" => "\xC2\xA6", "\xA7" => "\xC2\xA7", "\xA8" => "\xC2\xA8", "\xA9" => "\xC2\xA9", "\xAA" => "\xC2\xAA", "\xAB" => "\xC2\xAB", "\xAC" => "\xC2\xAC", "\xAD" => "\xC2\xAD", "\xAE" => "\xC2\xAE", "\xAF" => "\xC2\xAF", "\xB0" => "\xC2\xB0", "\xB1" => "\xC2\xB1", "\xB2" => "\xC2\xB2", "\xB3" => "\xC2\xB3", "\xB4" => "\xC2\xB4", "\xB5" => "\xC2\xB5", "\xB6" => "\xC2\xB6", "\xB7" => "\xC2\xB7", "\xB8" => "\xC2\xB8", "\xB9" => "\xC2\xB9", "\xBA" => "\xC2\xBA", "\xBB" => "\xC2\xBB", "\xBC" => "\xC2\xBC", "\xBD" => "\xC2\xBD", "\xBE" => "\xC2\xBE", "\xBF" => "\xC2\xBF", "\xC0" => "\xC3\x80", "\xC1" => "\xC3\x81", "\xC2" => "\xC3\x82", "\xC3" => "\xC3\x83", "\xC4" => "\xC3\x84", "\xC5" => "\xC3\x85", "\xC6" => "\xC3\x86", "\xC7" => "\xC3\x87", "\xC8" => "\xC3\x88", "\xC9" => "\xC3\x89", "\xCA" => "\xC3\x8A", "\xCB" => "\xC3\x8B", "\xCC" => "\xC3\x8C", "\xCD" => "\xC3\x8D", "\xCE" => "\xC3\x8E", "\xCF" => "\xC3\x8F", "\xD0" => "\xC3\x90", "\xD1" => "\xC3\x91", "\xD2" => "\xC3\x92", "\xD3" => "\xC3\x93", "\xD4" => "\xC3\x94", "\xD5" => "\xC3\x95", "\xD6" => "\xC3\x96", "\xD7" => "\xC3\x97", "\xD8" => "\xC3\x98", "\xD9" => "\xC3\x99", "\xDA" => "\xC3\x9A", "\xDB" => "\xC3\x9B", "\xDC" => "\xC3\x9C", "\xDD" => "\xC3\x9D", "\xDE" => "\xC3\x9E", "\xDF" => "\xC3\x9F", "\xE0" => "\xC3\xA0", "\xE1" => "\xC3\xA1", "\xE2" => "\xC3\xA2", "\xE3" => "\xC3\xA3", "\xE4" => "\xC3\xA4", "\xE5" => "\xC3\xA5", "\xE6" => "\xC3\xA6", "\xE7" => "\xC3\xA7", "\xE8" => "\xC3\xA8", "\xE9" => "\xC3\xA9", "\xEA" => "\xC3\xAA", "\xEB" => "\xC3\xAB", "\xEC" => "\xC3\xAC", "\xED" => "\xC3\xAD", "\xEE" => "\xC3\xAE", "\xEF" => "\xC3\xAF", "\xF0" => "\xC3\xB0", "\xF1" => "\xC3\xB1", "\xF2" => "\xC3\xB2", "\xF3" => "\xC3\xB3", "\xF4" => "\xC3\xB4", "\xF5" => "\xC3\xB5", "\xF6" => "\xC3\xB6", "\xF7" => "\xC3\xB7", "\xF8" => "\xC3\xB8", "\xF9" => "\xC3\xB9", "\xFA" => "\xC3\xBA", "\xFB" => "\xC3\xBB", "\xFC" => "\xC3\xBC", "\xFD" => "\xC3\xBD", "\xFE" => "\xC3\xBE", "\xFF" => "\xC3\xBF");

        return strtr($string, $convert_table);
    }

    function change_encoding($data, $input, $output)
    {
        $input = SimplePie_Misc::encoding($input);
        $output = SimplePie_Misc::encoding($output);

        // We fail to fail on non US-ASCII bytes
        if ($input === 'US-ASCII') {
            static $non_ascii_octects = '';
            if (!$non_ascii_octects) {
                for ($i = 0x80; $i <= 0xFF; $i++) {
                    $non_ascii_octects .= chr($i);
                }
            }
            $data = substr($data, 0, strcspn($data, $non_ascii_octects));
        }

        if (function_exists('iconv') && ($return = @iconv($input, $output, $data))) {
            return $return;
        } elseif (function_exists('mb_convert_encoding') && ($return = @mb_convert_encoding($data, $output, $input))) {
            return $return;
        } elseif ($input == 'windows-1252' && $output == 'UTF-8') {
            return SimplePie_Misc::windows_1252_to_utf8($data);
        } elseif ($input == 'UTF-8' && $output == 'windows-1252') {
            return utf8_decode($data);
        }
        return $data;
    }

    function encoding($encoding)
    {
        // Character sets are case-insensitive (though we'll return them in the form given in their registration)
        switch (strtoupper($encoding)) {
            case 'ANSI_X3.110-1983':
            case 'CSA_T500-1983':
            case 'CSISO99NAPLPS':
            case 'ISO-IR-99':
            case 'NAPLPS':
                return 'ANSI_X3.110-1983';

            case 'ARABIC7':
            case 'ASMO_449':
            case 'CSISO89ASMO449':
            case 'ISO-IR-89':
            case 'ISO_9036':
                return 'ASMO_449';

            case 'ADOBE-STANDARD-ENCODING':
            case 'CSADOBESTANDARDENCODING':
                return 'Adobe-Standard-Encoding';

            case 'ADOBE-SYMBOL-ENCODING':
            case 'CSHPPSMATH':
                return 'Adobe-Symbol-Encoding';

            case 'AMI-1251':
            case 'AMI1251':
            case 'AMIGA-1251':
            case 'AMIGA1251':
                return 'Amiga-1251';

            case 'BOCU-1':
            case 'CSBOCU-1':
                return 'BOCU-1';

            case 'BRF':
            case 'CSBRF':
                return 'BRF';

            case 'BS_4730':
            case 'CSISO4UNITEDKINGDOM':
            case 'GB':
            case 'ISO-IR-4':
            case 'ISO646-GB':
            case 'UK':
                return 'BS_4730';

            case 'BS_VIEWDATA':
            case 'CSISO47BSVIEWDATA':
            case 'ISO-IR-47':
                return 'BS_viewdata';

            case 'BIG5':
            case 'CSBIG5':
                return 'Big5';

            case 'BIG5-HKSCS':
                return 'Big5-HKSCS';

            case 'CESU-8':
            case 'CSCESU-8':
                return 'CESU-8';

            case 'CA':
            case 'CSA7-1':
            case 'CSA_Z243.4-1985-1':
            case 'CSISO121CANADIAN1':
            case 'ISO-IR-121':
            case 'ISO646-CA':
                return 'CSA_Z243.4-1985-1';

            case 'CSA7-2':
            case 'CSA_Z243.4-1985-2':
            case 'CSISO122CANADIAN2':
            case 'ISO-IR-122':
            case 'ISO646-CA2':
                return 'CSA_Z243.4-1985-2';

            case 'CSA_Z243.4-1985-GR':
            case 'CSISO123CSAZ24341985GR':
            case 'ISO-IR-123':
                return 'CSA_Z243.4-1985-gr';

            case 'CSISO139CSN369103':
            case 'CSN_369103':
            case 'ISO-IR-139':
                return 'CSN_369103';

            case 'CSDECMCS':
            case 'DEC':
            case 'DEC-MCS':
                return 'DEC-MCS';

            case 'CSISO21GERMAN':
            case 'DE':
            case 'DIN_66003':
            case 'ISO-IR-21':
            case 'ISO646-DE':
                return 'DIN_66003';

            case 'CSISO646DANISH':
            case 'DK':
            case 'DS2089':
            case 'DS_2089':
            case 'ISO646-DK':
                return 'DS_2089';

            case 'CSIBMEBCDICATDE':
            case 'EBCDIC-AT-DE':
                return 'EBCDIC-AT-DE';

            case 'CSEBCDICATDEA':
            case 'EBCDIC-AT-DE-A':
                return 'EBCDIC-AT-DE-A';

            case 'CSEBCDICCAFR':
            case 'EBCDIC-CA-FR':
                return 'EBCDIC-CA-FR';

            case 'CSEBCDICDKNO':
            case 'EBCDIC-DK-NO':
                return 'EBCDIC-DK-NO';

            case 'CSEBCDICDKNOA':
            case 'EBCDIC-DK-NO-A':
                return 'EBCDIC-DK-NO-A';

            case 'CSEBCDICES':
            case 'EBCDIC-ES':
                return 'EBCDIC-ES';

            case 'CSEBCDICESA':
            case 'EBCDIC-ES-A':
                return 'EBCDIC-ES-A';

            case 'CSEBCDICESS':
            case 'EBCDIC-ES-S':
                return 'EBCDIC-ES-S';

            case 'CSEBCDICFISE':
            case 'EBCDIC-FI-SE':
                return 'EBCDIC-FI-SE';

            case 'CSEBCDICFISEA':
            case 'EBCDIC-FI-SE-A':
                return 'EBCDIC-FI-SE-A';

            case 'CSEBCDICFR':
            case 'EBCDIC-FR':
                return 'EBCDIC-FR';

            case 'CSEBCDICIT':
            case 'EBCDIC-IT':
                return 'EBCDIC-IT';

            case 'CSEBCDICPT':
            case 'EBCDIC-PT':
                return 'EBCDIC-PT';

            case 'CSEBCDICUK':
            case 'EBCDIC-UK':
                return 'EBCDIC-UK';

            case 'CSEBCDICUS':
            case 'EBCDIC-US':
                return 'EBCDIC-US';

            case 'CSISO111ECMACYRILLIC':
            case 'ECMA-CYRILLIC':
            case 'ISO-IR-111':
            case 'KOI8-E':
                return 'ECMA-cyrillic';

            case 'CSISO17SPANISH':
            case 'ES':
            case 'ISO-IR-17':
            case 'ISO646-ES':
                return 'ES';

            case 'CSISO85SPANISH2':
            case 'ES2':
            case 'ISO-IR-85':
            case 'ISO646-ES2':
                return 'ES2';

            case 'CSEUCPKDFMTJAPANESE':
            case 'EUC-JP':
            case 'EXTENDED_UNIX_CODE_PACKED_FORMAT_FOR_JAPANESE':
                return 'EUC-JP';

            case 'CSEUCKR':
            case 'EUC-KR':
                return 'EUC-KR';

            case 'CSEUCFIXWIDJAPANESE':
            case 'EXTENDED_UNIX_CODE_FIXED_WIDTH_FOR_JAPANESE':
                return 'Extended_UNIX_Code_Fixed_Width_for_Japanese';

            case 'GB18030':
                return 'GB18030';

            case 'CSGB2312':
            case 'GB2312':
                return 'GB2312';

            case 'CP936':
            case 'GBK':
            case 'MS936':
            case 'WINDOWS-936':
                return 'GBK';

            case 'CN':
            case 'CSISO57GB1988':
            case 'GB_1988-80':
            case 'ISO-IR-57':
            case 'ISO646-CN':
                return 'GB_1988-80';

            case 'CHINESE':
            case 'CSISO58GB231280':
            case 'GB_2312-80':
            case 'ISO-IR-58':
                return 'GB_2312-80';

            case 'CSISO153GOST1976874':
            case 'GOST_19768-74':
            case 'ISO-IR-153':
            case 'ST_SEV_358-88':
                return 'GOST_19768-74';

            case 'CSHPDESKTOP':
            case 'HP-DESKTOP':
                return 'HP-DeskTop';

            case 'CSHPLEGAL':
            case 'HP-LEGAL':
                return 'HP-Legal';

            case 'CSHPMATH8':
            case 'HP-MATH8':
                return 'HP-Math8';

            case 'CSHPPIFONT':
            case 'HP-PI-FONT':
                return 'HP-Pi-font';

            case 'HZ-GB-2312':
                return 'HZ-GB-2312';

            case 'CSIBMSYMBOLS':
            case 'IBM-SYMBOLS':
                return 'IBM-Symbols';

            case 'CSIBMTHAI':
            case 'IBM-THAI':
                return 'IBM-Thai';

            case 'CCSID00858':
            case 'CP00858':
            case 'IBM00858':
            case 'PC-MULTILINGUAL-850+EURO':
                return 'IBM00858';

            case 'CCSID00924':
            case 'CP00924':
            case 'EBCDIC-LATIN9--EURO':
            case 'IBM00924':
                return 'IBM00924';

            case 'CCSID01140':
            case 'CP01140':
            case 'EBCDIC-US-37+EURO':
            case 'IBM01140':
                return 'IBM01140';

            case 'CCSID01141':
            case 'CP01141':
            case 'EBCDIC-DE-273+EURO':
            case 'IBM01141':
                return 'IBM01141';

            case 'CCSID01142':
            case 'CP01142':
            case 'EBCDIC-DK-277+EURO':
            case 'EBCDIC-NO-277+EURO':
            case 'IBM01142':
                return 'IBM01142';

            case 'CCSID01143':
            case 'CP01143':
            case 'EBCDIC-FI-278+EURO':
            case 'EBCDIC-SE-278+EURO':
            case 'IBM01143':
                return 'IBM01143';

            case 'CCSID01144':
            case 'CP01144':
            case 'EBCDIC-IT-280+EURO':
            case 'IBM01144':
                return 'IBM01144';

            case 'CCSID01145':
            case 'CP01145':
            case 'EBCDIC-ES-284+EURO':
            case 'IBM01145':
                return 'IBM01145';

            case 'CCSID01146':
            case 'CP01146':
            case 'EBCDIC-GB-285+EURO':
            case 'IBM01146':
                return 'IBM01146';

            case 'CCSID01147':
            case 'CP01147':
            case 'EBCDIC-FR-297+EURO':
            case 'IBM01147':
                return 'IBM01147';

            case 'CCSID01148':
            case 'CP01148':
            case 'EBCDIC-INTERNATIONAL-500+EURO':
            case 'IBM01148':
                return 'IBM01148';

            case 'CCSID01149':
            case 'CP01149':
            case 'EBCDIC-IS-871+EURO':
            case 'IBM01149':
                return 'IBM01149';

            case 'CP037':
            case 'CSIBM037':
            case 'EBCDIC-CP-CA':
            case 'EBCDIC-CP-NL':
            case 'EBCDIC-CP-US':
            case 'EBCDIC-CP-WT':
            case 'IBM037':
                return 'IBM037';

            case 'CP038':
            case 'CSIBM038':
            case 'EBCDIC-INT':
            case 'IBM038':
                return 'IBM038';

            case 'CP1026':
            case 'CSIBM1026':
            case 'IBM1026':
                return 'IBM1026';

            case 'IBM-1047':
            case 'IBM1047':
                return 'IBM1047';

            case 'CP273':
            case 'CSIBM273':
            case 'IBM273':
                return 'IBM273';

            case 'CP274':
            case 'CSIBM274':
            case 'EBCDIC-BE':
            case 'IBM274':
                return 'IBM274';

            case 'CP275':
            case 'CSIBM275':
            case 'EBCDIC-BR':
            case 'IBM275':
                return 'IBM275';

            case 'CSIBM277':
            case 'EBCDIC-CP-DK':
            case 'EBCDIC-CP-NO':
            case 'IBM277':
                return 'IBM277';

            case 'CP278':
            case 'CSIBM278':
            case 'EBCDIC-CP-FI':
            case 'EBCDIC-CP-SE':
            case 'IBM278':
                return 'IBM278';

            case 'CP280':
            case 'CSIBM280':
            case 'EBCDIC-CP-IT':
            case 'IBM280':
                return 'IBM280';

            case 'CP281':
            case 'CSIBM281':
            case 'EBCDIC-JP-E':
            case 'IBM281':
                return 'IBM281';

            case 'CP284':
            case 'CSIBM284':
            case 'EBCDIC-CP-ES':
            case 'IBM284':
                return 'IBM284';

            case 'CP285':
            case 'CSIBM285':
            case 'EBCDIC-CP-GB':
            case 'IBM285':
                return 'IBM285';

            case 'CP290':
            case 'CSIBM290':
            case 'EBCDIC-JP-KANA':
            case 'IBM290':
                return 'IBM290';

            case 'CP297':
            case 'CSIBM297':
            case 'EBCDIC-CP-FR':
            case 'IBM297':
                return 'IBM297';

            case 'CP420':
            case 'CSIBM420':
            case 'EBCDIC-CP-AR1':
            case 'IBM420':
                return 'IBM420';

            case 'CP423':
            case 'CSIBM423':
            case 'EBCDIC-CP-GR':
            case 'IBM423':
                return 'IBM423';

            case 'CP424':
            case 'CSIBM424':
            case 'EBCDIC-CP-HE':
            case 'IBM424':
                return 'IBM424';

            case '437':
            case 'CP437':
            case 'CSPC8CODEPAGE437':
            case 'IBM437':
                return 'IBM437';

            case 'CP500':
            case 'CSIBM500':
            case 'EBCDIC-CP-BE':
            case 'EBCDIC-CP-CH':
            case 'IBM500':
                return 'IBM500';

            case 'CP775':
            case 'CSPC775BALTIC':
            case 'IBM775':
                return 'IBM775';

            case '850':
            case 'CP850':
            case 'CSPC850MULTILINGUAL':
            case 'IBM850':
                return 'IBM850';

            case '851':
            case 'CP851':
            case 'CSIBM851':
            case 'IBM851':
                return 'IBM851';

            case '852':
            case 'CP852':
            case 'CSPCP852':
            case 'IBM852':
                return 'IBM852';

            case '855':
            case 'CP855':
            case 'CSIBM855':
            case 'IBM855':
                return 'IBM855';

            case '857':
            case 'CP857':
            case 'CSIBM857':
            case 'IBM857':
                return 'IBM857';

            case '860':
            case 'CP860':
            case 'CSIBM860':
            case 'IBM860':
                return 'IBM860';

            case '861':
            case 'CP-IS':
            case 'CP861':
            case 'CSIBM861':
            case 'IBM861':
                return 'IBM861';

            case '862':
            case 'CP862':
            case 'CSPC862LATINHEBREW':
            case 'IBM862':
                return 'IBM862';

            case '863':
            case 'CP863':
            case 'CSIBM863':
            case 'IBM863':
                return 'IBM863';

            case 'CP864':
            case 'CSIBM864':
            case 'IBM864':
                return 'IBM864';

            case '865':
            case 'CP865':
            case 'CSIBM865':
            case 'IBM865':
                return 'IBM865';

            case '866':
            case 'CP866':
            case 'CSIBM866':
            case 'IBM866':
                return 'IBM866';

            case 'CP-AR':
            case 'CP868':
            case 'CSIBM868':
            case 'IBM868':
                return 'IBM868';

            case '869':
            case 'CP-GR':
            case 'CP869':
            case 'CSIBM869':
            case 'IBM869':
                return 'IBM869';

            case 'CP870':
            case 'CSIBM870':
            case 'EBCDIC-CP-ROECE':
            case 'EBCDIC-CP-YU':
            case 'IBM870':
                return 'IBM870';

            case 'CP871':
            case 'CSIBM871':
            case 'EBCDIC-CP-IS':
            case 'IBM871':
                return 'IBM871';

            case 'CP880':
            case 'CSIBM880':
            case 'EBCDIC-CYRILLIC':
            case 'IBM880':
                return 'IBM880';

            case 'CP891':
            case 'CSIBM891':
            case 'IBM891':
                return 'IBM891';

            case 'CP903':
            case 'CSIBM903':
            case 'IBM903':
                return 'IBM903';

            case '904':
            case 'CP904':
            case 'CSIBBM904':
            case 'IBM904':
                return 'IBM904';

            case 'CP905':
            case 'CSIBM905':
            case 'EBCDIC-CP-TR':
            case 'IBM905':
                return 'IBM905';

            case 'CP918':
            case 'CSIBM918':
            case 'EBCDIC-CP-AR2':
            case 'IBM918':
                return 'IBM918';

            case 'CSISO143IECP271':
            case 'IEC_P27-1':
            case 'ISO-IR-143':
                return 'IEC_P27-1';

            case 'CSISO49INIS':
            case 'INIS':
            case 'ISO-IR-49':
                return 'INIS';

            case 'CSISO50INIS8':
            case 'INIS-8':
            case 'ISO-IR-50':
                return 'INIS-8';

            case 'CSISO51INISCYRILLIC':
            case 'INIS-CYRILLIC':
            case 'ISO-IR-51':
                return 'INIS-cyrillic';

            case 'CSINVARIANT':
            case 'INVARIANT':
                return 'INVARIANT';

            case 'ISO-10646-J-1':
                return 'ISO-10646-J-1';

            case 'CSUNICODE':
            case 'ISO-10646-UCS-2':
                return 'ISO-10646-UCS-2';

            case 'CSUCS4':
            case 'ISO-10646-UCS-4':
                return 'ISO-10646-UCS-4';

            case 'CSUNICODEASCII':
            case 'ISO-10646-UCS-BASIC':
                return 'ISO-10646-UCS-Basic';

            case 'CSISO10646UTF1':
            case 'ISO-10646-UTF-1':
                return 'ISO-10646-UTF-1';

            case 'CSUNICODELATIN1':
            case 'ISO-10646':
            case 'ISO-10646-UNICODE-LATIN1':
                return 'ISO-10646-Unicode-Latin1';

            case 'CSISO115481':
            case 'ISO-11548-1':
            case 'ISO_11548-1':
            case 'ISO_TR_11548-1':
                return 'ISO-11548-1';

            case 'ISO-2022-CN':
                return 'ISO-2022-CN';

            case 'ISO-2022-CN-EXT':
                return 'ISO-2022-CN-EXT';

            case 'CSISO2022JP':
            case 'ISO-2022-JP':
                return 'ISO-2022-JP';

            case 'CSISO2022JP2':
            case 'ISO-2022-JP-2':
                return 'ISO-2022-JP-2';

            case 'CSISO2022KR':
            case 'ISO-2022-KR':
                return 'ISO-2022-KR';

            case 'CSWINDOWS30LATIN1':
            case 'ISO-8859-1-WINDOWS-3.0-LATIN-1':
                return 'ISO-8859-1-Windows-3.0-Latin-1';

            case 'CSWINDOWS31LATIN1':
            case 'ISO-8859-1-WINDOWS-3.1-LATIN-1':
                return 'ISO-8859-1-Windows-3.1-Latin-1';

            case 'CSISOLATIN6':
            case 'ISO-8859-10':
            case 'ISO-IR-157':
            case 'ISO_8859-10:1992':
            case 'L6':
            case 'LATIN6':
                return 'ISO-8859-10';

            case 'ISO-8859-13':
                return 'ISO-8859-13';

            case 'ISO-8859-14':
            case 'ISO-CELTIC':
            case 'ISO-IR-199':
            case 'ISO_8859-14':
            case 'ISO_8859-14:1998':
            case 'L8':
            case 'LATIN8':
                return 'ISO-8859-14';

            case 'ISO-8859-15':
            case 'ISO_8859-15':
            case 'LATIN-9':
                return 'ISO-8859-15';

            case 'ISO-8859-16':
            case 'ISO-IR-226':
            case 'ISO_8859-16':
            case 'ISO_8859-16:2001':
            case 'L10':
            case 'LATIN10':
                return 'ISO-8859-16';

            case 'CSISOLATIN2':
            case 'ISO-8859-2':
            case 'ISO-IR-101':
            case 'ISO_8859-2':
            case 'ISO_8859-2:1987':
            case 'L2':
            case 'LATIN2':
                return 'ISO-8859-2';

            case 'CSWINDOWS31LATIN2':
            case 'ISO-8859-2-WINDOWS-LATIN-2':
                return 'ISO-8859-2-Windows-Latin-2';

            case 'CSISOLATIN3':
            case 'ISO-8859-3':
            case 'ISO-IR-109':
            case 'ISO_8859-3':
            case 'ISO_8859-3:1988':
            case 'L3':
            case 'LATIN3':
                return 'ISO-8859-3';

            case 'CSISOLATIN4':
            case 'ISO-8859-4':
            case 'ISO-IR-110':
            case 'ISO_8859-4':
            case 'ISO_8859-4:1988':
            case 'L4':
            case 'LATIN4':
                return 'ISO-8859-4';

            case 'CSISOLATINCYRILLIC':
            case 'CYRILLIC':
            case 'ISO-8859-5':
            case 'ISO-IR-144':
            case 'ISO_8859-5':
            case 'ISO_8859-5:1988':
                return 'ISO-8859-5';

            case 'ARABIC':
            case 'ASMO-708':
            case 'CSISOLATINARABIC':
            case 'ECMA-114':
            case 'ISO-8859-6':
            case 'ISO-IR-127':
            case 'ISO_8859-6':
            case 'ISO_8859-6:1987':
                return 'ISO-8859-6';

            case 'CSISO88596E':
            case 'ISO-8859-6-E':
            case 'ISO_8859-6-E':
                return 'ISO-8859-6-E';

            case 'CSISO88596I':
            case 'ISO-8859-6-I':
            case 'ISO_8859-6-I':
                return 'ISO-8859-6-I';

            case 'CSISOLATINGREEK':
            case 'ECMA-118':
            case 'ELOT_928':
            case 'GREEK':
            case 'GREEK8':
            case 'ISO-8859-7':
            case 'ISO-IR-126':
            case 'ISO_8859-7':
            case 'ISO_8859-7:1987':
                return 'ISO-8859-7';

            case 'CSISOLATINHEBREW':
            case 'HEBREW':
            case 'ISO-8859-8':
            case 'ISO-IR-138':
            case 'ISO_8859-8':
            case 'ISO_8859-8:1988':
                return 'ISO-8859-8';

            case 'CSISO88598E':
            case 'ISO-8859-8-E':
            case 'ISO_8859-8-E':
                return 'ISO-8859-8-E';

            case 'CSISO88598I':
            case 'ISO-8859-8-I':
            case 'ISO_8859-8-I':
                return 'ISO-8859-8-I';

            case 'CSISOLATIN5':
            case 'ISO-8859-9':
            case 'ISO-IR-148':
            case 'ISO_8859-9':
            case 'ISO_8859-9:1989':
            case 'L5':
            case 'LATIN5':
                return 'ISO-8859-9';

            case 'CSWINDOWS31LATIN5':
            case 'ISO-8859-9-WINDOWS-LATIN-5':
                return 'ISO-8859-9-Windows-Latin-5';

            case 'CSUNICODEIBM1261':
            case 'ISO-UNICODE-IBM-1261':
                return 'ISO-Unicode-IBM-1261';

            case 'CSUNICODEIBM1264':
            case 'ISO-UNICODE-IBM-1264':
                return 'ISO-Unicode-IBM-1264';

            case 'CSUNICODEIBM1265':
            case 'ISO-UNICODE-IBM-1265':
                return 'ISO-Unicode-IBM-1265';

            case 'CSUNICODEIBM1268':
            case 'ISO-UNICODE-IBM-1268':
                return 'ISO-Unicode-IBM-1268';

            case 'CSUNICODEIBM1276':
            case 'ISO-UNICODE-IBM-1276':
                return 'ISO-Unicode-IBM-1276';

            case 'CSISO10367BOX':
            case 'ISO-IR-155':
            case 'ISO_10367-BOX':
                return 'ISO_10367-box';

            case 'CSISO2033':
            case 'E13B':
            case 'ISO-IR-98':
            case 'ISO_2033-1983':
                return 'ISO_2033-1983';

            case 'CSISO5427CYRILLIC':
            case 'ISO-IR-37':
            case 'ISO_5427':
                return 'ISO_5427';

            case 'ISO-IR-54':
            case 'ISO5427CYRILLIC1981':
            case 'ISO_5427:1981':
                return 'ISO_5427:1981';

            case 'CSISO5428GREEK':
            case 'ISO-IR-55':
            case 'ISO_5428:1980':
                return 'ISO_5428:1980';

            case 'CSISO646BASIC1983':
            case 'ISO_646.BASIC:1983':
            case 'REF':
                return 'ISO_646.basic:1983';

            case 'CSISO2INTLREFVERSION':
            case 'IRV':
            case 'ISO-IR-2':
            case 'ISO_646.IRV:1983':
                return 'ISO_646.irv:1983';

            case 'CSISO6937ADD':
            case 'ISO-IR-152':
            case 'ISO_6937-2-25':
                return 'ISO_6937-2-25';

            case 'CSISOTEXTCOMM':
            case 'ISO-IR-142':
            case 'ISO_6937-2-ADD':
                return 'ISO_6937-2-add';

            case 'CSISO8859SUPP':
            case 'ISO-IR-154':
            case 'ISO_8859-SUPP':
            case 'LATIN1-2-5':
                return 'ISO_8859-supp';

            case 'CSISO15ITALIAN':
            case 'ISO-IR-15':
            case 'ISO646-IT':
            case 'IT':
                return 'IT';

            case 'CSISO13JISC6220JP':
            case 'ISO-IR-13':
            case 'JIS_C6220-1969':
            case 'JIS_C6220-1969-JP':
            case 'KATAKANA':
            case 'X0201-7':
                return 'JIS_C6220-1969-jp';

            case 'CSISO14JISC6220RO':
            case 'ISO-IR-14':
            case 'ISO646-JP':
            case 'JIS_C6220-1969-RO':
            case 'JP':
                return 'JIS_C6220-1969-ro';

            case 'CSISO42JISC62261978':
            case 'ISO-IR-42':
            case 'JIS_C6226-1978':
                return 'JIS_C6226-1978';

            case 'CSISO87JISX0208':
            case 'ISO-IR-87':
            case 'JIS_C6226-1983':
            case 'JIS_X0208-1983':
            case 'X0208':
                return 'JIS_C6226-1983';

            case 'CSISO91JISC62291984A':
            case 'ISO-IR-91':
            case 'JIS_C6229-1984-A':
            case 'JP-OCR-A':
                return 'JIS_C6229-1984-a';

            case 'CSISO92JISC62991984B':
            case 'ISO-IR-92':
            case 'ISO646-JP-OCR-B':
            case 'JIS_C6229-1984-B':
            case 'JP-OCR-B':
                return 'JIS_C6229-1984-b';

            case 'CSISO93JIS62291984BADD':
            case 'ISO-IR-93':
            case 'JIS_C6229-1984-B-ADD':
            case 'JP-OCR-B-ADD':
                return 'JIS_C6229-1984-b-add';

            case 'CSISO94JIS62291984HAND':
            case 'ISO-IR-94':
            case 'JIS_C6229-1984-HAND':
            case 'JP-OCR-HAND':
                return 'JIS_C6229-1984-hand';

            case 'CSISO95JIS62291984HANDADD':
            case 'ISO-IR-95':
            case 'JIS_C6229-1984-HAND-ADD':
            case 'JP-OCR-HAND-ADD':
                return 'JIS_C6229-1984-hand-add';

            case 'CSISO96JISC62291984KANA':
            case 'ISO-IR-96':
            case 'JIS_C6229-1984-KANA':
                return 'JIS_C6229-1984-kana';

            case 'CSJISENCODING':
            case 'JIS_ENCODING':
                return 'JIS_Encoding';

            case 'CSHALFWIDTHKATAKANA':
            case 'JIS_X0201':
            case 'X0201':
                return 'JIS_X0201';

            case 'CSISO159JISX02121990':
            case 'ISO-IR-159':
            case 'JIS_X0212-1990':
            case 'X0212':
                return 'JIS_X0212-1990';

            case 'CSISO141JUSIB1002':
            case 'ISO-IR-141':
            case 'ISO646-YU':
            case 'JS':
            case 'JUS_I.B1.002':
            case 'YU':
                return 'JUS_I.B1.002';

            case 'CSISO147MACEDONIAN':
            case 'ISO-IR-147':
            case 'JUS_I.B1.003-MAC':
            case 'MACEDONIAN':
                return 'JUS_I.B1.003-mac';

            case 'CSISO146SERBIAN':
            case 'ISO-IR-146':
            case 'JUS_I.B1.003-SERB':
            case 'SERBIAN':
                return 'JUS_I.B1.003-serb';

            case 'KOI7-SWITCHED':
                return 'KOI7-switched';

            case 'CSKOI8R':
            case 'KOI8-R':
                return 'KOI8-R';

            case 'KOI8-U':
                return 'KOI8-U';

            case 'CSKSC5636':
            case 'ISO646-KR':
            case 'KSC5636':
                return 'KSC5636';

            case 'CSKSC56011987':
            case 'ISO-IR-149':
            case 'KOREAN':
            case 'KSC_5601':
            case 'KS_C_5601-1987':
            case 'KS_C_5601-1989':
                return 'KS_C_5601-1987';

            case 'CSKZ1048':
            case 'KZ-1048':
            case 'RK1048':
            case 'STRK1048-2002':
                return 'KZ-1048';

            case 'CSISO27LATINGREEK1':
            case 'ISO-IR-27':
            case 'LATIN-GREEK-1':
                return 'Latin-greek-1';

            case 'CSMNEM':
            case 'MNEM':
                return 'MNEM';

            case 'CSMNEMONIC':
            case 'MNEMONIC':
                return 'MNEMONIC';

            case 'CSISO86HUNGARIAN':
            case 'HU':
            case 'ISO-IR-86':
            case 'ISO646-HU':
            case 'MSZ_7795.3':
                return 'MSZ_7795.3';

            case 'CSMICROSOFTPUBLISHING':
            case 'MICROSOFT-PUBLISHING':
                return 'Microsoft-Publishing';

            case 'CSNATSDANO':
            case 'ISO-IR-9-1':
            case 'NATS-DANO':
                return 'NATS-DANO';

            case 'CSNATSDANOADD':
            case 'ISO-IR-9-2':
            case 'NATS-DANO-ADD':
                return 'NATS-DANO-ADD';

            case 'CSNATSSEFI':
            case 'ISO-IR-8-1':
            case 'NATS-SEFI':
                return 'NATS-SEFI';

            case 'CSNATSSEFIADD':
            case 'ISO-IR-8-2':
            case 'NATS-SEFI-ADD':
                return 'NATS-SEFI-ADD';

            case 'CSISO151CUBA':
            case 'CUBA':
            case 'ISO-IR-151':
            case 'ISO646-CU':
            case 'NC_NC00-10:81':
                return 'NC_NC00-10:81';

            case 'CSISO69FRENCH':
            case 'FR':
            case 'ISO-IR-69':
            case 'ISO646-FR':
            case 'NF_Z_62-010':
                return 'NF_Z_62-010';

            case 'CSISO25FRENCH':
            case 'ISO-IR-25':
            case 'ISO646-FR1':
            case 'NF_Z_62-010_(1973)':
                return 'NF_Z_62-010_(1973)';

            case 'CSISO60DANISHNORWEGIAN':
            case 'CSISO60NORWEGIAN1':
            case 'ISO-IR-60':
            case 'ISO646-NO':
            case 'NO':
            case 'NS_4551-1':
                return 'NS_4551-1';

            case 'CSISO61NORWEGIAN2':
            case 'ISO-IR-61':
            case 'ISO646-NO2':
            case 'NO2':
            case 'NS_4551-2':
                return 'NS_4551-2';

            case 'OSD_EBCDIC_DF03_IRV':
                return 'OSD_EBCDIC_DF03_IRV';

            case 'OSD_EBCDIC_DF04_1':
                return 'OSD_EBCDIC_DF04_1';

            case 'OSD_EBCDIC_DF04_15':
                return 'OSD_EBCDIC_DF04_15';

            case 'CSPC8DANISHNORWEGIAN':
            case 'PC8-DANISH-NORWEGIAN':
                return 'PC8-Danish-Norwegian';

            case 'CSPC8TURKISH':
            case 'PC8-TURKISH':
                return 'PC8-Turkish';

            case 'CSISO16PORTUGUESE':
            case 'ISO-IR-16':
            case 'ISO646-PT':
            case 'PT':
                return 'PT';

            case 'CSISO84PORTUGUESE2':
            case 'ISO-IR-84':
            case 'ISO646-PT2':
            case 'PT2':
                return 'PT2';

            case 'CP154':
            case 'CSPTCP154':
            case 'CYRILLIC-ASIAN':
            case 'PT154':
            case 'PTCP154':
                return 'PTCP154';

            case 'SCSU':
                return 'SCSU';

            case 'CSISO10SWEDISH':
            case 'FI':
            case 'ISO-IR-10':
            case 'ISO646-FI':
            case 'ISO646-SE':
            case 'SE':
            case 'SEN_850200_B':
                return 'SEN_850200_B';

            case 'CSISO11SWEDISHFORNAMES':
            case 'ISO-IR-11':
            case 'ISO646-SE2':
            case 'SE2':
            case 'SEN_850200_C':
                return 'SEN_850200_C';

            case 'CSSHIFTJIS':
            case 'MS_KANJI':
            case 'SHIFT_JIS':
                return 'Shift_JIS';

            case 'CSISO128T101G2':
            case 'ISO-IR-128':
            case 'T.101-G2':
                return 'T.101-G2';

            case 'CSISO102T617BIT':
            case 'ISO-IR-102':
            case 'T.61-7BIT':
                return 'T.61-7bit';

            case 'CSISO103T618BIT':
            case 'ISO-IR-103':
            case 'T.61':
            case 'T.61-8BIT':
                return 'T.61-8bit';

            case 'CSTSCII':
            case 'TSCII':
                return 'TSCII';

            case 'CSUNICODE11':
            case 'UNICODE-1-1':
                return 'UNICODE-1-1';

            case 'CSUNICODE11UTF7':
            case 'UNICODE-1-1-UTF-7':
                return 'UNICODE-1-1-UTF-7';

            case 'CSUNKNOWN8BIT':
            case 'UNKNOWN-8BIT':
                return 'UNKNOWN-8BIT';

            case 'ANSI':
            case 'ANSI_X3.4-1968':
            case 'ANSI_X3.4-1986':
            case 'ASCII':
            case 'CP367':
            case 'CSASCII':
            case 'IBM367':
            case 'ISO-IR-6':
            case 'ISO646-US':
            case 'ISO_646.IRV:1991':
            case 'US':
            case 'US-ASCII':
                return 'US-ASCII';

            case 'UTF-16':
                return 'UTF-16';

            case 'UTF-16BE':
                return 'UTF-16BE';

            case 'UTF-16LE':
                return 'UTF-16LE';

            case 'UTF-32':
                return 'UTF-32';

            case 'UTF-32BE':
                return 'UTF-32BE';

            case 'UTF-32LE':
                return 'UTF-32LE';

            case 'UTF-7':
                return 'UTF-7';

            case 'UTF-8':
                return 'UTF-8';

            case 'CSVIQR':
            case 'VIQR':
                return 'VIQR';

            case 'CSVISCII':
            case 'VISCII':
                return 'VISCII';

            case 'CSVENTURAINTERNATIONAL':
            case 'VENTURA-INTERNATIONAL':
                return 'Ventura-International';

            case 'CSVENTURAMATH':
            case 'VENTURA-MATH':
                return 'Ventura-Math';

            case 'CSVENTURAUS':
            case 'VENTURA-US':
                return 'Ventura-US';

            case 'CSWINDOWS31J':
            case 'WINDOWS-31J':
                return 'Windows-31J';

            case 'CSDKUS':
            case 'DK-US':
                return 'dk-us';

            case 'CSISO150':
            case 'CSISO150GREEKCCITT':
            case 'GREEK-CCITT':
            case 'ISO-IR-150':
                return 'greek-ccitt';

            case 'CSISO88GREEK7':
            case 'GREEK7':
            case 'ISO-IR-88':
                return 'greek7';

            case 'CSISO18GREEK7OLD':
            case 'GREEK7-OLD':
            case 'ISO-IR-18':
                return 'greek7-old';

            case 'CSHPROMAN8':
            case 'HP-ROMAN8':
            case 'R8':
            case 'ROMAN8':
                return 'hp-roman8';

            case 'CSISO90':
            case 'ISO-IR-90':
                return 'iso-ir-90';

            case 'CSISO19LATINGREEK':
            case 'ISO-IR-19':
            case 'LATIN-GREEK':
                return 'latin-greek';

            case 'CSISO158LAP':
            case 'ISO-IR-158':
            case 'LAP':
            case 'LATIN-LAP':
                return 'latin-lap';

            case 'CSMACINTOSH':
            case 'MAC':
            case 'MACINTOSH':
                return 'macintosh';

            case 'CSUSDK':
            case 'US-DK':
                return 'us-dk';

            case 'CSISO70VIDEOTEXSUPP1':
            case 'ISO-IR-70':
            case 'VIDEOTEX-SUPPL':
                return 'videotex-suppl';

            case 'WINDOWS-1250':
                return 'windows-1250';

            case 'WINDOWS-1251':
                return 'windows-1251';

            case 'CP819':
            case 'CSISOLATIN1':
            case 'IBM819':
            case 'ISO-8859-1':
            case 'ISO-IR-100':
            case 'ISO_8859-1':
            case 'ISO_8859-1:1987':
            case 'L1':
            case 'LATIN1':
            case 'WINDOWS-1252':
                return 'windows-1252';

            case 'WINDOWS-1253':
                return 'windows-1253';

            case 'WINDOWS-1254':
                return 'windows-1254';

            case 'WINDOWS-1255':
                return 'windows-1255';

            case 'WINDOWS-1256':
                return 'windows-1256';

            case 'WINDOWS-1257':
                return 'windows-1257';

            case 'WINDOWS-1258':
                return 'windows-1258';

            default:
                return $encoding;
        }
    }

    function get_curl_version()
    {
        if (is_array($curl = curl_version())) {
            $curl = $curl['version'];
        } elseif (substr($curl, 0, 5) == 'curl/') {
            $curl = substr($curl, 5, strcspn($curl, "\x09\x0A\x0B\x0C\x0D", 5));
        } elseif (substr($curl, 0, 8) == 'libcurl/') {
            $curl = substr($curl, 8, strcspn($curl, "\x09\x0A\x0B\x0C\x0D", 8));
        } else {
            $curl = 0;
        }
        return $curl;
    }

    function is_subclass_of($class1, $class2)
    {
        if (func_num_args() != 2) {
            trigger_error('Wrong parameter count for SimplePie_Misc::is_subclass_of()', E_USER_WARNING);
        } elseif (version_compare(PHP_VERSION, '5.0.3', '>=') || is_object($class1)) {
            return is_subclass_of($class1, $class2);
        } elseif (is_string($class1) && is_string($class2)) {
            if (class_exists($class1)) {
                if (class_exists($class2)) {
                    $class2 = strtolower($class2);
                    while ($class1 = strtolower(get_parent_class($class1))) {
                        if ($class1 == $class2) {
                            return true;
                        }
                    }
                }
            } else {
                trigger_error('Unknown class passed as parameter', E_USER_WARNNG);
            }
        }
        return false;
    }

    /**
     * Strip HTML comments
     *
     * @access public
     * @param string $data Data to strip comments from
     * @return string Comment stripped string
     */
    function strip_comments($data)
    {
        $output = '';
        while (($start = strpos($data, '<!--')) !== false) {
            $output .= substr($data, 0, $start);
            if (($end = strpos($data, '-->', $start)) !== false) {
                $data = substr_replace($data, '', 0, $end + 3);
            } else {
                $data = '';
            }
        }
        return $output . $data;
    }

    function parse_date($dt)
    {
        $parser = SimplePie_Parse_Date::get();
        return $parser->parse($dt);
    }

    /**
     * Decode HTML entities
     *
     * @static
     * @access public
     * @param string $data Input data
     * @return string Output data
     */
    function entities_decode($data)
    {
        $decoder = new SimplePie_Decode_HTML_Entities($data);
        return $decoder->parse();
    }

    /**
     * Remove RFC822 comments
     *
     * @access public
     * @param string $data Data to strip comments from
     * @return string Comment stripped string
     */
    function uncomment_rfc822($string)
    {
        $string = (string)$string;
        $position = 0;
        $length = strlen($string);
        $depth = 0;

        $output = '';

        while ($position < $length && ($pos = strpos($string, '(', $position)) !== false) {
            $output .= substr($string, $position, $pos - $position);
            $position = $pos + 1;
            if ($string[$pos - 1] !== '\\') {
                $depth++;
                while ($depth && $position < $length) {
                    $position += strcspn($string, '()', $position);
                    if ($string[$position - 1] === '\\') {
                        $position++;
                        continue;
                    } elseif (isset($string[$position])) {
                        switch ($string[$position]) {
                            case '(':
                                $depth++;
                                break;

                            case ')':
                                $depth--;
                                break;
                        }
                        $position++;
                    } else {
                        break;
                    }
                }
            } else {
                $output .= '(';
            }
        }
        $output .= substr($string, $position);

        return $output;
    }

    function parse_mime($mime)
    {
        if (($pos = strpos($mime, ';')) === false) {
            return trim($mime);
        } else {
            return trim(substr($mime, 0, $pos));
        }
    }

    function htmlspecialchars_decode($string, $quote_style)
    {
        if (function_exists('htmlspecialchars_decode')) {
            return htmlspecialchars_decode($string, $quote_style);
        } else {
            return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
        }
    }

    function atom_03_construct_type($attribs)
    {
        if (isset($attribs['']['mode']) && strtolower(trim($attribs['']['mode']) == 'base64')) {
            $mode = SIMPLEPIE_CONSTRUCT_BASE64;
        } else {
            $mode = SIMPLEPIE_CONSTRUCT_NONE;
        }
        if (isset($attribs['']['type'])) {
            switch (strtolower(trim($attribs['']['type']))) {
                case 'text':
                case 'text/plain':
                    return SIMPLEPIE_CONSTRUCT_TEXT | $mode;

                case 'html':
                case 'text/html':
                    return SIMPLEPIE_CONSTRUCT_HTML | $mode;

                case 'xhtml':
                case 'application/xhtml+xml':
                    return SIMPLEPIE_CONSTRUCT_XHTML | $mode;

                default:
                    return SIMPLEPIE_CONSTRUCT_NONE | $mode;
            }
        } else {
            return SIMPLEPIE_CONSTRUCT_TEXT | $mode;
        }
    }

    function atom_10_construct_type($attribs)
    {
        if (isset($attribs['']['type'])) {
            switch (strtolower(trim($attribs['']['type']))) {
                case 'text':
                    return SIMPLEPIE_CONSTRUCT_TEXT;

                case 'html':
                    return SIMPLEPIE_CONSTRUCT_HTML;

                case 'xhtml':
                    return SIMPLEPIE_CONSTRUCT_XHTML;

                default:
                    return SIMPLEPIE_CONSTRUCT_NONE;
            }
        }
        return SIMPLEPIE_CONSTRUCT_TEXT;
    }

    function atom_10_content_construct_type($attribs)
    {
        if (isset($attribs['']['type'])) {
            $type = strtolower(trim($attribs['']['type']));
            switch ($type) {
                case 'text':
                    return SIMPLEPIE_CONSTRUCT_TEXT;

                case 'html':
                    return SIMPLEPIE_CONSTRUCT_HTML;

                case 'xhtml':
                    return SIMPLEPIE_CONSTRUCT_XHTML;
            }
            if (in_array(substr($type, -4), array('+xml', '/xml')) || substr($type, 0, 5) == 'text/') {
                return SIMPLEPIE_CONSTRUCT_NONE;
            } else {
                return SIMPLEPIE_CONSTRUCT_BASE64;
            }
        } else {
            return SIMPLEPIE_CONSTRUCT_TEXT;
        }
    }

    function is_isegment_nz_nc($string)
    {
        return (bool)preg_match('/^([A-Za-z0-9\-._~\x{A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}\x{10000}-\x{1FFFD}\x{20000}-\x{2FFFD}\x{30000}-\x{3FFFD}\x{40000}-\x{4FFFD}\x{50000}-\x{5FFFD}\x{60000}-\x{6FFFD}\x{70000}-\x{7FFFD}\x{80000}-\x{8FFFD}\x{90000}-\x{9FFFD}\x{A0000}-\x{AFFFD}\x{B0000}-\x{BFFFD}\x{C0000}-\x{CFFFD}\x{D0000}-\x{DFFFD}\x{E1000}-\x{EFFFD}!$&\'()*+,;=@]|(%[0-9ABCDEF]{2}))+$/u', $string);
    }

    function space_seperated_tokens($string)
    {
        $space_characters = "\x20\x09\x0A\x0B\x0C\x0D";
        $string_length = strlen($string);

        $position = strspn($string, $space_characters);
        $tokens = array();

        while ($position < $string_length) {
            $len = strcspn($string, $space_characters, $position);
            $tokens[] = substr($string, $position, $len);
            $position += $len;
            $position += strspn($string, $space_characters, $position);
        }

        return $tokens;
    }

    function array_unique($array)
    {
        if (version_compare(PHP_VERSION, '5.2', '>=')) {
            return array_unique($array);
        } else {
            $array = (array)$array;
            $new_array = array();
            $new_array_strings = array();
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    if (method_exists($value, '__toString')) {
                        $cmp = $value->__toString();
                    } else {
                        trigger_error('Object of class ' . get_class($value) . ' could not be converted to string', E_USER_ERROR);
                    }
                } elseif (is_array($value)) {
                    $cmp = (string)reset($value);
                } else {
                    $cmp = (string)$value;
                }
                if (!in_array($cmp, $new_array_strings)) {
                    $new_array[$key] = $value;
                    $new_array_strings[] = $cmp;
                }
            }
            return $new_array;
        }
    }

    /**
     * Converts a unicode codepoint to a UTF-8 character
     *
     * @static
     * @access public
     * @param int $codepoint Unicode codepoint
     * @return string UTF-8 character
     */
    function codepoint_to_utf8($codepoint)
    {
        static $cache = array();
        $codepoint = (int)$codepoint;
        if (isset($cache[$codepoint])) {
            return $cache[$codepoint];
        } elseif ($codepoint < 0) {
            return $cache[$codepoint] = false;
        } else if ($codepoint <= 0x7f) {
            return $cache[$codepoint] = chr($codepoint);
        } else if ($codepoint <= 0x7ff) {
            return $cache[$codepoint] = chr(0xc0 | ($codepoint >> 6)) . chr(0x80 | ($codepoint & 0x3f));
        } else if ($codepoint <= 0xffff) {
            return $cache[$codepoint] = chr(0xe0 | ($codepoint >> 12)) . chr(0x80 | (($codepoint >> 6) & 0x3f)) . chr(0x80 | ($codepoint & 0x3f));
        } else if ($codepoint <= 0x10ffff) {
            return $cache[$codepoint] = chr(0xf0 | ($codepoint >> 18)) . chr(0x80 | (($codepoint >> 12) & 0x3f)) . chr(0x80 | (($codepoint >> 6) & 0x3f)) . chr(0x80 | ($codepoint & 0x3f));
        } else {
            // U+FFFD REPLACEMENT CHARACTER
            return $cache[$codepoint] = "\xEF\xBF\xBD";
        }
    }

    /**
     * Re-implementation of PHP 5's stripos()
     *
     * Returns the numeric position of the first occurrence of needle in the
     * haystack string.
     *
     * @static
     * @access string
     * @param object $haystack
     * @param string $needle Note that the needle may be a string of one or more
     *     characters. If needle is not a string, it is converted to an integer
     *     and applied as the ordinal value of a character.
     * @param int $offset The optional offset parameter allows you to specify which
     *     character in haystack to start searching. The position returned is still
     *     relative to the beginning of haystack.
     * @return bool If needle is not found, stripos() will return boolean false.
     */
    function stripos($haystack, $needle, $offset = 0)
    {
        if (function_exists('stripos')) {
            return stripos($haystack, $needle, $offset);
        } else {
            if (is_string($needle)) {
                $needle = strtolower($needle);
            } elseif (is_int($needle) || is_bool($needle) || is_double($needle)) {
                $needle = strtolower(chr($needle));
            } else {
                trigger_error('needle is not a string or an integer', E_USER_WARNING);
                return false;
            }

            return strpos(strtolower($haystack), $needle, $offset);
        }
    }

    /**
     * Similar to parse_str()
     *
     * Returns an associative array of name/value pairs, where the value is an
     * array of values that have used the same name
     *
     * @static
     * @access string
     * @param string $str The input string.
     * @return array
     */
    function parse_str($str)
    {
        $return = array();
        $str = explode('&', $str);

        foreach ($str as $section) {
            if (strpos($section, '=') !== false) {
                list($name, $value) = explode('=', $section, 2);
                $return[urldecode($name)][] = urldecode($value);
            } else {
                $return[urldecode($section)][] = null;
            }
        }

        return $return;
    }

    /**
     * Detect XML encoding, as per XML 1.0 Appendix F.1
     *
     * @todo Add support for EBCDIC
     * @param string $data XML data
     * @return array Possible encodings
     */
    function xml_encoding($data)
    {
        // UTF-32 Big Endian BOM
        if (substr($data, 0, 4) === "\x00\x00\xFE\xFF") {
            $encoding[] = 'UTF-32BE';
        } // UTF-32 Little Endian BOM
        elseif (substr($data, 0, 4) === "\xFF\xFE\x00\x00") {
            $encoding[] = 'UTF-32LE';
        } // UTF-16 Big Endian BOM
        elseif (substr($data, 0, 2) === "\xFE\xFF") {
            $encoding[] = 'UTF-16BE';
        } // UTF-16 Little Endian BOM
        elseif (substr($data, 0, 2) === "\xFF\xFE") {
            $encoding[] = 'UTF-16LE';
        } // UTF-8 BOM
        elseif (substr($data, 0, 3) === "\xEF\xBB\xBF") {
            $encoding[] = 'UTF-8';
        } // UTF-32 Big Endian Without BOM
        elseif (substr($data, 0, 20) === "\x00\x00\x00\x3C\x00\x00\x00\x3F\x00\x00\x00\x78\x00\x00\x00\x6D\x00\x00\x00\x6C") {
            if ($pos = strpos($data, "\x00\x00\x00\x3F\x00\x00\x00\x3E")) {
                $parser = new SimplePie_XML_Declaration_Parser(SimplePie_Misc::change_encoding(substr($data, 20, $pos - 20), 'UTF-32BE', 'UTF-8'));
                if ($parser->parse()) {
                    $encoding[] = $parser->encoding;
                }
            }
            $encoding[] = 'UTF-32BE';
        } // UTF-32 Little Endian Without BOM
        elseif (substr($data, 0, 20) === "\x3C\x00\x00\x00\x3F\x00\x00\x00\x78\x00\x00\x00\x6D\x00\x00\x00\x6C\x00\x00\x00") {
            if ($pos = strpos($data, "\x3F\x00\x00\x00\x3E\x00\x00\x00")) {
                $parser = new SimplePie_XML_Declaration_Parser(SimplePie_Misc::change_encoding(substr($data, 20, $pos - 20), 'UTF-32LE', 'UTF-8'));
                if ($parser->parse()) {
                    $encoding[] = $parser->encoding;
                }
            }
            $encoding[] = 'UTF-32LE';
        } // UTF-16 Big Endian Without BOM
        elseif (substr($data, 0, 10) === "\x00\x3C\x00\x3F\x00\x78\x00\x6D\x00\x6C") {
            if ($pos = strpos($data, "\x00\x3F\x00\x3E")) {
                $parser = new SimplePie_XML_Declaration_Parser(SimplePie_Misc::change_encoding(substr($data, 20, $pos - 10), 'UTF-16BE', 'UTF-8'));
                if ($parser->parse()) {
                    $encoding[] = $parser->encoding;
                }
            }
            $encoding[] = 'UTF-16BE';
        } // UTF-16 Little Endian Without BOM
        elseif (substr($data, 0, 10) === "\x3C\x00\x3F\x00\x78\x00\x6D\x00\x6C\x00") {
            if ($pos = strpos($data, "\x3F\x00\x3E\x00")) {
                $parser = new SimplePie_XML_Declaration_Parser(SimplePie_Misc::change_encoding(substr($data, 20, $pos - 10), 'UTF-16LE', 'UTF-8'));
                if ($parser->parse()) {
                    $encoding[] = $parser->encoding;
                }
            }
            $encoding[] = 'UTF-16LE';
        } // US-ASCII (or superset)
        elseif (substr($data, 0, 5) === "\x3C\x3F\x78\x6D\x6C") {
            if ($pos = strpos($data, "\x3F\x3E")) {
                $parser = new SimplePie_XML_Declaration_Parser(substr($data, 5, $pos - 5));
                if ($parser->parse()) {
                    $encoding[] = $parser->encoding;
                }
            }
            $encoding[] = 'UTF-8';
        } // Fallback to UTF-8
        else {
            $encoding[] = 'UTF-8';
        }
        return $encoding;
    }
}

?>