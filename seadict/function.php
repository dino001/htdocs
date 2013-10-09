<?php

    /**
    * Check and get form POST data
    * 
    * @param mixed $key
    * @return mixed
    */
    function get_post_data($key)
    {
        if (isset($_POST[$key]) && !is_string_empty($_POST[$key]))
        {
            return $_POST[$key];
        }
        else
        {
            return "";
        }
    }

    /**
    * Return true if string is empty or null
    * 
    * @param mixed $str
    */
    function is_string_empty($str)
    {
        if (is_null($str))
        {
            return true;
        }
        elseif (is_array($str))
        {
            if (count($str) == 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        elseif (!is_object($str))
        {
            $len = strlen(trim($str));              
            if ($len <= 0) return true;
        }
        return false;
    }

    /**
    * Return one last word
    * 
    * @param mixed $fullword
    * @return mixed
    */
    function get_last_word($fullword)
    {
        $fullword = trim($fullword);
        if (vn_word_count($fullword) == 1) return $fullword;
        $arrSplit = explode(" ", $fullword);
        return $arrSplit[count($arrSplit) - 1];
    }

    /**
    * Return the first word
    * 
    * @param mixed $fullword
    * @return mixed
    */
    function get_first_word($fullword)
    {
        $fullword = trim($fullword);
        if (vn_word_count($fullword) == 1) return $fullword;
        $arrSplit = explode(" ", $fullword);
        return $arrSplit[0];
    }

    /**
    * Count number of word in a Vietnamese string
    * 
    * @param string $input_string
    */
    function vn_word_count($input_string)
    {
        $input_string = trim(tmp_process_raw_word($input_string));
        $word_count = 1;
        $word_count = substr_count($input_string, ' ') + 1;
        return $word_count;
    }

    /**
    * Remove all tones unicode
    * Source: http://phpvn.org/index.php/topic,5681.0.html
    * 
    * @param mixed $str
    * @return mixed
    */
    function vn_str_filter ($str){
        $unicode = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd'=>'đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i'=>'í|ì|ỉ|ĩ|ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
        'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D'=>'Đ',
        'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
        'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }


    /**
    * Process raw word
    * 
    * @param mixed $raw_word
    * @return mixed
    */
    function tmp_process_raw_word($raw_word)
    {
        $raw_word = str_replace("-", " ", $raw_word);
        //Turn all many-space into one space
        while (strpos($raw_word, "  ") !== false)
        {
            $raw_word = str_replace("  ", " ", $raw_word);
        }
        return $raw_word;
    }
?>
