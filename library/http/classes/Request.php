<?php

namespace pillr\library\http;


use \Psr\Http\Message\RequestInterface  as  RequestInterface;
use \Psr\Http\Message\UriInterface      as  UriInterface;

use \pillr\library\http\Message         as  Message;
/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Request extends Message implements RequestInterface
{

    public $protocolVersion = '1.1';
    public $httpMethod = 'GET';
    public $uri;
    public $headers = array();
    public $body;
    public function __construct($protocolVersion='1.1', $httpMethod='GET', $uri=NULL, $headers=array(), $body=NULL)
    {
        if ($uri === NULL)
        {
          $uri = new Uri;
        }

        if ($body === NULL)
        {
          $body = new Stream;
        }

        if (is_string($body))
        {
          $bodyTmp = new Stream;
          $bodyTmp->write($body);
          $body = $bodyTmp;
        }



        $tmp = $this;

        $tmp = $tmp->withProtocolVersion($protocolVersion);
        $tmp = $tmp->withMethod($httpMethod); #Using the setter methods to ensure values passed are valid.
        $tmp = $tmp->withUri($uri);
        foreach($headers as $name => $value)
        {
            $tmp=$tmp->withAddedHeader($name,$value);
        }

        $tmp = $tmp->withBody($body);

        $this->protocolVersion = $tmp->protocolVersion;
        $this->httpMethod=$tmp->httpMethod;
        $this->uri = $tmp->uri;
        $this->headers = $tmp->headers;
        $this->body = $tmp->body;

    }


    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->uri->__toString();
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        $output = $this;
        $output->uri = new Uri($requestTarget);
        return $output;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return self
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
      $output = $this;

      if(in_array(strtoupper($method),array("GET","HEAD","POST","PUT","DELETE","TRACE","OPTIONS","CONNECT","PATCH")))
      {
        $output->httpMethod = $method;
        return $output;
      }

      else {
        throw new \InvalidArgumentException('Not a valid HTTP method');
      }
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $output=$this;
        if ($this->uri) #Only if the uri has already been set for this object do we need to care about host preservation
        {
          if(!$preserveHost)
          {
              if($uri->getHost()=='')
              {
                  $uri=$uri->withHost($this->uri->getHost());
              }
          }

          else
          {
              if($this->uri->getHost()!=='')
              {
                  $uri=$uri->withHost($this->uri->getHost());
              }
          }
        }

        $output->uri = $uri;
        return $output;
    }


}
