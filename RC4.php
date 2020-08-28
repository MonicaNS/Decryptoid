<?php
/**
 * Authors : Wonyoung Kim, Monica Noori Saghar , Kanchanak Khat, Prerana Sunilkumar.
 */
// $key = "FabioIstheBest";
// $input = "wonyoungkim";
 
// $en= RC4($key,$input);
// $de = RC4($key,$en);
 
// echo 'Encrypted: '.bin2hex($en).'<br> Decrypted: '.$de.'';
 
function RC4($key, $input) {
    //initialize
	$ary = array();
	for ($i = 0; $i < 256; $i++) {
        //permutation of 0,1,...,255
		$ary[$i] = $i;
	}
	$j = 0;
	for ($i = 0; $i < 256; $i++) {
        $size_key = strlen($key);
		$j = ($j + $ary[$i] + ord($key[$i % $size_key])) % 256;
        swap($ary[$i], $ary[$j]);
    }
    
    //generate stream
	$i = 0;
	$j = 0;
    $output = '';
    $size_input = strlen($input);
    //first 256 bytes should be discarded otherwise related attack exist
	for ($y = 0; $y < $size_input; $y++) {
		$i = ($i + 1) % 256;
        $j = ($j + $ary[$i]) % 256;
        $pick_i = $ary[$i];
        $pick_j= $ary[$j];
        //swap element in table and select byte
        swap($pick_i, $pick_j);
		$output .= $input[$y] ^ chr($ary[($pick_i + $pick_j) % 256]);
	}
	return $output;
}
function swap ($a, $b) {
    $temp = $a;
    $a = $b;
    $b = $temp;
}
?>
