<?

function auth_user($user)
{
    $_SESSION['user'] = array('login' => $user['login'], 'user_id' => $user['user_id']);

    if (isset($_COOKIE['users']))
    {
        $cookie_data = json_decode($_COOKIE['users'], true);
        foreach ($cookie_data as $i => $user_data)
            $cookie_data[$i]['active'] = false;

        $cookie_data[$user['login']] = array(
            'active' => true,
            'key' => $user['secret_key']);
    } else
        $cookie_data = array($user['login'] => array(
            'active' => true,
            'key' => $user['secret_key']));

    setcookie('users', json_encode($cookie_data), time()+60*60*24*30);
}

function get_user_by_login($login)
{
    global $db;

    $result = null;

    $stmt = $db->prepare('SELECT * FROM `users` WHERE `login` = :login');
    $stmt->execute(array('login' => $login));

    if ($stmt->rowCount() == 1)
    {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $result = $row;
            $result['secret_key'] = md5($result['login'] . $result['secret_key']);
        }
    }

    return $result;
}

function auth_user_by_cookie($user)
{
    if (!isset($_COOKIE['users']))
        return false;

    $cookie_data = json_decode($_COOKIE['users'], true);

    if (!isset($cookie_data[$user['login']]))
        return false;

    $user_data = $cookie_data[$user['login']];

    if ($user['secret_key'] == $user_data['key'])
        auth_user($user);

    return true;
}

function auth_active_user_by_cookie()
{
    if (isset($_COOKIE['users']))
    {
        $cookie_data = json_decode($_COOKIE['users'], true);
        foreach ($cookie_data as $login => $user_data)
            if ($user_data['active'])
            {
                $user = get_user_by_login($login);
                if ($user != null && $user['secret_key'] == $user_data['key'])
                {
                    $_SESSION['user'] = array('login' => $user['login'], 'user_id' => $user['user_id']);
                    return true;
                }
            }
    }

    return false;
}

function logout_user()
{
    if (!isset($_SESSION['user']))
        return false;

    delete_cookie_user($_SESSION['user']['login']);
    unset($_SESSION['user']);
    return true;
}

function delete_cookie_user($login)
{
    if (!isset($_COOKIE['users']))
        return false;

    $cookie_data = json_decode($_COOKIE['users'], true);
    unset($cookie_data[$login]);

    $login = array_key_first($cookie_data);
    if ($login != null)
        $cookie_data[$login]['active'] = true;

    setcookie('users', json_encode($cookie_data), time()+60*60*24*30);

    return true;
}


function get_user_page($user)
{
    $result = '
<h2>'.$user['name'].'</h2>
<p>'.date('d.m.Y', strtotime($user['date_birth'])).'</p>
<button type="button" onclick="exit()">Выйти</button>';

    $auth_users = get_auth_users();

    if (count($auth_users) > 0)
    {
        $auth_users_list = '<ul>';
        foreach ($auth_users as $login => $user)
            $auth_users_list .= '<li onclick="change(\''.$login.'\')">' . $user['name'] . '</li>';

        $result .= $auth_users_list . '</ul>';
    }

    $result .= '<button type="button" onclick="add_new_user()">Добавить пользователя</button>';

    return $result;
}

function get_login_form()
{
    echo '
        <form id="login-form" method="post">
            <input type="text" name="login" placeholder="Логин">
            <input type="password" name="pass" placeholder="Пароль">
            <button type="button" onclick="auth_user()">Войти</button>
        </form>
    ';
}

function get_auth_users()
{
    $result = array();

    if (!isset($_COOKIE['users']) || !isset($_SESSION['user']))
        return $result;

    $cookie_data = json_decode($_COOKIE['users'], true);
    foreach ($cookie_data as $login => $_)
    {
        if ($_SESSION['user']['login'] == $login)
            continue;

        $user = get_user_by_login($login);
        $result[$login] = array('name' => $user['name'], 'photo' => $user['photo']);
    }

    return $result;
}

function save_failed_login($user_id)
{
    global $db;

    $stmt = $db->prepare('UPDATE `users` SET `failed_count` = `failed_count`+1,`last_failed_login` = :cur_time WHERE `user_id` = :user_id');
    $stmt->execute(array(
        'user_id' => $user_id,
        'cur_time' => date('Y-m-d H:i:s')));
}

function clean_failed_login($user_id)
{
    global $db;

    $stmt = $db->prepare('UPDATE `users` SET `failed_count` = 0,`last_failed_login`=:cur_time WHERE `user_id` = :user_id');
    $stmt->execute(array(
        'user_id' => $user_id,
        'cur_time' => null));
}


