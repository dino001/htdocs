<?php
    require_once('header.php');
    set_time_limit(7200);
    
    
    //$testInput = "Hoàn";
//    $word = new OneWord();
//    $word->set_word($testInput);    
//    echo "<pre>";      
//    $word->getID();
//    var_dump($word);
//    echo "</pre>";
   // iconv_set_encoding("internal_encoding", "UTF-8");
    echo "<pre>";  
    parse_dictionary();
    //backfill_lastword();
    //backfill_error_2();
    echo "Done backfill dict";    
    echo "</pre>";
?>

<?php

    /**
    * Backfill for erorr when "đụng" has syllable "ưng"
    * 
    */
    function backfill_error()
    {
        $db = new DBWrapper();
        $sql_backfill = "SELECT * FROM oneword WHERE oneword.`syllable` LIKE 'ưng' AND oneword.`tone` = 5";
        $rows = $db->getManyRow($sql_backfill);
        foreach($rows as $row)
        {
            $oneword = new OneWord();
            $oneword->set_word($row["word"]);
            $oneword->parse_one_word();
            //if (mb_convert_encoding($oneword->Syllable, "utf-8") != mb_convert_encoding($row["syllable"], "utf-8"))
            if (strcasecmp($oneword->Syllable, $row["syllable"]) !== 0)
            {
                //Update syllable
                $sqlUpdate = "UPDATE oneword SET syllable = ? WHERE oneword_id = ?";
                $arrParam = array($oneword->Syllable, $row["oneword_id"]);
                $db->execute($sqlUpdate, $arrParam);
                echo "Fixed ".$row["word"]."<br>";
            }
        }
    }
    
    
    /**
    * Backfill for erorr when "đựng" has syllable "ựng" and tone = 0
    * 
    */
    function backfill_error_2()
    {
        $db = new DBWrapper();
        $sql_backfill = "SELECT * FROM oneword WHERE oneword.`syllable` LIKE 'ựng'";
        $rows = $db->getManyRow($sql_backfill);
        foreach($rows as $row)
        {
            $oneword = new OneWord();
            $oneword->set_word($row["word"]);
            $oneword->parse_one_word();
            //if (mb_convert_encoding($oneword->Syllable, "utf-8") != mb_convert_encoding($row["syllable"], "utf-8"))
            if (strcasecmp($oneword->Syllable, $row["syllable"]) !== 0)
            {
                //Update syllable
                $sqlUpdate = "UPDATE oneword SET syllable = ?, tone = ? WHERE oneword_id = ?";
                $arrParam = array($oneword->Syllable, $oneword->Tone, $row["oneword_id"]);
                $db->execute($sqlUpdate, $arrParam);
                echo "Fixed ".$row["word"]."<br>";
            }
        }
    }

    function an_process_string($input)
    {
        $arrSplit = explode(",",$input);
        $out = "";
        foreach($arrSplit as $word)
        {
            $word = trim($word);
            $out = $out .',"'.$word.'"';
        }
        echo $out;
    }  
?>
