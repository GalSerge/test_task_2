async function auth_user() {

    var form_data = new FormData(document.getElementById('login-form'));

    const response = await fetch('login.php', {
        method: 'POST',
        body: form_data
    });

    if (response.status == 200)
    {
        var result = await response.json()
        if (result.ok)
        {
            const response_page = await fetch('login.php?page', {
                method: 'GET'
            });

            if (response_page.status == 200)
                document.getElementById('page').innerHTML = await response_page.text();
        }
    }
}

async function exit()
{
    const response_page = await fetch('login.php?exit', {
        method: 'GET'
    });

    if (response_page.status == 200)
        document.getElementById('page').innerHTML = await response_page.text();
}