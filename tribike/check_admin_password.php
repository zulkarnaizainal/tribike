<?php
/**
 * Script to check admin password
 * This will try common passwords against the hash
 */
$hash = '$2b$12$xJW70Oz/fQesP/PyxdJ4OuHJ4/r6jQqZNNCvy9jtGXwp1GMy7.hqy';

$passwords_to_try = [
    'admin',
    'password',
    'admin123',
    '123456',
    'password123',
    'Admin123',
    'admin@123',
    'admin1234',
    'Admin',
    'PASSWORD',
    'tribike',
    'Tribike123',
    'admin@example.com',
    '12345678',
    'qwerty'
];

echo "Checking password hash: " . substr($hash, 0, 20) . "...\n\n";

$found = false;
foreach($passwords_to_try as $pwd) {
    if(password_verify($pwd, $hash)) {
        echo "✅ FOUND! Password is: " . $pwd . "\n";
        $found = true;
        break;
    } else {
        echo "❌ NOT: " . $pwd . "\n";
    }
}

if (!$found) {
    echo "\n⚠️ Password tidak dijumpai dalam senarai password biasa.\n";
    echo "Password mungkin:\n";
    echo "- Custom password yang tidak diketahui\n";
    echo "- Sila gunakan reset_admin_password.php untuk reset password\n";
}


