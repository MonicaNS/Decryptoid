<?php
require_once "login.php";
require_once "substitution.php";
require_once "DoubleTransposition.php";
require_once "DES.php";
require_once "RC4.php";


$conn = new mysqli($hn,$un,$pw,$db); //get variables from login.php

if($conn->connect_error)die("There's an error!");//print out an error if the connection is disabled or can't be reached

ini_set('session.use_only_cookies', 1);//use a mixture of session and cookies 
ini_set('session.save_path', '\xampp\tmp\myaccount\session'); //store user's sessions into the custom path
session_start();
// check for a special session variable that you arbitrarily invent
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = 1;
}
//this is correct and can be acessed unless your username is correct and your browser types, ip address, versions, and computer platforms belong to you
if (isset($_SESSION['name']) &&
    ($_SESSION['check'] == hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']))) {
        
//create a table for storing uploaded file info from a user
function create_table($conn){
    
    $table = "CREATE TABLE userdata (
          input_text LONGTEXT NOT NULL,
          cipher_method VARCHAR(50) NOT NULL,
          date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)";
    
    $output = $conn->query($table);//store $table into the object property query
    if($conn->connect_error)echo "There's an error!";//print an error
    
}
//create a form where user can submit their input
function cipher_submission($conn){
    
//html form in heredoc
echo <<<_END
   <html>
    <head><title>PHP Form Upload</title></head>
    <body>


    <form method="post" action="cipherSubmit.php" enctype="multipart/form-data">
    <pre>
Upload File:  <input type="file"  name="textfile" accept = ".txt" size="10" id="upload">
              <p><b> OR</b></p>
<label vertical-align = "middle">Input text:</label>   <textarea name="text" rows="5" cols="30"></textarea>
<br>
<label>Select encryption/decryption:</label> <select name="encrdecr" id="cipher">
    <option name="none" value="0" disabled>Choose An Option</option>
    <option name ="encrypt" value="1">Encryption</option>
    <option name ="decrypt" value="2">Decryption</option>
</select>
<br>
<label>Select your cipher methods:</label> <select name="cipher" id="cipher">
    <option name="none" value="0" disabled>Choose An Option</option>
    <option  value="substitution">Simple Substitution</option>
    <option  value="transposition">Double Transposition</option>
    <option  value="rc4">RC4</option>
    <option value="des">DES</option>
</select>
<br>
Type in your Key: <input type="text" id="key" name="userkey">
<p style="font-weight:bold;">NOTE: For RC4, there is no need to put your input key! </p>
<p style="font-weight:bold;">NOTE: For DES, please put your text and key as long as possible! </p>
<br>
<input type = "submit" value="submit" name = "cryptosubmit">
<br>
<input type = "submit" value="logout" name = "logout">
<p style="font-weight:bold;">Important: We are implementing cookies for best improvement in service.</p>
</pre></form></body></html>

_END;

create_table($conn);//call table for storing user's data

if(isset($_POST["cryptosubmit"])){
   
    //sanitize the selection from users
    $encrypt = sanitizeMySQL($conn, $_POST["encrdecr"]);
    $decrypt = sanitizeMySQL($conn, $_POST["encrdecr"]);
    $cipher = sanitizeMySQL($conn, $_POST["cipher"]);
    
   if((isset($_FILES["textfile"])) || (isset($_POST["text"]))){

       if(($_FILES["textfile"]["size"])!=0 && !empty($_FILES["textfile"]["tmp_name"])){
           
           $textFile =  sanitizeMySQL($conn, $_FILES["textfile"]["tmp_name"]);//sanitize the user's uploaded file
           $textFile1 = strtolower(preg_replace("[^A-Za-z0-9.]","", $textFile));//remove any suspicious characters in the file's name
           $userInput = file_get_contents($textFile1);
           $userKey = sanitizeMySQL($conn, $_POST["userkey"]); //sanitize user's key input
              
        }
         else if("" !=  (trim($_POST['text']))){
        
         $userInput = sanitizeMySQL($conn,$_POST["text"] ); //sanitize the input for user's name
         $userKey = sanitizeMySQL($conn, $_POST["userkey"]); //sanitize user's key input
     }
     else{
         
         echo "Please upload your file or put in your text input.";
         
     }
    }
     else{
         
         echo "You cannot have two string inputs at the same time!!! You Too bad you can't choose :P ";
         
     }
       
           
        $timeRecord = date('Y-m-d H:i:s');
       
                 
        if($cipher === "substitution"){
            if(strlen($userKey)!=26){
                echo "Please enter a key that has exactly 26 unique characters";}
            else{
                if($encrypt === "1"){
                                         
                    echo "The encrypted form is: ";
                    $out = encryptSubstitution($userInput, $userKey);
                    echo $out;
                    funny_stuff();
                    
                }
                if($decrypt === "2"){
              
                    echo "The decrypted form is: ";
                    $out = decryptSubstitution($userInput, $userKey);
                    echo $out;
                    funny_stuff1();
                         
                }
               
            }  
            table_insertion($conn, $userInput,$cipher,$timeRecord);         
    }
    
  

    else if($cipher === "transposition"){
        
        $ob = new DoubleTransposition();
        if($encrypt === "1" ){
            
            echo "The encrypted form is: ";
            $output = $ob->encrypt($userKey,"456ABCDEFGHIJKLMNOP7635",$userInput);
            
            echo $output;
            funny_stuff();
            
        }
        if($decrypt === "2"){
     
            echo "The decrypted form is: ";
            $output = $ob->decrypt("456ABCDEFGHIJKLMNOP7635",$userKey,$userInput);
            echo $output;
            funny_stuff1();
        }
        table_insertion($conn, $userInput,$cipher,$timeRecord);
        
        
    }
    
    
   else if($cipher === "des"){ 
       $ob = new DES();
            if($encrypt === "1"){
                echo "The encrypted form is: ";
                echo "<br>";
                $output = $ob->encrypt($userInput, $userKey);
                echo $output;
                funny_stuff();
                
        }
             if($decrypt === "2"){
                 echo "The decrypted form is:  ";
                 echo "<br>";
                 $output1 = $ob->decrypt($userInput, $userKey);
                 echo $output1;
                 funny_stuff1();
                 
                
        }
        table_insertion($conn, $userInput,$cipher,$timeRecord);
    }
    
    else if ($cipher === "rc4") {
        $ob = new RC4();
        
        $key = "qweasd@#!@543,.><{}-sdf?";
        
        if ($encrypt === "1") {
            
            echo "The encrypted form is ";
            $encryption = $ob->rc4cipher($key, $userInput);
            echo $encryption;
            funny_stuff();
            
        } else if ($decrypt === "2") {
            
            echo "The decrypted form is ";
            $decryption = $ob->rc4cipher($key, $userInput);
            echo $decryption;
            funny_stuff1();
        }
        table_insertion($conn, $userInput, $cipher,$timeRecord);
    }
}
//redirect to the main page after the user logs out -> destroy sessions before that
if(isset($_POST["logout"])){
    ini_set('session.use_only_cookies', 1);
    ini_set('session.save_path', '\xampp\tmp\myaccount\session');
    destroy_session_and_data();
    header("Location: userPage.php");
    die();
    
    
}

}
function funny_stuff(){
    echo "<br>";
    echo "<br>";
    echo "Ahhhh! What are you going to do with this encrypted text hmmm....i am judging you...JK teehee";
    echo "..... sto morendo";
    
}
function funny_stuff1(){
    echo "<br>";
    echo "<br>";
    echo "Ahhh! here we go! You are decrypting this text for what reason?...I am doubting you...ah you want to be a genius but you can't that's why you are using this applicaiton. whatever it is, good luck! \(^_^)/ ";
    
}
//table insertion for all ciphers
function table_insertion($conn, $userInput,$cipher,$timeRecord){
    
    $stmt = $conn->prepare('INSERT INTO userdata (`input_text`,`cipher_method`,`date_created`) VALUES(?,?,?)');//insert values into the table using prepared statement
    $stmt->bind_param('sss', $userInput, $cipher, $timeRecord);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
}
//function to destroy users' session
function destroy_session_and_data() {
    session_start();
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

//functions for sanitization
function sanitizeMySQL($conn, $var){
    $var = $conn->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}

function sanitizeString($var){
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

cipher_submission($conn);
    }
else{ // if the sessions id is not correct or your ip address, browser types or computer platform does not belong to you -> have user relogin
    echo<<<_END
    <html> 
    <body>
    <form method="post" action="cipherSubmit.php" enctype="multipart/form-data">
    <pre>
<input type = "submit" value="login again?" name = "login">
</pre></form></body></html>
_END;
    if(isset($_POST["login"])){
        header("location: userLoginForm.php");
        die();
    }
}

?>
