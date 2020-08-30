<?php
require_once('header.php');

$action = get_post_data("action");
$result = array();
$result_code = 0;

if ($action == "search_syllable")
{
    $result["data"] = search_syllable();
}

$result["code"] = $result_code;
echo json_encode($result);

//------------------------------------------------------------------------------------------------------------------------------------------------
function search_syllable()
{
    $raw_word = get_post_data("word");
    $search_word = get_last_word($raw_word);
    $oneword = new OneWord();
    $oneword->Word = $search_word;
    $oneword->parse_one_word();
    $sql_search = "SELECT fullword.`word` AS fullword, oneword.`word` AS oneword,fullword.`fullword_id`,
    oneword.`oneword_id`, fullword.`is_propernoun`, oneword.`tone`
    FROM fullword, oneword, fullword_oneword
    WHERE fullword.`fullword_id` = `fullword_oneword`.`fullword_id`
    AND oneword.`oneword_id` = fullword_oneword.`oneword_id`
    AND oneword.`syllable` = ?
    AND fullword_oneword.`is_lastword` = 1
    AND fullword.`word_lowercase` = fullword.`word`
    GROUP BY oneword.`tone`, oneword.`oneword_id`, fullword.`fullword_id`
    ";
    $arrParam = array($oneword->Syllable);
    global $db;
    $rows = $db->getManyRow($sql_search, $arrParam);
    $data = array();
    foreach($rows as $row)
    {
        $tone = $row["tone"];
        $word = $row["oneword"];
        $is_propernoun = $row["is_propernoun"];
        $arrWord = array();
        $arrWord["fullword"] = $row["fullword"];
        $arrWord["fullword_id"] = $row["fullword_id"];
        $arrWord["is_propernoun"] = $row["is_propernoun"];
        if (!isset($data[$tone]))
        {
            $data[$tone] = array();
        }
        if (!isset($data[$tone][$word]))
        {
            $data[$tone][$word] = array();
        }
        array_push($data[$tone][$word], $arrWord);
    }
    if (count($rows) > 0)
    {
        $data["count"] = count($rows);
        $data["tone_default"] = $oneword->Tone;
    }
    else
    {
        $data["count"] = 0;
    }
    return $data;
}
?>