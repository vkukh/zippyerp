<html>
<head>
<title></title>
</head>
<body>

Information:<p>

Name: {$name|capitalize}<br>
Addr: {$address|escape}<br>
Date: {$smarty.now|date_format:"%Y-%m-%d"}<br>

</body>
</html>