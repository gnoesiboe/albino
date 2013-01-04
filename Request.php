<?php

namespace Albino;

use Albino\DataHolder;

/**
 * Request class.
 *
 * @package    Albino
 * @subpackage Request
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class Request extends DataHolder
{

  /**
   * @var string
   */
  const METHOD_GET = 'GET';

  /**
   * @var string
   */
  const METHOD_POST = 'POST';

  /**
   * @var string
   */
  const METHOD_DELETE = 'DELETE';

  /**
   * @var string
   */
  const METHOD_PUT = 'PUT';

  /**
   * Constructor
   */
  public function __construct()
  {
    if ($this->isCommandLine() === true)
    {
      //@todo implement
      die('@todo: ' . __METHOD__ . ' - (line: ' . __LINE__ . ', file: ' . __FILE__ . ')');
    }
    else
    {
      $this->initHeaders();
      $this->initPath();
      $this->initQuery();
      $this->initMethod();
      $this->initBody();
      $this->initFiles();
    }
  }

  /**
   * @return string
   */
  public function getContentType()
  {
    if (isset($_SERVER['CONTENT_TYPE']))
    {
      return $_SERVER['CONTENT_TYPE'];
    }

    if (isset($_SERVER['HTTP_CONTENT_TYPE']))
    {
      return $_SERVER['HTTP_CONTENT_TYPE'];
    }

    return null;
  }

  /**
   * Parses http headers from the $_SERVER variables
   * and makes them available for the convenience functions
   */
  protected function initHeaders()
  {
    $headers = new DataHolder();

    foreach ($_SERVER as $key => $value)
    {
      if (stripos($key, 'http_', 0) !== false)
      {
        $headers->set($this->prepareHeaderKey($key), $value);
      }
    }

    $this->set('_headers', $headers);
  }

  /**
   * @param string $key
   * @return string
   */
  protected function prepareHeaderKey($key)
  {
    return strtolower(substr($key, 5));
  }

  /**
   * @return DataHolder
   */
  public function getHeaders()
  {
    return $this->get('_headers');
  }

  /**
   * Retrieves the request method and saves it to a convenience
   * variable
   */
  protected function initMethod()
  {
    $this->set('_method', strtoupper($_SERVER['REQUEST_METHOD']));
  }

  /**
   * retrieves the request path and
   * saves it to a convenience variable
   */
  protected function initPath()
  {
    $this->set('_path', isset($_GET['_path']) ? $_GET['_path'] : '/');

    unset($_GET['_path']);
  }

  /**
   * @return string
   */
  public function getRawBody()
  {
    if ($this->has('_rawBody') === true)
    {
      return $this->get('_rawBody');
    }

    $this->set('_rawBody', trim(file_get_contents('php://input')));

    return $this->get('_rawBody');
  }

  /**
   * @return DataHolder
   */
  public function getFiles()
  {
    return $this->get('_files');
  }

  /**
   * Initiates the easy files access
   */
  protected function initFiles()
  {
    $this->set('_files', new DataHolder($_FILES));
  }

  /**
   * Inits the request body
   *
   * @throws Exception
   */
  protected function initBody()
  {
    $body = new DataHolder();

    switch ($this->getMethod())
    {
      case self::METHOD_GET:
        break;

      case self::METHOD_POST:
        $body->setData($_POST);
        break;

      case self::METHOD_PUT:
        $body->setData($this->parsePutVariables());
        break;

      default:
        throw new Exception(sprintf('Request method: %s not supported', $this->getMethod()));
    }

    $this->set('_body', $body);
  }

  /**
   * PHP doesnt support put variables
   *
   * @todo-al optimize
   *
   * @return array
   */
  protected function parsePutVariables()
  {
    $putParams = array();
    $input = $this->getRawBody();

    // if not multipart we don't need to
    if ($this->isMultipart() === false)
    {
      parse_str($input, $putParams);
      return $putParams;
    }

    // check if a boundary is used, if not just parse data
    $boundary = $this->getMultipartBoundary();
    if (is_null($boundary) === true)
    {
      parse_str($input, $putParams);
      return $putParams;
    }

    // echo $input . PHP_EOL . PHP_EOL;

    $values = preg_split("/-+$boundary/", $input);

    foreach ($values as $value)
    {
      // if empty string or null
      if (empty($value) === true)
      {
        continue;
      }

      // parse value name
      if (preg_match('/name="(?P<name>[^"]+)/', $value, $match) > 0)
      {
        $name = $match['name'];
      }
      else
      {
        continue;
      }

      // the value containts a lot of whitespace. For easy handling we split on 'm and remove any empty values in the
      // response array
      $valueParts = array_values(array_filter(preg_split('/\s{2,}/', $value), function ($item) {
        return is_null($item) !== true && trim($item) !== '';
      }));

      if (strpos($value, 'filename') !== false)
      {
        $originalFileName = null;
        if (preg_match('/filename="(?P<originalFileName>[^"]+)"/', $valueParts[0], $match))
        {
          $originalFileName = $match['originalFileName'];
        }

        $contentType = null;
        if (preg_match('/^Content-Type:\s+(?P<contentType>.*)$/', $valueParts[1], $match))
        {
          $contentType = $match['contentType'];
        }

        if (is_null($originalFileName) === true || is_null($contentType) === true)
        {
          continue;
        }

        $contents = $contents = trim(preg_replace("/^(.*\n){3}/", "", $value));

        $filepath = tempnam(sys_get_temp_dir(), 'fhapi_');
        file_put_contents($filepath, $contents);

        $_FILES[$name] = array(
          'name' => $originalFileName,
          'type' => $contentType,
          'tmp_name' => $filepath,
          'error' => 0,
          'size' => mb_strlen($contents)
        );
      }
      else
      {
        // not a file, so text value

        $putParams[$name] = $valueParts[1];
      }
    }

    return $putParams;
  }

  /**
   * @return string
   */
  public function getMultipartBoundary()
  {
    if ($this->isMultipart() === false)
    {
      return null;
    }

    if (preg_match('#boundary=(?P<key>.*)$#i', $this->getContentType(), $match) !== false)
    {
      return $match['key'];
    }

    return null;
  }

  /**
   * @return bool
   */
  public function isMultipart()
  {
    return stripos($this->getContentType(), 'multipart/form-data', 0) !== false;
  }

  /**
   * @return DataHolder
   */
  public function getBody()
  {
    return $this->get('_body');
  }

  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->get('_method');
  }

  /**
   * Returns the query params, if any
   *
   * @return DataHolder
   */
  public function getQuery()
  {
    return $this->get('_query');
  }

  /**
   * Returns the request path
   *
   * @return string
   */
  public function getPath()
  {
    return $this->data['_path'];
  }

  /**
   * Retrieves and parses the query string
   */
  protected function initQuery()
  {
    // add query params
    $queryString = substr($_SERVER['REQUEST_URI'], strlen($this->getPath()) + 1);

    $this->set('_query', new DataHolder(Util::queryStringToArray($queryString)));
  }

  /**
   * @return bool
   */
  protected function isCommandLine()
  {
    return php_sapi_name() === 'cli';
  }
}
