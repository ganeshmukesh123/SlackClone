<?php

    include("connectToDB.php");

    $allSetToGo = true;

    //loginSignup action
    if(isset($_GET['actions'])){

        if($_GET['actions'] == "loginSignup"){

            $error = "";

            if($_POST['loginActive'] == "0"){
                if(!$_POST['username']){
                    $error = "An username is required";
                }
            }
            
            //if email field is empty
            if(!$_POST['email']){
                
                $error = "An email address is required";
            
            //if password field is empty
            }else if(!$_POST['password']){
                
                $error = "A password is required";
            
            //if not a valid email is used
            }else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                
                $error = "Please enter a valid email address";
                
            }
            
            //discontinue if error is there
            if($error != ""){
                echo $error;
                exit();
            }
            
            //if user wants to sign up
            if($_POST['loginActive'] == "0"){
                
                $query = "SELECT * FROM users WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                if(mysqli_num_rows($result) > 0){
                    $allSetToGo = false;
                    $error = "That email address is already taken";
                    echo $error;
                }
                $query = "SELECT * FROM users WHERE username = '".mysqli_real_escape_string($link, $_POST['username'])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                if(mysqli_num_rows($result) > 0){
                    $allSetToGo = false;
                    $error = "That username address is already taken";
                    echo $error;
                }
                if($allSetToGo){
                    $query = "INSERT INTO users (`username`, `email`, `password`) VALUES('".mysqli_real_escape_string($link, $_POST['username'])."','".mysqli_real_escape_string($link, $_POST['email'])."','".mysqli_real_escape_string($link, $_POST['password'])."')";
                    if(mysqli_query($link, $query)){
                        $_SESSION['id'] = mysqli_insert_id($link);
                        $query = "UPDATE users SET password = '".md5(md5($_SESSION['id']).$_POST['password'])."' WHERE id = '".$_SESSION['id']."' LIMIT 1";
                        mysqli_query($link, $query);
                        echo 1;
                    }else{
                        $error = "Sorry, our bad! Couldn't add ya, Please try again later";
                    }
                }
            //if user wants to log in  
            }else{
                $query = "SELECT * FROM users WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_assoc($result);
                if($row['password'] == md5(md5($row['id']).$_POST['password'])){
                    echo 1;
                    $_SESSION['id'] = $row['id'];
                }else{
                    $error = "Sorry, could not find this user. Did you enter the correct login credentials?";
                }
            }
            
            if($error != ""){
                echo $error;
                exit();
            }
            
        }

        if($_GET['actions'] == "createTeam"){

            $query = "SELECT * FROM teamdetails WHERE teamname = '".mysqli_real_escape_string($link, $_POST['teamname'])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                if(mysqli_num_rows($result) > 0){
                    $allSetToGo = false;
                    $error = "That Team Name is already taken";
                    echo $error;
                }else{
                    $query = "INSERT INTO teamdetails (`teamadmin`, `teamname`, `teampassword`) VALUES('".mysqli_real_escape_string($link, $_POST['teamadmin'])."','".mysqli_real_escape_string($link, $_POST['teamname'])."','".mysqli_real_escape_string($link, $_POST['teampassword'])."')";
                    if(mysqli_query($link, $query)){
                        $_TEAMSESSION['id'] = mysqli_insert_id($link);
                        $query = "UPDATE teamdetails SET teampassword = '".md5(md5($_TEAMSESSION['id']).$_POST['teampassword'])."' WHERE id = '".$_TEAMSESSION['id']."' LIMIT 1";
                        mysqli_query($link, $query);
                        echo 1;
                    }else{
                        $error = "Sorry, our bad! Couldn't add your team, Please try again later";
                    }
                }

        }

    }

    //if user presses 'logout' button then unset the session
    if (isset($_GET['function'])) {
        if($_GET['function'] == "logout"){
            session_unset();
            if(isset($_TEAMSESSION))
                unset($_TEAMSESSION);
            header('Location: http://localhost/SlackClone'); 
        }

        if($_GET['function'] == "teamLogout"){
            if(isset($_TEAMSESSION))
                unset($_TEAMSESSION);
            header('Location: http://localhost/SlackClone/dash.php'); 
        }
    }

?>
