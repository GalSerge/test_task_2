<?
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

include('config.php');
include('functions.php');

session_start();
//unset($_SESSION['user']);
//unset($_COOKIE['users']);

//var_dump($_COOKIE);
//var_dump($_SESSION['user']);
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
<div id="page" class="card">

<?
if (!isset($_SESSION['user'])
&& !auth_active_user_by_cookie()
)
{
    echo get_login_form();
} else
{
    $user = get_user_by_login($_SESSION['user']['login']);
    echo get_user_page($user);
}
?>

</div>
<div id="welcome"><span>Привет!</span></div>
<script src="script.js"></script>
</body>
</html>