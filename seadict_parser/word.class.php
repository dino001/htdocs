<?php
    /**
    * Một chữ
    */
    class OneWord
    {
        public $Word; //Từ gốc
        public $Word_NoTone; //Từ không dấu
        public $FirstConsonant; //Âm đầu
        public $Syllable; //Vần
        public $Tone; //Dấu thanh: Ngang-Sắc-Huyền-Hỏi-Ngã-Nặng => 0-1-2-3-4-5
        public $ID; //ID trong database

        /**
        * Set new word (lower case)
        * 
        * @param mixed $word
        */
        public function set_word($raw_word)
        {
            $this->Word = mb_strtolower($raw_word, 'UTF-8');              
        }
        
        /**
        * Return database ID of this word
        * Insert word to database if not inserted
        * 
        */
        public function getID()
        {
            if (!is_string_empty($this->ID)) return $this->ID;
            global $db;
            $sql = "SELECT * FROM oneword WHERE word = ?";
            $arrParam = array($this->Word);
            $rows = $db->getManyRow($sql, $arrParam);
            $id_return = -1;
            if (count($rows) > 1)
            {
                log_error("OneWord dupplicated ".$this->Word)." db oneword_id=".$rows[0]["oneword_id"];   
                $id_return = $rows[0]["oneword_id"];
            }
            elseif (count($rows) == 1)
            {
                $id_return = $rows[0]["oneword_id"];
            }
            else
            {
                //Insert new OneWord
                if (is_string_empty($this->Syllable)) 
                {
                    $this->parse_one_word();
                }
                $arrOneWord = array("word"=>$this->Word,
                "word_notone"=>$this->Word_NoTone,
                "first_consonant"=>$this->FirstConsonant,
                "syllable"=>$this->Syllable,
                "tone"=>$this->Tone);
                $id_return = insertSQL($arrOneWord, "oneword", $db);
            }
            $this->ID = $id_return;
            return $id_return;
        }

        /**
        * Parse word into structure        
        */
        public function parse_one_word()
        {
            $input = $this->Word;
            $input = trim($input);
            $arrConsonant = array( "ch", "gh", "kh", "ng", "ngh", "nh", "ph", "th", "tr", "gi", "qu",
            "b","c","d","đ","g","h","k","l","m","n","p","q","r","s","t","v","x");

            $arrVowel = array(
            "a"=>array("a","á","à","ả","ã","ạ"),
            "ă"=>array("ă","ắ","ằ","ẳ","ẵ","ặ"),
            "â"=>array("â","ấ","ầ","ẩ","ẫ","ậ"),
            "e"=>array("e","é","è","ẻ","ẽ","ẹ"),
            "ê"=>array("ê","ế","ề","ể","ễ","ệ"),
            "i"=>array("i","í","ì","ỉ","ĩ","ị"),
            "o"=>array("o","ó","ò","ỏ","õ","ọ"),
            "ô"=>array("ô","ố","ồ","ổ","ỗ","ộ"),
            "ơ"=>array("ơ","ớ","ờ","ở","ỡ","ợ"),
            "u"=>array("u","ú","ù","ủ","ũ","ụ"),
            "ư"=>array("ư","ứ","ừ","ử","ữ","ự"),
            "y"=>array("y","ý","ỳ","ỷ","ỹ","ỵ"),
            );

            //$word = new OneWord();
            $word = &$this;
            $word->Word = $input;
            //Get consonant
            $has_consonant = false;
            foreach($arrConsonant as $consonant)
            {
                $pos_consonant = strpos($input, $consonant);
                if ($pos_consonant === 0)
                {
                    $word->FirstConsonant = $consonant;
                    $the_rest = substr($input, strlen($consonant));                
                    $has_consonant = true;
                    break;
                }        
            }
            if ($has_consonant == false)
            {
                $word->FirstConsonant = null;
                $the_rest = $input;
            }

            //Get tone
            $has_tone = false;
            foreach($arrVowel as $keyVowel=>$subarrVowel)
            {
                for($i = 5; $i>0; $i--)
                {
                    $vowel = $subarrVowel[$i];
                    $pos_vowel = strpos($the_rest, $vowel);
                    if ($pos_vowel !== false)
                    {
                        $word->Tone = $i;                      
                        $word->Syllable = str_replace($vowel, $keyVowel, $the_rest);
                        $has_tone = true; 
                        break;
                    }
                }
            }
            if ($has_tone == false)
            {
                $word->Tone = 0; //No tone
                $word->Syllable = $the_rest;
            } 

            //Strip all tone and lower case (for easy searching)
            $word->Word_NoTone =strtolower(vn_str_filter($input));            
        }  
    }
