<?php
foreach(file(__DIR__ . '/backend2/TextRank/lib/TextRank/Stopword/french-geo.txt') as $f)
	echo mb_strtolower($f) . '<br />';