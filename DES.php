<?php 
/**
 * Authors : **Kanchanak Khat**, Monica Noori Saghar, Wonyoung Kim, Prerana Sunilkumar,.
 */
class DES {
const IP = array(58, 50, 42, 34, 26, 18,
                10, 2, 60, 52, 44, 36, 28, 20,
                12, 4, 62, 54, 46, 38,
                30, 22, 14, 6, 64, 56,
                48, 40, 32, 24, 16, 8,
                57, 49, 41, 33, 25, 17,
                9, 1, 59, 51, 43, 35, 27,
                19, 11, 3, 61, 53, 45,
                37, 29, 21, 13, 5, 63, 55,
                47, 39, 31, 23, 15, 7);
    //Inverse initial Permutation 
    const IP1 = array(40, 8, 48, 16, 56, 24, 64,
                32, 39, 7, 47, 15, 55,
                23, 63, 31, 38, 6, 46,
                14, 54, 22, 62, 30, 37,
                5, 45, 13, 53, 21, 61,
                29, 36, 4, 44, 12, 52,
                20, 60, 28, 35, 3, 43,
                11, 51, 19, 59, 27, 34,
                2, 42, 10, 50, 18, 58,
                26, 33, 1, 41, 9, 49,
                17, 57, 25 );
    //first key permutation
    const PC1 = array(7, 49, 41, 33, 25,
                17, 9, 1, 58, 50, 42, 34, 26,
                18, 10, 2, 59, 51, 43, 35, 27,
                19, 11, 3, 60, 52, 44, 36, 63,
                55, 47, 39, 31, 23, 15, 7, 62,
                54, 46, 38, 30, 22, 14, 6, 61,
                53, 45, 37, 29, 21, 13, 5, 28,
                20, 12, 4 );
    //second key permutation 
    const PC2 = array(14, 17, 11, 24, 1, 5, 3,
                28, 15, 6, 21, 10, 23, 19, 12,
                4, 26, 8, 16, 7, 27, 20, 13, 2,
                41, 52, 31, 37, 47, 55, 30, 40,
                51, 45, 33, 48, 44, 49, 39, 56,
                34, 53, 46, 42, 50, 36, 29, 32);
    //Expansion d-box
    const EP = array(32, 1, 2, 3, 4, 5, 4,
                5, 6, 7, 8, 9, 8, 9, 10,
                11, 12, 13, 12, 13, 14, 15,
                16, 17, 16, 17, 18, 19, 20,
                21, 20, 21, 22, 23, 24, 25,
                24, 25, 26, 27, 28, 29, 28,
                29, 30, 31, 32, 1 );
    //Straight Permutation table
    const P = array(16, 7, 20, 21, 29, 12, 28,
               17, 1, 15, 23, 26, 5, 18,
               31, 10, 2, 8, 24, 14, 32,
               27, 3, 9, 19, 13, 30, 6,
               22, 11, 4, 25);
    const s_box = array(
                array(
                    array(14, 4, 13, 1, 2, 15, 11, 8, 3, 10, 6, 12, 5, 9, 0, 7),
                    array(0, 15, 7, 4, 14, 2, 13, 1, 10, 6, 12, 11, 9, 5, 3, 8),
                    array(4, 1, 14, 8, 13, 6, 2, 11, 15, 12, 9, 7, 3, 10, 5, 0),
                    array(15, 12, 8, 2, 4, 9, 1, 7, 5, 11, 3, 14, 10, 0, 6, 13)),
                array(
                    array(15, 1, 8, 14, 6, 11, 3, 4, 9, 7, 2, 13, 12, 0, 5, 10),
                    array(3, 13, 4, 7, 15, 2, 8, 14, 12, 0, 1, 10, 6, 9, 11, 5),
                    array(0, 14, 7, 11, 10, 4, 13, 1, 5, 8, 12, 6, 9, 3, 2, 15),
                    array(13, 8, 10, 1, 3, 15, 4, 2, 11, 6, 7, 12, 0, 5, 14, 9)),
                array(
                    array(10, 0, 9, 14, 6, 3, 15, 5, 1, 13, 12, 7, 11, 4, 2, 8),
                    array(13, 7, 0, 9, 3, 4, 6, 10, 2, 8, 5, 14, 12, 11, 15, 1),
                    array(13, 6, 4, 9, 8, 15, 3, 0, 11, 1, 2, 12, 5, 10, 14, 7),
                    array(1, 10, 13, 0, 6, 9, 8, 7, 4, 15, 14, 3, 11, 5, 2, 12)),
                array(
                    array(7, 13, 14, 3, 0, 6, 9, 10, 1, 2, 8, 5, 11, 12, 4, 15),
                    array(13, 8, 11, 5, 6, 15, 0, 3, 4, 7, 2, 12, 1, 10, 14, 9),
                    array(10, 6, 9, 0, 12, 11, 7, 13, 15, 1, 3, 14, 5, 2, 8, 4),
                    array(3, 15, 0, 6, 10, 1, 13, 8, 9, 4, 5, 11, 12, 7, 2, 14)),
               array(
                   array(2, 12, 4, 1, 7, 10, 11, 6, 8, 5, 3, 15, 13, 0, 14, 9),
                   array(14, 11, 2, 12, 4, 7, 13, 1, 5, 0, 15, 10, 3, 9, 8, 6),
                   array(4, 2, 1, 11, 10, 13, 7, 8, 15, 9, 12, 5, 6, 3, 0, 14),
                   array(11, 8, 12, 7, 1, 14, 2, 13, 6, 15, 0, 9, 10, 4, 5, 3)),
              array(
                  array(12, 1, 10, 15, 9, 2, 6, 8, 0, 13, 3, 4, 14, 7, 5, 11),
                  array(10, 15, 4, 2, 7, 12, 9, 5, 6, 1, 13, 14, 0, 11, 3, 8),
                  array(9, 14, 15, 5, 2, 8, 12, 3, 7, 0, 4, 10, 1, 13, 11, 6),
                  array(4, 3, 2, 12, 9, 5, 15, 10, 11, 14, 1, 7, 6, 0, 8, 13)),
              array(
                  array(4, 11, 2, 14, 15, 0, 8, 13, 3, 12, 9, 7, 5, 10, 6, 1),
                  array(13, 0, 11, 7, 4, 9, 1, 10, 14, 3, 5, 12, 2, 15, 8, 6),
                  array(1, 4, 11, 13, 12, 3, 7, 14, 10, 15, 6, 8, 0, 5, 9, 2 ),
                  array(6, 11, 13, 8, 1, 4, 10, 7, 9, 5, 0, 15, 14, 2, 3, 12)),
              array(
                  array(13, 2, 8, 4, 6, 15, 11, 1, 10, 9, 3, 14, 5, 0, 12, 7),
                  array(1, 15, 13, 8, 10, 3, 7, 4, 12, 5, 6, 11, 0, 14, 9, 2),
                  array(7, 11, 4, 1, 9, 12, 14, 2, 0, 6, 10, 13, 15, 3, 5, 8),
                  array(2, 1, 14, 7, 4, 10, 8, 13, 15, 12, 9, 0, 3, 5, 6, 11))
            );
    const shiftBits = array( 1, 1, 2, 2, 2, 2, 2, 2,
                        1, 2, 2, 2, 2, 2, 2, 1);
    // hexadecimal to binary conversion
    function hextoBin($input)
    {
        $n = strlen($input) *4;
        $input = base_convert($input, 16,2);
        while(strlen($input) < $n)
            $input .= "0".$input;
        return $input;
        
    }
    
