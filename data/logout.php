<?php
session_start();
session_destroy();
header('Location: ../Door/login.php');
exit;
