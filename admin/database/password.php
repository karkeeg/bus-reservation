<?php
$adminPassword = password_hash("kermi", PASSWORD_BCRYPT);
$userPassword = password_hash("gelek", PASSWORD_BCRYPT);

echo "Admin Password Hash: " . $adminPassword . "\n";
echo "User Password Hash: " . $userPassword . "\n";
?>
