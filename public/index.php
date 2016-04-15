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

    #Defining getallheaders() for nginx for whole HTTP request retrieval.
    if (!function_exists('getallheaders'))
    {
        function getallheaders()
        {
               $headers = '';
           foreach ($_SERVER as $name => $value)
           {
               if (substr($name, 0, 5) == 'HTTP_')
               {
                   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
               }
           }
           return $headers;
        }
    }

    #Request retrieval
    $httpRequest = new Request
    (
      $_SERVER['SERVER_PROTOCOL'],
      $_SERVER['REQUEST_METHOD'],
      new Uri($_SERVER['REQUEST_URI']),
      getallheaders(),
      new Stream(file_get_contents('php://input'))
    );

    #Response creation
    $httpResponse = new Response('1.1','200','OK');

    #Body creation

    $bodyArray =
    array(
      '@id'=> $httpRequest->getRequestTarget(),
      'to' => 'Pillr', 'subject'=>'Hello Pillr',
      'message'=>'Here is my submission',
      'from'=>'Felix Dube',
      'timesent'=>time()
    );

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

    #Response printing

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
