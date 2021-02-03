<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/properties.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";
include_once "utils.php";

$text = get_required("q");

$response = array();

function indexing($word, $mode)
{
    /*$word_id = scalar("select word_id from words where word_text = '$word' and word_mode = '$mode'");
    if ($word_id != null) {
        return select("select t2.*  from word_similar t1 left join words t2 on t1.word_id_similar == t2.word_id where word_id == $word_id");
    } else */ {
        echo $mode;


    $similar_words = select("select * from words where length(word_text) >= " . strlen($word) . " - 1 and length(word_text) <= " . strlen($word) . " + 1 and word_mode = '$mode'");

    $word_id = insertRowAndGetId("words", array(
        "word_text" => $word,
        "word_mode" => $mode,
    ));

    $result = [];
    echo json_encode($similar_words) . "\n";
    foreach ($similar_words as $similar_word) {
        $distance = similar_distance($word, $similar_word["word_text"]);
        if ($distance <= 2) {
            insertRow("word_similar", array(
                "word_id" => $word_id,
                "word_id_similar" => $similar_word["word_id"],
            ));
            insertRow("word_similar", array(
                "word_id" => $similar_word["word_id"],
                "word_id_similar" => $word_id,
            ));
            $result[] = $similar_word;
        }
    }

    if ($mode == "text") {
        $sort_chars = utf_split($word);
        sort($sort_chars);
        $sort_word = implode('', $sort_chars);
        indexing($sort_word, "sort");

        $chars = [];
        foreach ($sort_chars as $sort_char)
            if (array_search($sort_char, $chars) === false)
                $chars[] = $sort_char;
        $char_word = implode('', $chars);
        indexing($char_word, "chars");
    }

    return $similar_words;
}
}

query("delete from words");
query("delete from word_similar");
indexing("привет", "text");
indexing("привет2", "text");


return $response;

