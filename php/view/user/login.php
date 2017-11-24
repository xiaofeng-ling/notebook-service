<html>
<meta charset="'utf-8" />
<head>
    <title>登录页</title>
</head>

<script>
    function login()
    {
        let name = document.getElementById('name').value;
        let password = document.getElementById('password').value;

        let ajax = new XMLHttpRequest();

        ajax.open('post', '/index.php/user/login', true);
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.onreadystatechange = function() {
            if (ajax.readyState == 4 && ajax.status == 200) {
                console.log(ajax.responseText);
            }
        }
        ajax.send('name='+ name +'&password='+ password);
    }
</script>

<body>
    <p>用户名：<input type="text" id="name" /></p>
    密码：<input type="password" id="password" />
    <button onclick="login();">登录</button>
</body>

</html>