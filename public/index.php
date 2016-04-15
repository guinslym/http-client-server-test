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
    use pillr\library\http\Stream as Stream;

    $protocolVersion = '1.1';
    $statusCode = '200';
    $reasonPhrase = 'OK';


    $httpRequest = new Request;
    $httpRequest = $httpRequest->withUri(new Uri($_SERVER['REQUEST_URI']));

    $httpResponse = new Response($protocolVersion,$statusCode,$reasonPhrase);

    #Body creation

    $id = "\t".'"@id":'.$httpRequest->getRequestTarget().'",';
    $timeSent = "\t".'"timeSent":"'.time().'"';

    $body = new Stream;
    $body->write('{');
    $body->write($id);
    $body->write("\t".'"to": "Pillr",');
    $body->write("\t".'"subject": "Hello Pillr",');
    $body->write("\t".'"message": "Here is my submission.",');
    $body->write("\t".'"from": "Felix Dube",');
    $body->write($timeSent);
    $body->write("}");

    $httpResponse = $httpResponse->withBody($body);



    #Headers

    $httpResponse = $httpResponse->withAddedHeader('Date',date('D, d M Y H:i:s T'));
    $httpResponse = $httpResponse->withAddedHeader('Server',$_SERVER['SERVER_SOFTWARE']);
    $httpResponse = $httpResponse->withAddedHeader('Last-Modified',date('D, d M Y H:i:s T',filemtime(__FILE__)));
    $httpResponse = $httpResponse->withAddedHeader('Content-Length',strlen($httpResponse->getBody()->__toString()));
    $httpResponse = $httpResponse->withAddedHeader('Content_Type','application/json');

    echo nl2br("HTTP/".$httpResponse->getProtocolVersion().' '.$httpResponse->getStatusCode().' '.$httpResponse->getReasonPhrase().PHP_EOL);
    foreach($httpResponse->headers as $name => $value)
    {
        echo nl2br($name.': '.$value.PHP_EOL);
    }

    echo nl2br($httpResponse->getBody()->__toString());



 ?>
 </body>
</html>
