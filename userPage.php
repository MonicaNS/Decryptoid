<?php  
    //html heredoc
    echo <<<_END
   <html>
    <head><title>PHP Form Upload</title></head>
    <body>
    <form method="post" action="userPage.php" enctype="multipart/form-data">
    <pre>
<table align="center">
<tr>
    <th colspan="2" style="border-bottom:2px solid black"><h2 align="center" outline-style= "dotted">Welcome to Cipher 174!</h2></th>

</tr>
    
<tr>
    <td align="center" width="150px" margin="0 auto"><input type="submit" name="userregister" value="register?"></td>
    <td align="left" colspan="2"><input type="submit" name="userlogin" value="login?"></td>
</tr>

</table></pre></form></body></html>
_END;
    
    
    if(isset($_POST["userregister"])){ //if the user wants to register -> redirect to another page
        header("Location: userRegisterForm.php"); 
        die();
    }
    if(isset($_POST["userlogin"])){ //if the user wants to login -> redirect to another page
        header("Location: userLoginForm.php");
        die();
    }

          
?>