?>
<?php  
    /**
    * Một từ hoàn chỉnh (có nghĩa)  
    */
    class FullWord
    {
        public $Word; //Từ gốc
        public $Word_NoTone; //Từ không dấu
        public $Meaning; //Nghĩa
        public $CountWord; //Số chữ của từ
        public $WordType; //Loại từ: khác, danh từ, động từ, tính từ => 0,1,2,3        
        public $LowerCase; //Từ có dấu nhưng viết thường, dùng để tách thành chữ
        public $ID; //ID trong database
        public $IsProperNoun; //Có phải là danh từ riêng hay không

        /**
        * Return database ID of this word
        * Insert word to database if not inserted
        * 
        */
        public function getID()
        {
            if (!is_string_empty($this->ID)) return $this->ID;
            global $db;
            $sql = "SELECT * FROM fullword WHERE word = ?";
            $arrParam = array($this->Word);
            $rows = $db->getManyRow($sql, $arrParam);
            $id_return = -1;
            if (count($rows) > 1)
            {
                log_error("FullWord dupplicated ".$this->Word)." db fullword_id=".$rows[0]["fullword_id"];   
                $id_return = $rows[0]["fullword_id"];
            }
            elseif (count($rows) == 1)
            {
                $id_return = $rows[0]["fullword_id"];
            }
            else
            {
                //Insert new FullWord
                $arrOneWord = array("word"=>$this->Word,
                "word_notone"=>$this->Word_NoTone,
                "count_word"=>$this->CountWord,
                "word_type"=>$this->WordType,
                "meaning"=>$this->Meaning,
                "word_lowercase"=>$this->LowerCase,
                "is_propernoun"=>$this->IsProperNoun);
                $id_return = insertSQL($arrOneWord, "fullword", $db);
                //Mapping fullword to one word in database
                $this->ID = $id_return;
                $this->link_fullword_to_oneword();
            }
            $this->ID = $id_return;
            return $id_return;
        }
        
        /**
        * Analyze each full word into many onewords
        *                
        */
        public function link_fullword_to_oneword()
        {
            $arrSplit = explode(" ",$this->LowerCase);
            //Get list of oneword_id
            $arrOneWordId = array();
            $last_word_id = -1;
            //foreach($arrSplit as $raw_word)                  
            for($i = 0; $i<count($arrSplit); $i++)
            {
                $raw_word = $arrSplit[$i];
                $one_word = new OneWord();
                $one_word->set_word($raw_word);
                $tmp_oneword_id = $one_word->getID();
                array_push($arrOneWordId, $tmp_oneword_id);
                if ($i == count($arrSplit)-1)
                {
                    $last_word_id = $tmp_oneword_id;
                }
            }
            
            //Remove dupplicated oneword_id (Example: ai hầu chi ai)
            $arrOneWordId = array_unique($arrOneWordId);
            //Insert to DB
            foreach($arrOneWordId as $one_word_id)
            {
                $arrData = array(
                "fullword_id"=>$this->ID,
                "oneword_id"=>$one_word_id
                );
                if ($one_word_id == $last_word_id)
                {
                    $arrData["is_lastword"] = true;
                }
                insertSQL($arrData, "fullword_oneword");
            }
        }
    }
?>

