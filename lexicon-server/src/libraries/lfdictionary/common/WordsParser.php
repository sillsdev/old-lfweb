<?php
namespace libraries\lfdictionary\common;

/**
 * WordsParser breaks text into 'words'. This is used by the gatherWordsFromText (or File) commands.
 * TODO Move. Somewhere closer to the GatherWordsFromTextCommand would be better. CP 2013-12
 * REVIEWED CP 2013-12: Ok.
 */
class WordsParser {

	//http://en.wikipedia.org/wiki/Punctuation

	public static $Punctuation = array('’', '\'', '[',
	']', '(', ')', 
	'{', '}', '⟨', 
	'⟩', ':', ',', 
	'‒', '–', '—', 
	'―', '…', '...', 
	'. . .',
	'!', '.', '«',
	'»', '‐', '-',
	'?', '‘', '’',
	'“', '”', '\'',
	'\'', '"', '"',
	';', '/', '⁄');


	/**
	 * parsing a UTF8 string into an array, each element contrins a word.
	 * @return UTF8 string array
	 */
	public static function parsingToArray($string) {

		//remove punctuation
		
		foreach(WordsParser::$Punctuation as $pun)
		{
			$string = str_replace($pun, " ", $string);
		}
		
		//remove newline,Tab, and double space.
		$string = str_replace("\n", " ", $string);
		$string = str_replace("\r", " ", $string);
		$string = str_replace("  ", " ", $string);  
		$string = str_replace("	", " ", $string);
		$arrtext = explode(" ", $string);
		foreach($arrtext as $keyword)
		{
			$wordsArr[] = $keyword;
		}
		
		return $wordsArr;
	}
};
?>