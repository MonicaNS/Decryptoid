<?php
/**
 * Authors : Prerana Sunilkumar, Kanchanak Khat, Monica Noori Saghar, Wonyoung Kim.
 */

$cipherKey = "yhkqgvxfoluapwmtzecjdbsnri";

function substitution($input, $original, $key){
	$output = "";
	$length = strlen($input);

	for ($i = 0; $i < $length; ++$i)
	{
		$letter = strtolower($input[$i]);
		$oldCharIndex = strpos($original, $letter);

		if ($oldCharIndex !== false){
			if(ctype_upper($input[$i])){
				$output = $output . strtoupper($key[$oldCharIndex]);
			}
			else
				$output = $output . $key[$oldCharIndex];
		}
		else
			$output = $output . $input[$i]; //when there are spaces or punctuations in input
	}
	return $output;
}

function encryptSubstitution($input, $cipherKey){
	$plainAlphabet = "abcdefghijklmnopqrstuvwxyz";
	return substitution($input, $plainAlphabet, $cipherKey);
}

function decryptSubstitution($input, $cipherKey){
	$plainAlphabet = "abcdefghijklmnopqrstuvwxyz";
	return substitution($input, $cipherKey, $plainAlphabet);
}

?>