<?php

require_once "login.php";

$conn = new mysqli($hn,$un,$pw,$db); //get variables from adminlogin.php

if($conn->connect_error)die("There's an error!");//display an error if connection to database is not enabled

session_start();
//login function for contributors
function user_login($conn){
    
    echo <<<_END
    <html>
    <head><title>PHP Form Signin</title></head>
    <body>
    <form method="post" action="userLoginForm.php" enctype="multipart/form-data">
    <table align="center">
<tr>
    <th colspan="2"><h2 align="center">Login</h2></th>
</tr>
<tr>
    <td>Username:</td>
    <td><input type="text" name="uname"></td>
</tr>
<tr>
    <td>Email:</td>
    <td><input type="text" name="email"></td>
</tr>
<tr>
    <td>Password:</td>
    <td><input type="password" name="pwd"></td>
</tr>
<tr>
    <td align="center" colspan="2"><input type="submit" name="login" value="login"></td>
</tr>


_END;

    button_goback();
    //if login is set-> username, email and password must be set and not left empty
    if(isset($_POST["login"])){
        if(isset($_POST["uname"]) && isset($_POST["email"]) && isset($_POST["pwd"])){
            
            if("" !=  ((trim($_POST['uname'])) && (trim($_POST['email'])) && (trim($_POST['pwd'])))){
                
                $user_name = sanitizeMySQL($conn,$_POST["uname"]);//sanitize username
                $user_email = sanitizeMySQL($conn,$_POST["email"]);//sanitize user's email
                $user_pwd = sanitizeMySQL($conn,$_POST["pwd"]);//sanitize user's password
                //check if the database has any non-empty rows
                $stmt = $conn->prepare("SELECT * FROM userinfo WHERE user_name = ?");
                $stmt->bind_param('s', $user_name);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
               
                if($result){
                    $row_cnt= $result->num_rows;
                    
                    if($row_cnt > 0){ 
                        //for($j=0; $j<$row_cnt; ++$j){
                
                         //$result->data_seek($j);
                         $row = $result->fetch_array(MYSQLI_NUM);
                         //verify password that the username's type in with the one in the database
                         //redirect to the next page if the password is correct
                        if(password_verify($user_pwd, $row[2]) && $user_name == $row[0] && $user_email == $row[1]){
                           $_SESSION['name'] = $user_name;
                           $_SESSION['email'] = $user_email;
                           $_SESSION['pass'] = $user_pwd;
                            
                            header("location: userUpload.php");
                            die();
                            
                        }
                    }
                        
                     else{
                            die("Invalid username /Invalid email/ password combination");
                            
                     }
                        
                       
                    
                    //else{
                        //die("Sorry! No way you can access that~ XD");
                    //}
                }
                $stmt->close();
                $conn->close();
            }
            else{//if the user does not provide any input after submitting, print an error statement
                echo "It's required to type in all your inputs first!";}
                
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

//Functions to sanitize values from contributors

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
user_login($conn);
?>
