<?php

function utf_split($word)
{
    return preg_split('//u', $word, null, PREG_SPLIT_NO_EMPTY);
}

function similar_distance($word1, $word2)
{
    $distance = 0;
    $chars1 = utf_split($word1);
    $chars2 = utf_split($word2);
    $word1_length = sizeof($chars1);
    $word2_length = sizeof($chars2);
    $i = 0;
    $j = 0;
    while ($i < $word1_length && $j < $word2_length) {
        $char1 = ord($chars1[$i]);
        $char2 = ord($chars2[$j]);
        if ($char1 == $char2) {
            $i += 1;
            $j += 1;
        } else if ($char1 > $char2) {
            $j += 1;
            $distance += 1;
        } else if ($char1 < $char2) {
            $i += 1;
            $distance += 1;
        }
        if ($i == $word1_length) {
            $distance += $word2_length - $j;
            continue;
        } else if ($j == $word2_length) {
            $distance += $word1_length - $i;
            continue;
        }
    }
    return $distance;
}