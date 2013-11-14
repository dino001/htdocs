<?php

    function backfill_lastword()
    {
        global $db;
        $sqlAll = "SELECT * FROM fullword where fullword_id > 0";
        $rows = $db->getManyRow($sqlAll);
        $count_row = count($rows);
        for($i = 0; $i < $count_row; $i++)
        {
            $row = $rows[$i];
            $fullword_id = $row["fullword_id"];
            $fullword = $row["word"];
            $fullword_lowercase = $row["word_lowercase"];
            if ($fullword !== mb_strtolower($fullword, 'UTF-8'))
            {
                $is_propernoun = true;                
                tmp_update_propernoun($fullword_id);
            }
            else
            {
                $is_propernoun = false;
            }   
            //Get last word to update DB
            //$lastword = get_last_word($fullword_lowercase);
            //tmp_update_lastword($fullword_id, $lastword);                         
        }
    }
        
    function tmp_update_lastword($fullword_id, $lastword)
    {
        $oneword = new OneWord();
        $oneword->Word = $lastword;        
        $lastword_id = $oneword->getID();
        
        $sqlUpdate = "UPDATE fullword_oneword SET is_lastword = true 
        WHERE fullword_id = ?
        AND oneword_id = ?";
        $arrParam = array($fullword_id, $lastword_id);
        global $db;
        $db->execute($sqlUpdate, $arrParam);        
    }
    
    /**
    * Return first character in a string
    * 
    * @param mixed $input
    * @return string
    */
    function str_first_char($input)
    {
        return mb_strcut($input, 0, 1, 'UTF-8');
    }
    
    /**
    * Return true if string is all lowercase
    * 
    * @param mixed $input
    */
    function str_islowercase($input)
    {
        if (mb_strtolower($input, 'UTF-8') == $input)
        {
            return true;
        }
        return false;
    }
    
    /**
    * Update proper noun in DB
    * 
    * @param mixed $fullword_id
    */
    function tmp_update_propernoun($fullword_id)
    {        
        $arrUpdate = array("is_propernoun"=>1,
        "word_type"=>1,
        "fullword_id"=>$fullword_id);
        updateSQL($arrUpdate, "fullword", "fullword_id");
    }
    
    /**
    * Return one last word
    * 
    * @param mixed $fullword
    * @return mixed
    */
    function get_last_word($fullword)
    {
        if (vn_word_count($fullword) == 1) return $fullword;
        $arrSplit = explode(" ", $fullword);
        return $arrSplit[count($arrSplit) - 1];
    }

    /**
    * Read all words in dictionary file
    * 
    */
    function parse_dictionary()
    {
        $input_path = "dictd_viet-viet.txt";
        $raw_input = file_get_contents($input_path);
        $arrLine = explode("\r\n", $raw_input);
        $count_line = 0;        
        //foreach($arrLine as $line)
        $total_line = count($arrLine);
        $starting_line = 17199;
        for ($i = $starting_line; $i<=$total_line; $i++)
        {
            $line = $arrLine[$i];
            $word = new FullWord();
            $arrSplit = explode("\t@", $line);
            $word->Word = trim($arrSplit[0]);
            if (is_string_empty($word->Word)) continue;
            $word->Word_NoTone = strtolower(vn_str_filter(tmp_process_raw_word($word->Word)));
            //Split by @ to get the first meaning only
            $arrSplit = explode("\\n\\n", $arrSplit[1]);
            $arrSplit = explode("\\n-", $arrSplit[0]);
            $word->Meaning = trim($arrSplit[1]);
            $word->CountWord = vn_word_count($word->Word_NoTone);            
            $word->WordType = tmp_get_wordtype_from_meaning($word->Meaning);
            $word->LowerCase = mb_strtolower(tmp_process_raw_word($word->Word), 'UTF-8');
            $word->IsProperNoun = !str_islowercase($word->Word);
            
            $word->getID();
            //var_dump($word);
            $count_line++;
            //if ($count_line == 50) break;
        }
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
    * Process raw word
    * 
    * @param mixed $raw_word
    * @return mixed
    */
    function tmp_process_raw_word($raw_word)
    {        
        $raw_word = trim($raw_word);
        $raw_word = str_replace("-", " ", $raw_word);
        $raw_word = str_replace(",", " ", $raw_word);
        $raw_word = str_replace(";", " ", $raw_word);
        $raw_word = str_replace(":", " ", $raw_word);
        $raw_word = str_replace("'", " ", $raw_word);
        $raw_word = str_replace("(", " ", $raw_word);
        $raw_word = str_replace(")", " ", $raw_word);
        $raw_word = str_onespace($raw_word);
        return $raw_word;
    }
    
    /**
    * Replace two or more splace by one space only
    * 
    * @param mixed $input
    */
    function str_onespace($input)
    {
        while (strpos($input, "  ")!== false)
        {
            $input = str_replace("  ", " ", $input);
        }
        return $input;
    }

    /**
    * Split meaning to get word type 
    * (khác, danh, động, tính từ => 0,1,2,3)
    * @param mixed $meaning
    */
    function tmp_get_wordtype_from_meaning($meaning)
    {
        $meaning = trim($meaning);
        $signal = ". ";
        $pos_signal = strpos($meaning, $signal);
        if ($pos_signal !== false && $pos_signal < 30)
        {       
            $pos_space = strrpos(substr($meaning, 0, $pos_signal + 1), ' ');
            $raw_type = trim(substr($meaning, $pos_space, $pos_signal - $pos_space));
        }
        $word_type = 0; //Default is Other
        //Mapping $raw_type
        switch($raw_type)
        {
            case "d":
            case "dt":
                $word_type = 1;
                break;
            case "đg":
            case "đgt":
                $word_type = 2;
                break;
            case "tt":
                $word_type = 3;
                break;    
            default:
                $word_type = 0;
                break;    
        }
        return $word_type;
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
    * Log error to text file
    * 
    * @param mixed $message
    */
    function log_error($message)
    {
        $logFile = "dict_rre_log.txt";
        $message = "==".format_date(null, "d-m-Y H:i:s")."\r\n".$message."\r\n";
        file_put_contents($logFile, $message, FILE_APPEND);
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
    * Format date string or date object
    * 
    * @param mixed $dateObj
    * @param mixed $formatString default is SQL style
    * @return string
    */
    function format_date($dateObj = null, $formatString = 'Y-m-d H:i:s')
    {    
        //If no parameter, return formatted current date
        if (func_num_args() == 0) return date($formatString);
        $tmpResult = '';
        //If dateObj is null, set current date as default
        if (is_string_empty($dateObj)) $dateObj = date('Y-m-d H:i:s');

        if (is_object($dateObj))                             
        {
            $tmpResult = date_format($dateObj, $formatString);
        }
        elseif (!is_string_empty($dateObj))
        {
            //If input is not date object, try to convert
            $timeStamp = strtotime($dateObj);
            if ($timeStamp)
            {
                $tmpResult = date($formatString, $timeStamp);
            }
        }       
        return $tmpResult;
    }

    /**
    * Format inputstring (dd/mm/yyyy) to DB style (yyyy-mm-dd)
    * 
    * @param string $dateObj
    * @return string
    */
    function format_date_dbstyle($dateObj)
    {
        global $g_dateFormatDb;

        $dateObj = str_replace("/", "-", $dateObj);
        $dateObj = formatDate($dateObj, $g_dateFormatDb);

        return $dateObj;
    }

    /**
    * Generate update SQL
    * 
    * @param mixed $row
    * @param mixed $tableName
    * @param mixed $pkName Primary key
    * @param string $sql
    * @param mixed $arrParam
    */
    function generateUpdateSQL($row, $tableName, $pkName, &$sql, &$arrParam)       
    {
        $sql = "UPDATE ".$tableName." SET ";
        $arrParam = array();
        foreach($row as $key=>$value)
        {
            if ($key != $pkName)
            {
                //Add fieldnames
                $sql .= $key." = ?,";
                array_push($arrParam, $value);
            }
        }
        $sql = substr($sql, 0, strlen($sql) - 1);        
        $sql .= " WHERE ".$pkName." = ".$row[$pkName];        
    }

    /**
    * Generate and do SQL update
    * 
    * @param mixed $row
    * @param mixed $tableName
    * @param mixed $pkName
    */
    function updateSQL($row, $tableName, $pkName, $db = null)
    {
        if (is_string_empty($db)) $db = new DBWrapper();
        generateUpdateSQL($row, $tableName, $pkName, $sql, $arrParam);
        $db->execute($sql, $arrParam);
    }

    /**
    * Generate and do SQL insert
    * 
    * @param mixed $row
    * @param mixed $tableName
    */
    function insertSQL($row, $tableName, $db = null)
    {
        if (is_string_empty($db)) $db = new DBWrapper();
        generateInsertSQL($row, $tableName, $sql, $arrParam);
        //echo $db->interpolateQuery($sql, $arrParam)."<br>";
        $db->execute($sql, $arrParam);
        return $db->getLastInsertId();
    } 

    /**
    * Generate insert SQL query
    * 
    * @param mixed $row
    * @param mixed $tableName
    * @param string $sql output SQL
    * @param array $arrParam out SQL parameters
    */
    function generateInsertSQL($row, $tableName, &$sql, &$arrParam)
    {
        $sql = "INSERT INTO ".$tableName." (";
        $arrParam = array();
        foreach($row as $key=>$value)
        {
            //Add fieldnames
            $sql .= $key.",";
            array_push($arrParam, $value);
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        $sql .= ") VALUES(";
        foreach($row as $key=>$value)
        {
            $sql .= "?,";
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        $sql .= ")";
    }

?>
