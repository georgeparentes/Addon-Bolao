<?php
$Host = "127.0.0.1";
$user = "root";
$pass = "vertrigo";
$db_name = "mkradius";
$connection = mysqli_connect($Host, $user, $pass, $db_name);
if (mysqli_connect_errno()) die("Conexao falhou: " . mysqli_connect_error());
