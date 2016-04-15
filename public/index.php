<html>
<head>
<title>PHP Test</title>
</head>
<body>
<pre>
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

    $bodyArray = array('@id'=> $httpRequest->getRequestTarget(), 'to' => 'Pillr', 'subject'=>'Hello Pillr','message'=>'Here is my submission','from'=>'Felix Dube','timesent'=>time());
    $bodyJson = json_encode($bodyArray,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

    $body = new Stream;
    $body->write($bodyJson);
    $httpResponse = $httpResponse->withBody($body);

    #Headers

    $httpResponse = $httpResponse->withAddedHeader('Date',date('D, d M Y H:i:s T'));
    $httpResponse = $httpResponse->withAddedHeader('Server',$_SERVER['SERVER_SOFTWARE']);
    $httpResponse = $httpResponse->withAddedHeader('Last-Modified',date('D, d M Y H:i:s T',filemtime(__FILE__)));
    $httpResponse = $httpResponse->withAddedHeader('Content-Length',strlen($httpResponse->getBody()->__toString()));
    $httpResponse = $httpResponse->withAddedHeader('Content_Type','application/json');

    echo "HTTP/".$httpResponse->getProtocolVersion().' '.$httpResponse->getStatusCode().' '.$httpResponse->getReasonPhrase().PHP_EOL;
    foreach($httpResponse->headers as $name => $value)
    {
        echo $name.': '.$value.PHP_EOL;
    }

    echo $httpResponse->getBody()->__toString();



 ?>
</pre>
 </body>
</html>
