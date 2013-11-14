SELECT fullword.`word` AS fullword, oneword.`word` AS oneword,fullword.`fullword_id`,
oneword.`oneword_id`, fullword.`is_propernoun`, oneword.`tone`
FROM fullword, oneword, fullword_oneword
WHERE fullword.`fullword_id` = `fullword_oneword`.`fullword_id`
AND oneword.`oneword_id` = fullword_oneword.`oneword_id`
AND oneword.`syllable` = 'iên'
AND fullword_oneword.`is_lastword` = 1
AND fullword.`word_lowercase` = fullword.`word`
GROUP BY oneword.`tone`, oneword.`oneword_id`, fullword.`fullword_id`

