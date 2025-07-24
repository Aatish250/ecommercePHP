<?php
function verify_user($role_for, $redirect_link){
    // echo "<br> role_for: $role_for";
    // echo "<br> rdeirect_link: $redirect_link";
    // echo "<br>session user_id: ".$_SESSION['user_id'];
    // echo "<br>session role: ".$_SESSION['role'];
    
    if($_SESSION['role'] !== $role_for){
        echo "<br>(1) not equal to role => " . $_SESSION['role'] . " === $role_for";
        header("Location: $redirect_link");
        exit();
    }
}
?>