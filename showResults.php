<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Show Results</title>
  </head>
  <body>
    <?php
    $fileName = "paymentsCallResponse.txt";
    $file = fopen($fileName, "r");

    while (!feof($file)) {
      $str = fgets($file);
      print "$str<BR>";
    }

    fclose($file);
    ?>
  </body>
</html>
