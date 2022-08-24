<?php

namespace Pkit\Http;

class ContentType
{
  public static function validate(string $content)
  {
    $constantNames = (new \ReflectionClass(self::class))
      ->getConstants();
    return in_array($content, $constantNames);
  }
  const NONE = "*";
  const AAC =  "audio/aac";
  const ABW =  "application/x-abiword";
  const ARC =  "application/octet-stream";
  const AVI =  "video/x-msvideo";
  const AZW =  "application/vnd.amazon.ebook";
  const BIN =  "application/octet-stream";
  const BZ = "application/x-bzip";
  const BZ2 =  "application/x-bzip2";
  const CSH =  "application/x-csh";
  const CSS =  "text/css";
  const CSV =  "text/csv";
  const DOC =  "application/msword";
  const EOT =  "application/vnd.ms-fontobject";
  const EPUB = "application/epub+zip";
  const GIF =  "image/gif";
  const HTM = "text/html";
  const HTML = "text/html";
  const ICO =  "image/x-icon";
  const ICS =  "text/calendar";
  const JAR = "application/java-archive";
  const JPEG = "image/jpeg";
  const JPG = "image/jpeg";
  const JS = "application/javascript";
  const JSON = "application/json";
  const MID = "audio/midi";
  const MIDI = "audio/midi";
  const MPEG = "video/mpeg";
  const MPKG = "application/vnd.apple.installer+xml";
  const ODP = "application/vnd.oasis.opendocument.presentation";
  const ODS = "application/vnd.oasis.opendocument.spreadsheet";
  const ODT = "application/vnd.oasis.opendocument.text";
  const OGA = "audio/ogg";
  const OGV = "video/ogg";
  const OGX = "application/ogg";
  const OTF = "font/otf";
  const PNG = "image/png";
  const PDF = "application/pdf";
  const PPT = "application/vnd.ms-powerpoint";
  const RAR = "application/x-rar-compressed";
  const RTF = "application/rtf";
  const SH = "application/x-sh";
  const SVG = "image/svg+xml";
  const SWF = "application/x-shockwave-flash";
  const TAR = "application/x-tar";
  const TIF = "image/tiff";
  const TIFF = "image/tiff";
  const TS = "application/typescript";
  const TTF = "font/ttf";
  const VSD = "application/vnd.visio";
  const WAV = "audio/x-wav";
  const WEBA = "audio/webm";
  const WEBM = "video/webm";
  const WEBP = "image/webp";
  const WOFF = "font/woff";
  const WOFF2 = "font/woff2";
  const XHTML = "application/xhtml+xml";
  const XLS = "application/vnd.ms-excel";
  const XLSX = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
  const XML = "application/xml";
  const XUL = "application/vnd.mozilla.xul+xml";
  const ZIP = "application/zip";
  const _3GP = "video/3gpp";
  const _3GP_video = "video/3gpp";
  const _3GP_audio = "audio/3gpp";
  const _3G2 = "video/3gpp2";
  const _3G2_video = "video/3gpp2";
  const _3g2_audio = "audio/3gpp2";
  const _7Z = "application/x-7z-compressed";
}
