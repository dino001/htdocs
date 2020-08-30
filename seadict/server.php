<?php
require_once('header.php');

$action = get_post_data("action");
$result = array();
$result_code = 0;

switch($action){
    case "search_syllable":
        $result["data"] = search_syllable();
        break;
    case "get_random_word":
        $result["data"] = get_random_word(get_post_data("length_min"), get_post_data("length_max"));
        break;
}

$result["code"] = $result_code;
echo json_encode($result);

//------------------------------------------------------------------------------------------------------------------------------------------------
function get_random_word($length_min = 1, $length_max = 4) {
    global $db;
    $sql_one_word = "SELECT word, meaning FROM fullword WHERE count_word BETWEEN $length_min AND $length_max ORDER BY RAND() LIMIT 1";
    $row = $db->getOneRow($sql_one_word);
    return [
        'random_word' => $row['word'],
        'meaning' => $row['meaning'],
    ];
}

function search_syllable()
{
    global $db;
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