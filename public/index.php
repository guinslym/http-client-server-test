<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php

   # TIP: Use the $_SERVER Sugerglobal to get all the data your need from the Client's HTTP Request.

   # TIP: HTTP headers are printed natively in PHP by invoking header().
   #      Ex. header('Content-Type', 'text/html');

    require __DIR__ .'/../vendor/autoload.php';

    use pillr\library\http\Message as Message;
    use pillr\library\http\Uri as Uri;

    $uri_string = "http://pillrcompany.com/interns/test?psr=true";



    $test = new Uri($uri_string);
    echo $test->getScheme().' ';
    echo $test->getHost().' ';
    echo $test->getAuthority().' ';
    echo $test->getPath().' ';
    echo $test->getQuery().' ';
    echo $test->getFragment().' ';






 ?>
 </body>
</html>
