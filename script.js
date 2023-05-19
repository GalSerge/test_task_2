async function auth_user() {

    document.getElementById('msg_text').innerHTML = '';
    var form_data = new FormData(document.getElementById('login_form'));

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
            {
                await show_hello();
                document.getElementById('page').innerHTML = await response_page.text();
                await new Promise(resolve => setTimeout(resolve, 8000));
                await hidden_hello();
            }
        } else
        {
            document.getElementById('msg_text').innerHTML = result.msg;
        }
    }
}

async function show_hello() {
    document.getElementById('welcome').style.display = 'block';
    for (let i = 0; i <= 50; i++) {
        await new Promise(resolve => setTimeout(resolve, 20));
        document.getElementById('welcome').style.backdropFilter = `blur(${i}px)`;
        document.getElementById('welcome').style.textShadow = `0 0 ${50 - i}px rgb(24, 33, 47)`;
    }
}

async function hidden_hello() {
    document.getElementById('welcome').style.display = 'block';
    for (let i = 50; i >= 0; i--) {
        await new Promise(resolve => setTimeout(resolve, 20));
        document.getElementById('welcome').style.backdropFilter = `blur(${i}px)`;
        document.getElementById('welcome').style.textShadow = `0 0 ${49 - i}px rgb(24, 33, 47)`;
    }

    document.getElementById('welcome').style.display = 'none';
}

async function exit()
{
    const response_page = await fetch('login.php?exit', {
        method: 'GET'
    });

    if (response_page.status == 200)
        document.getElementById('page').innerHTML = await response_page.text();
}

async function add_new_user()
{
    const response_page = await fetch('login.php?login', {
        method: 'GET'
    });

    if (response_page.status == 200)
        document.getElementById('page').innerHTML = await response_page.text();
}

async function change(login)
{
    const response_page = await fetch('login.php?select=' + login, {
        method: 'GET'
    });

    if (response_page.status == 200)
        document.getElementById('page').innerHTML = await response_page.text();
}
