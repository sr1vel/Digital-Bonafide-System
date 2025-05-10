<?php
$to="shrinidhi912@gmail.com";
$subject ="HTML email";
$message="
<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>THis email contains HTML Tags!</p>
<table>
<tr>
<th>Firstname</th>
<th>Lastname</th>
</tr>
<tr>
<td>john</td>
<td>Doe</td>
</tr>
</table>
</body>
</html>
";
$headers="MIME-Version: 1.0"."\r\n";
$headers="Content-type:text/html;charset=UTF-8"."\r\n";
$headers='From: <shrinidhi912@gmail.com>'
mail($to,$subject,$message,$headers);
?>