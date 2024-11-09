<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use League\HTMLToMarkdown\HtmlConverter;
use League\CommonMark\CommonMarkConverter;

final class Toolbox
{
  /**
   * Clean integer string value (strip all chars not - and spaces )
   *
   * @since versin 0.83.5
   *
   * @param string  $integer  integer string
   *
   * @return string  clean integer
   **/
  public static function cleanInteger($integer)
  {
    return preg_replace("/[^0-9-]/", "", $integer);
  }

  public static function getRootPath(Request $request)
  {
    $routeContext = RouteContext::fromRequest($request);
    return $routeContext->getBasePath();
  }

  /**
   * strtolower function for utf8 string
   *
   * @param string $str
   *
   * @return string  lower case string
  **/
  public static function strtolower($str)
  {
    if (is_null($str))
    {
      return null;
    }
    return mb_strtolower($str, "UTF-8");
  }

  /**
   * Convert HTML text into markdown
   */
  public static function convertHtmlToMarkdown($text)
  {
    $converter = new HtmlConverter();
    $converter->getConfig()->setOption('strip_tags', true);

    return $converter->convert(html_entity_decode($text));
  }

  /**
   * Convert markdown text into HTML
   */
  public static function convertMarkdownToHtml($text)
  {
    $converter = new CommonMarkConverter([
      'html_input' => 'strip',
      'allow_unsafe_links' => false,
    ]);
    return $converter->convert($text);
  }

  /**
   * used in migration
   */
  public static function fixDate($myDate)
  {
    if (
        $myDate == '0000-00-00 00:00:00' ||
        $myDate == '0000-00-00'
    )
    {
      return null;
    }
    return $myDate;
  }
}
