<?php
session_start();
session_unset();
session_destroy();
header('Location: ../../telas/login.php');
exit;
