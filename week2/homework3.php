<!DOCTYPE html>
<html>
<head>
<title>homework3</title>
</head>
<body>
<?php
$x = rand(1,100);
$y = rand(1,100);
echo "The first random number is " . $x . "<br>";
echo "The second random number is " . $y . "<br>";
if ($x > $y) { 
  echo "The larger random number is " . $x;
}
?>

</body>
</html>
