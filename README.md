## 这是什么？

一个简单的日记本程序，具有简单的编辑功能，能够保存你的每日的记录

## 它有什么特性？
轻量

## 如何安装？

安装需求：  
* PHP >= 5.6.4
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension

将代码下载到服务器上，进入laravel目录，然后执行  
<code>composer install</code>  
来安装，之后复制.env.example为.env，调整其中的配置以适应您的需求  
最后，运行
<code>php artisan key:generate</code>  
生成应用程序所必须的key  
至此安装完成

**Tips：存储在数据库中的数据是加密过后的，但是在传输途中是解密后的明文，强烈建议使用https来保障传输过程中的安全性**

