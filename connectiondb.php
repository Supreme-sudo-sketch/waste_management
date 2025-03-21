<?php
//connect to waste
$server = "localhost";
       $serveruseraccount = "root";
       $serveruserpassword = "";
       $db = "waste";

       //establish connection
       $connect = mysqli_connect($server, $serveruseraccount , $serveruserpassword, $db);
       if(!$connect){
       	die(mysqli_connect_error($connect));
       }