    // binary to hexadecimal conversion
    function binToHex($input)
    {
        $n = (int)strlen($input)/4;
        $input = base_convert($input,2,16);
        while(strlen($input) < $n)
            $input = "0". $input;
        return $input;
        
    } 
     //pre-mutate input hexadecimal accordng to specified sequence
    function permutation($sequence, $input){
        $output = "";
        $input = $this->hextoBin($input);
        for($i =0; $i<sizeof($sequence); $i++){
            $seq = $sequence[$i]-1;
            $output .= $input[$seq];
        }
        $output = $this->binToHex($output);
        return $output;
           
    }
    function doXor($a, $b){
        $t_a = base_convert($a, 16, 10);//hexadecimal to decimal
        $t_b = base_convert($b, 16, 10);
        
        $t_a = $t_a ^ $t_b;
        $a =base_convert($t_a,10,16);
        
        while(strlen($a) < strlen($b))
            $a = "0".$a;
        return $a;     
    }
    
    function leftCircularShift($input, $numBits){
        $n = strlen($input)*4;
        $perm = new SplFixedArray($n);
        for($i=0 ; $i < $n-1 ; $i++){
            
            $perm[$i] = ($i+2);
        }
        $perm[$n-1] = 1;
        while($numBits-- > 0)
            $input =  $this->permutation($perm, $input);
        return $input;
    }
    function getKeys($key){
        $totalRound = 16;
        $keys = new SplFixedArray($totalRound);
        
        $key = $this->permutation(self::PC1, $key, 56);
        for($i = 0; $i<$totalRound ; $i++){
            
            $key = $this->leftCircularShift(substr($key,0,7), self::shiftBits[$i])
                    .$this->leftCircularShift(substr($key, 7, 14), self::shiftBits[$i]);
            $keys[$i] = $this->permutation(self::PC2, $key,48);
        }
        return $keys;
    }
    function sBox($input){
        
        $output = "";
        $input = base_convert($input, 16, 10);
        $numberOfKeys = 48;
        for($i=0; $i<$numberOfKeys; $i += 6){
            
            $temp = substr($input, $i, $i+6);
            $num = $i /6;
            $row = intVal($temp[0]."".$temp[5], 2);
            $col = intVal(substr($temp,1,5),2);
            $output .= dechex(self::sBox[$num][$row][$col]);
            
        }
        return $output;
    }
    function DESRound($input, $key,$num){
        
        $left = substr($input,0,8);
        $temp = substr($input,8,16);
        $right = $temp;
        //Expansion permutation
        $temp = $this->permutation(self::EP,$temp);
        //xor temp and round key
        $temp = $this->doXor($temp,$key);
        echo "Round:".($num +1) . " ".strtoupper($right)." ".strtoupper($left)." ".strtoupper($key)."<br>";
        return $right.$left;
    }
    function encrypt($plainText, $key){
        $i;
        $keys = $this->getKeys($key); //get round keys
        
        $plainT = $this->permutation(self::IP, $plainText);
        echo "After initial permutation: ".strtoupper($plainText). "<br>";
        echo "<br>";
        echo "After splitting: L0 = ".strtoupper(substr($plainT,0, 8))." ". "R0=".strtoupper(substr($plainT,8,16)). "<br>";        
        
        $totalRound = 16;
        //16 rounds
        for($i=0; $i<$totalRound; $i++){
            $plainT = $this->DESRound($plainT, $keys[$i], $i);
        }
        
        $plainT = substr($plainT, 8,16).substr($plainT,0,8);
        $plainT = $this->permutation(self::IP1, $plainT);
        echo "<br>";
        return "Result of your ciphertext: ". strtoupper($plainT);
        
    }
    function decrypt($plainText, $key){
        $i;
        $keys = array($this->getKeys($key)); //get round keys
        
        $plainT = $this->permutation(self::IP, $plainText);
        echo "After initial permutation: ".strtoupper($plainText)."<br>";
        echo "<br>";
        echo "After splitting: L0 = ".strtoupper(substr($plainT,0, 8))." "."R0=".strtoupper(substr($plainT,8,16)). "<br>";
        
        $numRound =16;
        //16 rounds
        for($totalRound=16; $totalRound > 0; $totalRound--){
            $plainT = $this->DESRound($plainT, $keys[$totalRound], $numRound-$totalRound);
        }
        
        $plainT = substr($plainT, 8,16).substr($plainT,0,8);
        $plainT = $this->permutation(self::IP1, $plainT);
        echo "<br>";
        return "Result of your plainText: " . strtoupper($plainT);
        
    }
}
    
       
     
 ?>