<html>
<meta charset="'utf-8" />
<head>
    <title>登录页</title>
    <script type="text/javascript" src="../../js/lib/$.js"></script>
</head>

<script>
    function login()
    {
        let name = $("#name").value;
        let password = $('#password').value;

        $.ajax({
            method: 'post',
            url: '/index.php/user/login',
            data: {
                name: name,
                password: password
            },
            success: function(response) {
                console.log(response)
                if (response.cn === 0) {
                    document.cookie = 'sess='+decodeURI(response.data);
                }
            }
        });
    }
</script>

<body>
    <p>用户名：<input type="text" id="name" /></p>
    密码：<input type="password" id="password" />
    <button type='button' onclick="login();">登录</button>
    <p id="info"></p>
</body>

</html>