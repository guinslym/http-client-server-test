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
    use pillr\library\http\Request as Request;
    use pillr\library\http\Response as Response;

    $protocolVersion = '1.1';
    $statusCode = '200';
    $reasonPhrase = 'OK';

    $headers = array();
    $body='';

    $httpRequest = new Request();

    $httpResponse = new Response($protocolVersion,$statusCode,$reasonPhrase,$headers,$body)




    #Headers
    $date = array('Date'=>date('D, d M Y H:i:s T'));
    $server = array('Server'=>$_SERVER['SERVER_NAME']);
    $lastModified = array('Last-Modified'=>date('D, d M Y H:i:s T',filemtime(__FILE__));
    $contentLength = array('Content-Length'=>strlen($body));
    $contentType = array('Content_Type'=> 'application/json');

    $headers = array_merge($date,$server,$lastModified,$contentLength,$contentType);







 ?>
 </body>
</html>
