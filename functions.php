<?

function auth_user($user)
{
    $_SESSION['user'] = array('login' => $user['login'], 'user_id' => $user['user_id']);

    if (isset($_COOKIE['users']))
    {
        $cookie_data = json_decode($_COOKIE['users'], true);
        foreach ($cookie_data as $user_data)
            $user_data['active'] = false;

        $cookie_data[] = array(
            'active' => true,
            'login' => $user['login'],
            'key' => $user['secret_key']);
    } else
        $cookie_data = array(array(
            'active' => true,
            'login' => $user['login'],
            'key' => $user['secret_key']));

    setcookie('users', json_encode($cookie_data), time()+60*60*24*30);
}

function get_user_by_login($login)
{
    global $db;

    $result = null;

    $stmt = $db->prepare("SELECT * FROM `users` WHERE `login` = :login");
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

function get_user_page($user)
{
    return '
<h2>'.$user['name'].'</h2>
<p>'.date('d.m.Y', strtotime($user['date_birth'])).'</p>
';
}

