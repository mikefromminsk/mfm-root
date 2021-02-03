<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/properties.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";
include_once "utils.php";


$text = get_required("q");

$response = array();

function search($word, $mode)
{
    $word_id = scalar("select word_id from words where word_text = '$word' and word_mode = '$mode'");
    if ($word_id != null)
        return select("select t2.*  from word_similar t1 left join words t2 on t1.word_id_similar = t2.word_id where t1.word_id = $word_id");
    return null;
}

$text_search = search($text, "text");
if ($text_search != null)
    die(json_encode($text_search));

$sort_chars = utf_split($text);
sort($sort_chars);
$sort_word = join($sort_chars);
$sort_search = search($sort_word, "sort");
if ($sort_search != null)
    die(json_encode($sort_search));

$chars = count_chars($sort_word, 3);
$chars_search = search($chars, "chars");
if ($text_search != null)
    die(json_encode($text_search));

die( json_encode($response));

