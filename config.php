<?
$db_name = 'admin_serge_db';
$db_username = 'admin_serge';
$db_password = 'df4zRgm3fjw';

try {
    $db = new PDO('mysql:host=localhost;dbname='.$db_name.';charset=utf8;',
        $db_username,
        $db_password);
} catch (PDOException $e) {
    die();
}