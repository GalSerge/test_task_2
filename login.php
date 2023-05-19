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
    }
    die();
} else if (isset($_GET['exit']))
{
    logout_user();
    echo get_login_form();
    die();
} else if (isset($_GET['login']))
{
    echo get_login_form();
    die();
} else if (isset($_GET['select']))
{
    $user = get_user_by_login($_GET['select']);

    if ($user != null && auth_user_by_cookie($user))
        echo get_user_page($user);
    else
        echo get_login_form();

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
    } else if ($user['failed_count'] > 5 &&
        $user['last_failed_login'] != null &&
        abs(strtotime($user['last_failed_login']) - time()) / 60 < 1)
    {
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'msg' => 'Повторите попытку позже'));
        die();
    } else if ($user['pass'] != md5($_POST['pass']))
    {
        save_failed_login($user['user_id']);

        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'msg' => 'Неверный пароль'));
        die();
    } else
    {
        clean_failed_login($user['user_id']);
        auth_user($user);
    }

    header('Content-Type: application/json');
    echo json_encode(array('ok' => true, 'msg' => 'Добро пожаловать, ' . $user['name'] . '!'));
} else
{
    header('Content-Type: application/json');
    echo json_encode(array('ok' => false, 'msg' => 'Введите логин и пароль'));
}