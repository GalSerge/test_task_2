<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include('config.php');
include('functions.php');

session_start();
unset($_SESSION['user']);
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div id="page">

<?
if (!isset($_SESSION['user'])
//&& !auth_user_by_cookie()
)
{

        echo '
        <form id="login-form" method="post">
            <input type="text" name="login" placeholder="Логин">
            <input type="password" name="pass" placeholder="Пароль">
            <button type="button" onclick="auth_user()">Войти</button>
        </form>

    ';

} else
{
    $user = get_user_by_login($_SESSION['user']['login']);
    echo get_user_page($user);
}
?>

</div>
<script src="script.js"></script>
</body>
</html>