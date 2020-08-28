<?php
require_once "login.php";

$conn = new mysqli($hn,$un,$pw,$db); //get variables from adminlogin.php

if($conn->connect_error)die("There's an error!");//display an error if connection to database is not enabled

//create a credential table for contributors
function create_table($conn){
    
    $table = "CREATE TABLE userinfo (
          user_name VARCHAR(150) NOT NULL UNIQUE,
          email VARCHAR(150) NOT NULL UNIQUE,
          password VARCHAR(500) NOT NULL)";
    
    $output = $conn->query($table);//store $table into the object property query
    if($conn->connect_error)echo "There's an error!";//print an error
    
}
//form registration for contributors
function user_form_register($conn){
    //html form in heredoc
    echo <<<_END
   <html>
    <head><title>PHP Form Signin</title></head>
    <body> 
    <form method="post" action="userRegisterForm.php" enctype="multipart/form-data">
    <table align="center">
<tr>
    <th colspan="2"><h2 align="center">Signup</h2></th>
</tr>
<tr>
    <td>Username:</td>
    <td><input type="text" name="username"></td>
</tr>
<tr>
    <td>Email:</td>
    <td><input type="text" name="email"></td>
</tr>
<tr>
    <td>Password:</td>
    <td><input type="password" name="password"></td>
</tr>
<tr>
    <td align="center" colspan="2"><input type="submit" name="signup" value="signup"></td>
</tr>

_END;
    
    button_goback();
    create_table($conn);
    
    //if signup is set-> username, email and password must be set also and must not be left empty
    //username can contain English letters (capitalized or not), digits, and the characters '_' (underscore) and '-' (dash). Nothing else.
    if(isset($_POST["signup"])){
                
        if(isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])){
            
            if("" !=  ((trim($_POST['username'])) && (trim($_POST['email'])) && (trim($_POST['password'])))){                
                $username = sanitizeMYSQL($conn, $_POST["username"]);//sanitize username input
                $email = sanitizeMYSQL($conn,$_POST["email"]);//sanitize user's email 
                $passw = sanitizeMySQL($conn,$_POST["password"]);//sanitize contributor's password
                $userpassw = password_hash($passw, PASSWORD_ARGON2I);//hash password with salt
                if(preg_match("(^[A-Za-z0-9-_]{1,}$)", $username) && filter_var($email, FILTER_VALIDATE_EMAIL)){//insert those inputs into the database
                         
                    $stmt = $conn->prepare("SELECT `user_name`, `email` FROM `userinfo` WHERE `user_name` = ? OR `email` = ?");
                    $stmt->bind_param('ss', $username,$email);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if(mysqli_num_rows($result) > 0){
                     $row = $result->fetch_array(MYSQLI_NUM);
                        if($username == $row[0] || $email==$row[1]){
                            echo "The username or email already exist";
                            $stmt->close();
                            $conn->close(); 
                       }
                       
                    }
                    else{
                          
                            $stmt1 = $conn->prepare('INSERT INTO userinfo VALUES(?,?,?)');//insert values into the table using prepare statement
                            $stmt1->bind_param('sss', $username, $email, $userpassw);
                            $stmt1->execute();
                            echo "Register successfully!";
                           
                            $stmt->close();
                            $stmt1->close();
                            $conn->close(); 
                            
                        }
                }
                    
                else{
                      
                     echo "Invalid username or email. You are allowed only to have letters (capitalized or not), digits, hypens and underscore for username\n";
                                                     
    
                }
                                  
                
                }
               
            else{
             echo "it is required to type in all of your inputs first before registeration!";
            }
        }
        
    }
    
}

function button_goback(){
    echo <<<_END
<tr>
    <td align="center" colspan="2"><input type="submit" name="back" value="Go back"></td>
</tr>

 </table></form></body></html>
_END;
    if(isset($_POST["back"])){
        header("location: userPage.php");
        die();
    }
    
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
user_form_register($conn);

?>
    