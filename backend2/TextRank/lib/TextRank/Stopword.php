<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2013 César Rodas                                                  |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/
namespace crodas\TextRank;

use LanguageDetector\Detect;

/**
 *  Data files has been borrowed from
 *      https://github.com/ekorn/Keywords
 */
class Stopword extends DefaultEvents
{
    protected $stopword;
    protected $common_words;
    protected $lang;

    public function normalize_keywords(Array $keywords)
    {
        $normalized = parent::normalize_keywords($keywords);
        $callback   = "stemword";
        
        $tagger   = __NAMESPACE__ . '\POS\\' . ucfirst($this->lang) . '\Tagger';
        if (is_callable($callback)) {
            return array_map(function ($keyword) use ($callback) {
                return $callback($keyword, 'french', 'UTF_8');
            }, $normalized);
        }
        return $normalized;
    }

    public function filter_keywords(Array $keywords)
    {
        $keywords = parent::filter_keywords($keywords);
        $tagger   = __NAMESPACE__ . '\POS\\' . ucfirst($this->lang) . '\Tagger';

        if (class_exists($tagger)) {
            $keywords = $tagger::get($keywords);
        }

        $keywords = array_filter($keywords, function ($word) {
            $word = mb_strtolower($word);
            $normalized_word = stemword($word, 'french', 'UTF_8');
            return empty($this->stopword[$word]) && empty($this->common_words[$normalized_word]) && stristr($word, 'www') === false && stristr($word, '@') === false && stristr($word, '.com') === false && stristr($word, '.fr') === false;
        });

        return $keywords;
    }

    protected function getClassifier()
    {
        static $detect;
        if (empty($detect)) {
            $detect = Detect::initByPath(__DIR__ . '/language-profile.php');
        }
        return $detect;
    }
    protected function getStopwords()
    {
      //require __DIR__ . '/Stopword/Stopword.php';
      static $stopwords;
      $stopwords = [];
      $words = file(__DIR__ . '/Stopword/french-stopwords.txt', FILE_IGNORE_NEW_LINES) 
        //+ file(__DIR__ . '/Stopword/french-geo.txt', FILE_IGNORE_NEW_LINES)
      ;
      foreach($words as $w)
        $stopwords[$w] = 1;
      return ['french' => $stopwords];
    }

    public function get_words($text)
    {
        $detect    = $this->getClassifier();
        $stopwords = $this->getStopwords(); 

        $lang = 'french';
        if (!is_string($lang)) {
            throw new \RuntimeException("Cannot detect the language of the text");
        }
        if (empty($stopwords[$lang])) {
            throw new \RuntimeException("We dont have an stop word for {$lang}, please add it in " . __DIR__ . "/Stopword/{$lang}-stopwords.txt and run generate.php");
        }
        $this->stopword = $stopwords[$lang];
        $this->common_words = [];
        $common_words = $this->normalize_keywords(file(__DIR__ . '/Stopword/common-french.txt', FILE_IGNORE_NEW_LINES));
        foreach($common_words as $cw)
          $this->common_words[$cw] = 1;

        $this->lang     = $lang;

        return parent::get_words($text);
    }
}
