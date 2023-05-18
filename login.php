<?

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include('config.php');
include('functions.php');


session_start();

if (isset($_GET['page']))
{
    if (isset($_SESSION['user']))
    {
        $user = get_user_by_login($_SESSION['user']['login']);
        echo get_user_page($user);
    } else
{
    echo '1';
}
    die();
}

if (isset($_POST['login']) && isset($_POST['pass']) && $_POST['login'] != '' && $_POST['pass'] != '')
{
    $user = get_user_by_login($_POST['login']);

    if ($user == null)
    {
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'msg' => 'Неверный логин'));
        die();
    } else if ($user['pass'] != md5($_POST['pass']))
    {
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'msg' => 'Неверный пароль'));
        die();
    } else
        auth_user($user);

    header('Content-Type: application/json');
    echo json_encode(array('ok' => true, 'msg' => 'Добро пожаловать, ' . $user['name'] . '!'));
} else
{
    header('Content-Type: application/json');
    echo json_encode(array('ok' => false, 'msg' => 'Введите логин и пароль'));
}