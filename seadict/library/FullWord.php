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
            "word_lowercase"=>$this->LowerCase);
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
        foreach($arrSplit as $raw_word)
        {
            $one_word = new OneWord();
            $one_word->set_word($raw_word);
            array_push($arrOneWordId, $one_word->getID());
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
            insertSQL($arrData, "fullword_oneword");
        }
    }
}

