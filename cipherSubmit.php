<?php
require_once "login.php";
require_once "substitution.php";

$conn = new mysqli($hn,$un,$pw,$db); //get variables from adminlogin.php

if($conn->connect_error)die("There's an error!");//print out an error if the connection is disabled or can't be reached
session_start();
//if cookie has been set from user->sanitize the content otherwise, set the variable to null value
if (isset($_SESSION['name']) ){
  $userName = sanitizeMySQL($conn,$_SESSION["name"]);
  $userEmail = sanitizeMySQL($conn,$_SESSION["email"]);
  $userPassword = sanitizeMySQL($conn,$_SESSION["pass"]);
  
}
else{
    header("location: userLoginForm.php");
    die();
    
}
//create a table for storing uploaded file info from an admin
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
        
    //html form in herdoc
    echo <<<_END
       <html>
        <head><title>PHP Form Upload</title></head>
        <body>
        <form method="post" action="cipherSubmit.php" enctype="multipart/form-data">
        <pre>
    Upload File:  <input type="file"  name="textfile" accept = ".txt" size="10">
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
    Type in the key for the cipher: <input type="text" id="key" name="cipherKey">
    <br>
    <input type = "submit" value="submit" name = "cryptosubmit">
    </pre></form></body></html>

    _END;

    create_table($conn);//call table for storing user's data

    if(isset($_POST["cryptosubmit"])){
        
        if($_FILES["textfile"]["size"]!=0 && !empty($_FILES["textfile"]["tmp_name"]) || isset($_POST["text"]) && "" !=  (trim($_POST['text']))){
           
            
            $textBox = sanitizeMySQL($conn,$_POST["text"] ); //sanitize the input for user's name
            $textFile =   sanitizeMySQL($conn, $_FILES["textfile"]["tmp_name"]);//sanitize the user's uploaded file
            $textFile1 = strtolower(preg_replace("[^A-Za-z0-9.]","", $textFile));//remove any suspicious characters in the file's name
            $contentFile = file_get_contents($textFile1);
            $key = sanitizeMySQL($conn,$_POST["cipherKey"] );
            
            
           // $decrypt = sanitizeMySQL($conn, $_POST["decrypt"]);
            $encrypt = sanitizeMySQL($conn, $_POST["encrdecr"]);
            $cipher = sanitizeMySQL($conn, $_POST["cipher"]);
            //$transposition = sanitizeMySQL($conn, $_POST["transposition"]);
            //$RC4 = sanitizeMySQL($conn, $_POST["rc4"]);
            //$DES = sanitizeMySQL($conn, $_POST["des"]);
            
            if($cipher === "substitution"){
                if(strlen($key)!=26)
                    echo "Please enter a key that has exactly 26 unique characters";
                else{
                    if($encrypt === "1" && isset($_POST["text"]) && $textBox!=""){
                            $stmt = "INSERT INTO userdata(input_text, cipher_method, date_created) VALUES ('$textBox', '$cipher', CURRENT_TIMESTAMP)";
                            $result1 = $conn->query($stmt);
                            if (!$result1) die("   error adding into table");

                            echo "The encrypted form is ";
                            $out = encryptSubstitution($textBox, $key);
                            echo $out;
                    }
                    if($encrypt === "2" && isset($_POST["text"]) && $textBox!=""){
                            $stmt = "INSERT INTO userdata(input_text, cipher_method, date_created) VALUES ('$textBox', '$cipher', CURRENT_TIMESTAMP)";
                            $result1 = $conn->query($stmt);
                            if (!$result1) die("   error adding into table");

                            echo "The decrypted form is ";
                            $out = decryptSubstitution($textBox, $key);
                            echo $out;
                    }
                    if($encrypt === "1" && isset($_FILES["textfile"]) && $contentFile!=""){
                            $stmt = "INSERT INTO userdata(input_text, cipher_method, date_created) VALUES ('$contentFile', '$cipher', CURRENT_TIMESTAMP)";
                            $result1 = $conn->query($stmt);
                            if (!$result1) die("   error adding into table");

                            echo "The encrypted form is ";
                            $out = encryptSubstitution($contentFile, $key);
                            echo $out;
                            //echo " done";
                    }
                    if($encrypt === "2" && isset($_FILES["textfile"]) && $contentFile!=""){
                            $stmt = "INSERT INTO userdata(input_text, cipher_method, date_created) VALUES ('$contentFile', '$cipher', CURRENT_TIMESTAMP)";
                            $result1 = $conn->query($stmt);
                            if (!$result1) die("   error adding into table");

                            echo "The decrypted form is ";
                            $out = decryptSubstitution($contentFile, $key);
                            echo $out;
                    }
                }
            } else if ($cipher === "rc4" && isset($_SESSION['name'])) {
                require_once "RC4.php";
                $key = "qweasd@#!@543,.><{}-sdf?";
                if ($contentFile != "") {
                    $textBox = $contentFile;
                }
                if ($encrypt === "1" && isset($_POST["text"]) && $textBox!="") {
                    $encription = "";
                    $encription = RC4($key, $textBox);
                    echo $encription;
                } else if ($encrypt === "2" && isset($_POST["text"]) && $textBox!="") {
                    $decription = "";
                    $decription = RC4($key, $textBox);
                    echo $decription;
                }
                insert_to_userdata($conn, $textBox, $cipher);
            } 
        }
        else{
            echo "You haven't uploaded the file or your file's content is empty. Please reupload. ";
        }
    }
}

function insert_to_userdata($conn, $textBox, $cipher) {
    $stmt = "INSERT INTO userdata(input_text, cipher_method, date_created) VALUES ('$textBox', '$cipher', CURRENT_TIMESTAMP)";
    $result = $conn->query($stmt);
    if (!$result) die("There are errors");
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

?>
