<?php
$list = file('TextRank/lib/TextRank/Stopword/french-geo.txt');

foreach($list as $word) {
	// àâäéèêëïîôöùûüÿç
	$simple = strtr($word, ["à" => "a", "â" => "a", "ä" => "a", "é" => "e", "è" => "e", "ê" => "e", "ë" => "e", "ï" => "i", "î" => "i", "ô" => "o", "ö" => "o", "ù" => "u", "û" => "u", "ü" => "u", "ÿ" => "y", "ç" => "c", "œ" => "oe", "æ" => "ae"]);
	if($simple != $word) {
		$list[] = $simple;
		echo $simple;
	}
}

file_put_contents('TextRank/lib/TextRank/Stopword/french-geo-full.txt', implode('', $list));