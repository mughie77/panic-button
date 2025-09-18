<?php
// A temporary script to generate password hashes.
// This should be deleted after use.

$guru_password = 'guru123';
$siswa_password = 'siswa123';

$guru_hash = password_hash($guru_password, PASSWORD_DEFAULT);
$siswa_hash = password_hash($siswa_password, PASSWORD_DEFAULT);

echo "Guru Hash: " . $guru_hash . "\n";
echo "Siswa Hash: " . $siswa_hash . "\n";
?>
