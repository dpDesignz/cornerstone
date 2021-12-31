<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * File Manager Class
 */

class FileManager
{

  // Properties
  protected $is_windows = false; // Check if system is Windows
  protected $iconv_input_encoding = 'UTF-8'; // Input encoding for iconv
  protected $curr_path = ''; // Current path for file manager.
  protected $root_path = ''; // Root path for file manager. use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
  protected $root_url = ''; // Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'. Will not working if $root_path will be outside of server document root
  protected $allowed_file_extensions = ''; // Allowed file extensions for create and rename files e.g. 'txt,html,css,js'
  protected $allowed_upload_extensions = ''; // Allowed file extensions for upload files e.g. 'gif,png,jpg,html,txt'
  protected $exclude_items = array(); // Files and folders to excluded from listing e.g. array('myfile.html', 'personal-folder', '*.php', ...)
  protected $max_upload_size_bytes = 5000; // Maximum file upload size. Increase the following values in php.ini to work properly: memory_limit, upload_max_filesize, post_max_size
  protected $show_hidden_files = false; // Show or hide files and folders that starts with a dot
  protected $show_directory_size = false; // Show directory size: true or speedup output: false
  protected $hide_cols = false; // Hide Permissions and Owner cols in file-listing
  protected $online_viewer = 'google'; // Online office Docs Viewer

  /**
   * Constructor
   */
  public function __construct($values = [])
  {
    // If values are specified, then the object is hydrated.
    if (!empty($values)) {
      $this->hydrate($values);
    }

    // Set if system is Windows
    $this->is_windows = (DIRECTORY_SEPARATOR == '\\') ? true : false;
  }

  /**
   * Hydrater
   */
  public function hydrate($data)
  {
    foreach ($data as $attribute => $value) {

      // Get the prefix
      $method = 'set' . ucfirst($attribute);

      if (is_callable([$this, $method])) {
        $this->$method($value);
      }
    }
  }

  /**
   * Encode html entities
   * @param string $text
   * @return string
   */
  public function enc($text)
  {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }

  /**
   * Path traversal prevention and clean the url
   * It replaces (consecutive) occurrences of / and \\ with whatever is in DIRECTORY_SEPARATOR, and processes /. and /.. fine.
   * @param $path
   * @return string
   */
  private function get_absolute_path($path)
  {
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
      if ('.' == $part) continue;
      if ('..' == $part) {
        array_pop($absolutes);
      } else {
        $absolutes[] = $part;
      }
    }
    return implode(DIRECTORY_SEPARATOR, $absolutes);
  }

  /**
   * Clean Path
   * @param string $path
   * @return string
   */
  public function clean_path($path, $trim = true)
  {
    $path = $trim ? trim($path) : $path;
    $path = trim($path, '\\/');
    $path = str_replace(array('../', '..\\'), '', $path);
    $path =  $this->get_absolute_path($path);
    if ($path == '..') {
      $path = '';
    }
    return str_replace('\\', '/', $path);
  }

  /**
   * Get parent path
   * @param string $path
   * @return bool|string
   */
  public function get_parent_path($path)
  {
    $path = $this->clean_path($path);
    if ($path != '') {
      $array = explode('/', $path);
      if (count($array) > 1) {
        $array = array_slice($array, 0, -1);
        return implode('/', $array);
      }
      return '';
    }
    return false;
  }

  /**
   * Check file is in exclude list
   * @param string $file
   * @return bool
   */
  public function is_exclude_items($file)
  {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (isset($exclude_items) and sizeof($exclude_items)) {
      unset($exclude_items);
    }

    $exclude_items = $this->excludeItems();
    if (version_compare(PHP_VERSION, '7.0.0', '<')) {
      $exclude_items = unserialize($exclude_items);
    }
    if (!in_array($file, $exclude_items) && !in_array("*.$ext", $exclude_items)) {
      return false;
    }
    return true;
  }

  /**
   * Get file mimes
   * @param string $extension
   * @return string
   */
  private function get_file_mimes($extension)
  {
    $fileTypes['swf'] = 'application/x-shockwave-flash';
    $fileTypes['pdf'] = 'application/pdf';
    $fileTypes['exe'] = 'application/octet-stream';
    $fileTypes['zip'] = 'application/zip';
    $fileTypes['doc'] = 'application/msword';
    $fileTypes['xls'] = 'application/vnd.ms-excel';
    $fileTypes['ppt'] = 'application/vnd.ms-powerpoint';
    $fileTypes['gif'] = 'image/gif';
    $fileTypes['png'] = 'image/png';
    $fileTypes['jpeg'] = 'image/jpg';
    $fileTypes['jpg'] = 'image/jpg';
    $fileTypes['webp'] = 'image/webp';
    $fileTypes['avif'] = 'image/avif';
    $fileTypes['rar'] = 'application/rar';

    $fileTypes['ra'] = 'audio/x-pn-realaudio';
    $fileTypes['ram'] = 'audio/x-pn-realaudio';
    $fileTypes['ogg'] = 'audio/x-pn-realaudio';

    $fileTypes['wav'] = 'video/x-msvideo';
    $fileTypes['wmv'] = 'video/x-msvideo';
    $fileTypes['avi'] = 'video/x-msvideo';
    $fileTypes['asf'] = 'video/x-msvideo';
    $fileTypes['divx'] = 'video/x-msvideo';

    $fileTypes['mp3'] = 'audio/mpeg';
    $fileTypes['mp4'] = 'audio/mpeg';
    $fileTypes['mpeg'] = 'video/mpeg';
    $fileTypes['mpg'] = 'video/mpeg';
    $fileTypes['mpe'] = 'video/mpeg';
    $fileTypes['mov'] = 'video/quicktime';
    $fileTypes['swf'] = 'video/quicktime';
    $fileTypes['3gp'] = 'video/quicktime';
    $fileTypes['m4a'] = 'video/quicktime';
    $fileTypes['aac'] = 'video/quicktime';
    $fileTypes['m3u'] = 'video/quicktime';

    $fileTypes['php'] = ['application/x-php'];
    $fileTypes['html'] = ['text/html'];
    $fileTypes['txt'] = ['text/plain'];
    //Unknown mime-types should be 'application/octet-stream'
    if (empty($fileTypes[$extension])) {
      $fileTypes[$extension] = ['application/octet-stream'];
    }
    return $fileTypes[$extension];
  }

  /**
   * @param $file
   * Recover all file sizes larger than > 2GB.
   * Works on php 32bits and 64bits and supports linux
   * @return int|string
   */
  public function get_size($file)
  {
    static $iswin;
    static $isdarwin;
    if (!isset($iswin)) {
      $iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
    }
    if (!isset($isdarwin)) {
      $isdarwin = (strtoupper(substr(PHP_OS, 0)) == "DARWIN");
    }

    static $exec_works;
    if (!isset($exec_works)) {
      $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
    }

    // try a shell command
    if ($exec_works) {
      $arg = escapeshellarg($file);
      $cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : ($isdarwin ? "stat -f%z $arg" : "stat -c%s $arg");
      @exec($cmd, $output);
      if (is_array($output) && ctype_digit($size = trim(implode("\n", $output)))) {
        return $size;
      }
    }

    // try the Windows COM interface
    if ($iswin && class_exists("COM")) {
      try {
        $fsobj = new \COM('Scripting.FileSystemObject');
        $f = $fsobj->GetFile(realpath($file));
        $size = $f->Size;
      } catch (\Exception $e) {
        $size = null;
      }
      if (ctype_digit($size)) {
        return $size;
      }
    }

    // if all else fails
    return filesize($file);
  }

  /**
   * Get nice filesize
   * @param int $size
   * @return string
   */
  public function get_file_size($size)
  {
    $size = (float) $size;
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = ($size > 0) ? floor(log($size, 1024)) : 0;
    $power = ($power > (count($units) - 1)) ? (count($units) - 1) : $power;
    return sprintf('%s %s', round($size / pow(1024, $power), 2), $units[$power]);
  }

  /**
   * Get director information
   * @param string $directory
   * @return array [$size, $count, $dirCount]
   */
  public function get_directory_info($directory)
  {
    if ($this->showDirectorySize() == true) { //  Slower output
      $size = 0;
      $count = 0;
      $dirCount = 0;
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file)
        if ($file->isFile()) {
          $size += $file->getSize();
          $count++;
        } else if ($file->isDir()) {
          $dirCount++;
        }
      return [$size, $count, $dirCount];
      // return $size;
    } else return 'Folder'; //  Quick output
  }

  /**
   * Get info about zip archive
   * @param string $path
   * @return array|bool
   */
  public function get_zip_info($path, $ext)
  {
    if ($ext == 'zip' && function_exists('zip_open')) {
      $zip = new \ZipArchive;
      $arch = $zip->open($path);
      if ($arch) {
        $filenames = array();
        for ($i = 0; $i < $zip->numFiles; $i++) {
          $zip_name = $zip->statIndex($i)['name'];
          $zip_folder = substr($zip_name, -1) == '/';
          $filenames[] = array(
            'name' => $zip_name,
            'filesize' => $zip->statIndex($i)['size'],
            'compressed_size' => $zip->statIndex($i)['comp_size'],
            'folder' => $zip_folder
            //'compression_method' => zip_entry_compressionmethod($zip_entry),
          );
        }
        $zip->close();
        return $filenames;
      }
    } elseif ($ext == 'tar' && class_exists('PharData')) {
      $archive = new \PharData($path);
      $filenames = array();
      foreach (new \RecursiveIteratorIterator($archive) as $file) {
        $parent_info = $file->getPathInfo();
        $zip_name = str_replace("phar://" . $path, '', $file->getPathName());
        $zip_name = substr($zip_name, ($pos = strpos($zip_name, '/')) !== false ? $pos + 1 : 0);
        $zip_folder = $parent_info->getFileName();
        $zip_info = new \SplFileInfo($file);
        $filenames[] = array(
          'name' => $zip_name,
          'filesize' => $zip_info->getSize(),
          'compressed_size' => $file->getCompressedSize(),
          'folder' => $zip_folder
        );
      }
      return $filenames;
    }
    return false;
  }

  /**
   * This function scans the files and folder recursively, and return matching files
   * @param string $dir
   * @param string $filter
   * @return json
   */
  public function scan($dir, $filter = '')
  {
    $path = $this->root_path . '/' . $dir;
    if ($dir) {
      $ite = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
      $rii = new \RegexIterator($ite, "/(" . $filter . ")/i");

      $files = array();
      foreach ($rii as $file) {
        if (!$file->isDir()) {
          $fileName = $file->getFilename();
          $location = str_replace($this->root_path, '', $file->getPath());
          $files[] = array(
            "name" => $fileName,
            "type" => "file",
            "path" => $location,
          );
        }
      }
      return $files;
    }
  }

  /**
   * Safely create folder
   * @param string $dir
   * @param bool $force
   * @return bool
   */
  public function mkdir($dir, $force)
  {
    if (file_exists($dir)) {
      if (is_dir($dir)) {
        return $dir;
      } elseif (!$force) {
        return false;
      }
      unlink($dir);
    }
    return mkdir($dir, 0777, true);
  }

  /*
  Parameters: downloadFile(File Location, File Name,
  max speed, is streaming
  If streaming - videos will show as videos, images as images
  instead of download prompt
  https://stackoverflow.com/a/13821992/1164642
  */
  public function download_file($fileLocation, $fileName, $chunkSize  = 1024)
  {
    if (connection_status() != 0)
      return (false);
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

    $contentType = $this->get_file_mimes($extension);
    header("Cache-Control: public");
    header("Content-Transfer-Encoding: binary\n");
    header('Content-Type: $contentType');

    $contentDisposition = 'attachment';

    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
      $fileName = preg_replace('/\./', '%2e', $fileName, substr_count($fileName, '.') - 1);
      header("Content-Disposition: $contentDisposition;filename=\"$fileName\"");
    } else {
      header("Content-Disposition: $contentDisposition;filename=\"$fileName\"");
    }

    header("Accept-Ranges: bytes");
    $range = 0;
    $size = filesize($fileLocation);

    if (isset($_SERVER['HTTP_RANGE'])) {
      list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
      str_replace($range, "-", $range);
      $size2 = $size - 1;
      $new_length = $size - $range;
      header("HTTP/1.1 206 Partial Content");
      header("Content-Length: $new_length");
      header("Content-Range: bytes $range$size2/$size");
    } else {
      $size2 = $size - 1;
      header("Content-Range: bytes 0-$size2/$size");
      header("Content-Length: " . $size);
    }

    if (
      $size == 0
    ) {
      die('Zero byte file! Aborting download');
    }
    @ini_set('magic_quotes_runtime', 0);
    $fp = fopen("$fileLocation", "rb");

    fseek($fp, $range);

    while (
      !feof($fp) and (connection_status() == 0)
    ) {
      set_time_limit(0);
      print(@fread($fp, 1024 * $chunkSize));
      flush();
      ob_flush();
      // sleep(1);
    }
    fclose($fp);

    return ((connection_status() == 0) and !connection_aborted());
  }

  /**
   * This function outputs an event callback as JSON
   * @param array $message
   * @return json
   */
  public function event_callback($message)
  {
    echo json_encode($message);
  }

  /**
   * This function gets the file path
   * @param string $path
   * @param class $fileinfo
   * @return string
   */
  public function get_file_path($path, $fileinfo)
  {
    return $path . "/" . basename($fileinfo->name);
  }

  /**
   * This function gets the relative path
   * @param string $path
   * @return string
   */
  public function get_relative_path($path)
  {
    $rootPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->rootPath());
    return trim(str_replace($rootPath, '', $path), '\\');
  }

  /**
   * This function gets the relative path folder
   * @param string $path
   * @return string
   */
  public function get_relative_path_folder($path)
  {
    $rootPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->rootPath());
    // Check if path is a directory
    if (is_dir($path)) {
      return trim(str_replace($rootPath, '', $path), '\\');
    } else {
      $path = pathinfo($path);
      return trim(str_replace($rootPath, '', $path['dirname']), '\\');
    }
  }

  /**
   * Get mime type
   * @param string $file_path
   * @return mixed|string
   */
  public function get_mime_type($file_path)
  {
    if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $file_path);
      finfo_close($finfo);
      return $mime;
    } elseif (function_exists('mime_content_type')) {
      return mime_content_type($file_path);
    } elseif (!stristr(ini_get('disable_functions'), 'shell_exec')) {
      $file = escapeshellarg($file_path);
      $mime = shell_exec('file -bi ' . $file);
      return $mime;
    } else {
      return '--';
    }
  }

  /**
   * Get image files extensions
   * @return array
   */
  public function get_image_exts()
  {
    return array('ico', 'gif', 'jpg', 'jpeg', 'jpc', 'jp2', 'jpx', 'xbm', 'wbmp', 'png', 'bmp', 'tif', 'tiff', 'psd', 'svg', 'webp', 'avif');
  }

  /**
   * Get video files extensions
   * @return array
   */
  public function get_video_exts()
  {
    return array('avi', 'webm', 'wmv', 'mp4', 'm4v', 'ogm', 'ogv', 'mov', 'mkv');
  }

  /**
   * Get audio files extensions
   * @return array
   */
  public function get_audio_exts()
  {
    return array('wav', 'mp3', 'ogg', 'm4a');
  }

  /**
   * Get text file extensions
   * @return array
   */
  public function get_text_exts()
  {
    return array(
      'txt', 'css', 'ini', 'conf', 'log', 'htaccess', 'passwd', 'ftpquota', 'sql', 'js', 'json', 'sh', 'config',
      'php', 'php4', 'php5', 'phps', 'phtml', 'htm', 'html', 'shtml', 'xhtml', 'xml', 'xsl', 'm3u', 'm3u8', 'pls', 'cue',
      'eml', 'msg', 'csv', 'bat', 'twig', 'tpl', 'md', 'gitignore', 'less', 'sass', 'scss', 'c', 'cpp', 'cs', 'py',
      'map', 'lock', 'dtd', 'svg', 'scss', 'asp', 'aspx', 'asx', 'asmx', 'ashx', 'jsx', 'jsp', 'jspx', 'cfm', 'cgi'
    );
  }

  /**
   * Get mime types of text files
   * @return array
   */
  public function get_text_mimes()
  {
    return array(
      'application/xml',
      'application/javascript',
      'application/x-javascript',
      'image/svg+xml',
      'message/rfc822',
    );
  }

  /**
   * Get file names of text files w/o extensions
   * @return array
   */
  public function get_text_names()
  {
    return array(
      'license',
      'readme',
      'authors',
      'contributors',
      'changelog',
    );
  }

  /**
   * Prevent XSS attacks
   * @param string $text
   * @return string
   */
  public function is_valid_filename($text)
  {
    return (strpbrk($text, '/?%*:|"<>') === FALSE) ? true : false;
  }

  /**
   * Check the file extension which is allowed or not
   * @param string $filename
   * @return bool
   */
  public function is_valid_ext($filename)
  {
    $allowed = ($this->allowed_file_extensions) ? explode(',', $this->allowed_file_extensions) : false;

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;

    return ($isFileAllowed) ? true : false;
  }

  /**
   * Check if string is in UTF-8
   * @param string $string
   * @return int
   */
  public function is_utf8($string)
  {
    return preg_match('//u', $string);
  }

  /**
   * Convert file name to UTF-8 in Windows
   * @param string $filename
   * @return string
   */
  public function convert_win($filename)
  {
    if ($this->is_windows && function_exists('iconv')) {
      $filename = iconv($this->iconv_input_encoding, 'UTF-8//IGNORE', $filename);
    }
    return $filename;
  }

  /**
   * Safely rename
   * @param string $old
   * @param string $new
   * @return bool|null
   */
  public function rename($old, $new)
  {
    $isFileAllowed = $this->is_valid_ext($new);

    if (!$isFileAllowed) return false;

    return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
  }

  /**
   * Safely copy file
   * @param string $f1
   * @param string $f2
   * @param bool $upd Indicates if file should be updated with new content
   * @return bool
   */
  public function copy($f1, $f2, $upd)
  {
    $time1 = filemtime($f1);
    if (file_exists($f2)) {
      $time2 = filemtime($f2);
      if (
        $time2 >= $time1 && $upd
      ) {
        return false;
      }
    }
    $ok = copy($f1, $f2);
    if ($ok) {
      touch($f2, $time1);
    }
    return $ok;
  }

  /**
   * Copy file or folder (recursively).
   * @param string $path
   * @param string $dest
   * @param bool $upd Update files
   * @param bool $force Create folder with same names instead file
   * @return bool
   */
  public function rcopy($path, $dest, $upd = true, $force = true)
  {
    if (is_dir($path)) {
      if (!$this->mkdir($dest, $force)) {
        return false;
      }
      $objects = scandir($path);
      $ok = true;
      if (is_array($objects)) {
        foreach ($objects as $file) {
          if ($file != '.' && $file != '..') {
            if (!$this->rcopy($path . '/' . $file, $dest . '/' . $file)) {
              $ok = false;
            }
          }
        }
      }
      return $ok;
    } elseif (is_file($path)) {
      return $this->copy($path, $dest, $upd);
    }
    return false;
  }

  /**
   * Delete  file or folder (recursively)
   * @param string $path
   * @return bool
   */
  public function rdelete($path)
  {
    if (is_link($path)) {
      return unlink($path);
    } elseif (is_dir($path)) {
      $objects = scandir($path);
      $ok = true;
      if (is_array($objects)) {
        foreach ($objects as $file) {
          if ($file != '.' && $file != '..') {
            if (!$this->rdelete($path . '/' . $file)) {
              $ok = false;
            }
          }
        }
      }
      return ($ok) ? rmdir($path) : false;
    } elseif (is_file($path)) {
      return unlink($path);
    }
    return false;
  }

  /**
   * Recursive chmod
   * @param string $path
   * @param int $filemode
   * @param int $dirmode
   * @return bool
   * @todo Will use in mass chmod
   */
  public function rchmod($path, $filemode, $dirmode)
  {
    if (is_dir($path)) {
      if (!chmod($path, $dirmode)) {
        return false;
      }
      $objects = scandir($path);
      if (is_array($objects)) {
        foreach ($objects as $file) {
          if ($file != '.' && $file != '..') {
            if (!$this->rchmod($path . '/' . $file, $filemode, $dirmode)) {
              return false;
            }
          }
        }
      }
      return true;
    } elseif (is_link($path)) {
      return true;
    } elseif (is_file($path)) {
      return chmod($path, $filemode);
    }
    return false;
  }

  /**
   * Get CSS classname for file
   * @param string $path
   * @return string
   */
  public function get_file_icon_class($path)
  {
    // get extension
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    switch ($ext) {
      case 'ico':
      case 'gif':
      case 'jpg':
      case 'jpeg':
      case 'jpc':
      case 'jp2':
      case 'jpx':
      case 'xbm':
      case 'wbmp':
      case 'png':
      case 'bmp':
      case 'tif':
      case 'tiff':
      case 'webp':
      case 'avif':
      case 'svg':
        $img = 'far fa-image';
        break;
      case 'passwd':
      case 'ftpquota':
      case 'sql':
      case 'js':
      case 'json':
      case 'sh':
      case 'config':
      case 'twig':
      case 'tpl':
      case 'md':
      case 'gitignore':
      case 'c':
      case 'cpp':
      case 'cs':
      case 'py':
      case 'rs':
      case 'map':
      case 'lock':
      case 'dtd':
        $img = 'far fa-file-code';
        break;
      case 'txt':
      case 'ini':
      case 'conf':
      case 'log':
      case 'htaccess':
        $img = 'far fa-file-alt';
        break;
      case 'css':
      case 'less':
      case 'sass':
      case 'scss':
        $img = 'fab fa-css3';
        break;
      case 'bz2':
      case 'zip':
      case 'rar':
      case 'gz':
      case 'tar':
      case '7z':
      case 'xz':
        $img = 'far fa-file-archive';
        break;
      case 'php':
      case 'php4':
      case 'php5':
      case 'phps':
      case 'phtml':
        $img = 'far fa-code';
        break;
      case 'htm':
      case 'html':
      case 'shtml':
      case 'xhtml':
        $img = 'fab fa-html5';
        break;
      case 'xml':
      case 'xsl':
        $img = 'far fa-file-excel';
        break;
      case 'wav':
      case 'mp3':
      case 'mp2':
      case 'm4a':
      case 'aac':
      case 'ogg':
      case 'oga':
      case 'wma':
      case 'mka':
      case 'flac':
      case 'ac3':
      case 'tds':
        $img = 'fas fa-music';
        break;
      case 'm3u':
      case 'm3u8':
      case 'pls':
      case 'cue':
      case 'xspf':
        $img = 'fas fa-headphones';
        break;
      case 'avi':
      case 'mpg':
      case 'mpeg':
      case 'mp4':
      case 'm4v':
      case 'flv':
      case 'f4v':
      case 'ogm':
      case 'ogv':
      case 'mov':
      case 'mkv':
      case '3gp':
      case 'asf':
      case 'wmv':
        $img = 'far fa-file-video';
        break;
      case 'eml':
      case 'msg':
        $img = 'far fa-envelope';
        break;
      case 'xls':
      case 'xlsx':
      case 'ods':
        $img = 'far fa-file-excel';
        break;
      case 'csv':
        $img = 'far fa-file-alt';
        break;
      case 'bak':
      case 'swp':
        $img = 'fas fa-paste';
        break;
      case 'doc':
      case 'docx':
      case 'odt':
        $img = 'far fa-file-word';
        break;
      case 'ppt':
      case 'pptx':
        $img = 'far fa-file-powerpoint';
        break;
      case 'ttf':
      case 'ttc':
      case 'otf':
      case 'woff':
      case 'woff2':
      case 'eot':
      case 'fon':
        $img = 'fas fa-font';
        break;
      case 'pdf':
        $img = 'far fa-file-pdf';
        break;
      case 'psd':
      case 'ai':
      case 'eps':
      case 'fla':
      case 'swf':
        $img = 'far fa-file-image';
        break;
      case 'exe':
      case 'msi':
        $img = 'far fa-file';
        break;
      case 'bat':
        $img = 'fas fa-terminal';
        break;
      default:
        $img = 'fas fa-info-circle';
    }

    return $img;
  }

  /**
   * Get online docs viewer supported files extensions
   * @return array
   */
  public function get_onlineViewer_exts()
  {
    return array('doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt', 'pptx', 'ai', 'psd', 'dxf', 'xps', 'rar', 'odt', 'ods');
  }

  /* SET */

  /**
   * Current path for file manager
   *
   * @param string $path Relative path to root path
   */
  public function setCurrentPath(string $path)
  {
    $this->curr_path = $path;
  }

  /**
   * Root path for file manager
   * use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
   *
   * @param string $path use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
   */
  public function setRootPath(string $path)
  {
    // clean and check $root_path
    $root_path = rtrim($path, '\\/');
    $root_path = str_replace('\\', '/', $path);
    if (!@is_dir($root_path)) {
      echo "<h1>Root path \"{$root_path}\" not found!</h1>";
      exit;
    }
    $this->root_path = $root_path;
  }

  /**
   * Root url for links in file manager. Relative to HTTP_HOST. Variants: '', 'path/to/subfolder'
   * Will not working if $root_path will be outside of server document root
   *
   * @param string $path Variants: '', 'path/to/subfolder'
   */
  public function setRootURL(string $path)
  {
    $this->root_url = $path;
  }

  /**
   * Set the allowed file extensions for create and rename files e.g. 'txt,html,css,js'
   *
   * @param string $extensions CSV format extensions e.g. 'txt,html,css,js'
   */
  public function setAllowedFileExtensions(string $extensions)
  {
    $this->allowed_file_extensions = $extensions;
  }

  /**
   * Set the allowed file extensions for upload files e.g. 'gif,png,jpg,html,txt'
   *
   * @param string $extensions CSV format extensions e.g. 'gif,png,jpg,html,txt'
   */
  public function setAllowedUploadExtensions(string $extensions)
  {
    $this->allowed_upload_extensions =  $extensions;
  }

  /**
   * Set the files and folders to excluded from listing e.g. array('myfile.html', 'personal-folder', '*.php', ...)
   *
   * @param array $exclusions e.g. array('myfile.html', 'personal-folder', '*.php', ...)
   */
  public function setExcludeItems(array $exclusions)
  {
    $this->exclude_items = (array) $exclusions;
  }

  /**
   * Maximum file upload size.
   * Increase the following values in php.ini to work properly: memory_limit, upload_max_filesize, post_max_size
   *
   * @param int $maxSize The maximum file upload size in bytes
   */
  public function setMaxUploadSize(int $maxSize)
  {
    $this->max_upload_size_bytes = $maxSize;
  }

  /**
   * Show or hide files and folders that starts with a dot
   *
   * @param bool $show Boolean of whether to show or not
   */
  public function setShowHiddenFiles(bool $show)
  {
    $this->show_hidden_files = $show;
  }

  /**
   * Show directory size: true or speedup output: false
   *
   * @param bool $show Boolean of whether to show or not
   */
  public function setShowDirectorySize(bool $show)
  {
    $this->show_directory_size = $show;
  }

  /**
   * Hide Permissions and Owner cols in file-listing
   *
   * @param bool $show Boolean of whether to show or not
   */
  public function setHideCols(bool $show)
  {
    $this->hide_cols = $show;
  }

  /**
   * Online office Docs Viewer
   * Available rules are 'google', 'microsoft' or false
   * google => View documents using Google Docs Viewer
   * microsoft => View documents using Microsoft Web Apps Viewer
   * false => disable online doc viewer
   *
   * @param bool $show Boolean of whether to show or not
   */
  public function setOnlineViewer(string $viewer)
  {
    if (in_array(trim(strtolower($viewer)), array('google', 'microsoft', 'false'))) {
      $this->online_viewer = strtolower($viewer);
    }
  }

  /* GET */

  public function isWindows()
  {
    return $this->is_windows;
  }

  public function iconvInputEnc()
  {
    return $this->iconv_input_encoding;
  }

  public function currentPath()
  {
    return $this->curr_path;
  }

  public function rootPath()
  {
    return $this->root_path;
  }

  public function rootURL()
  {
    return $this->root_url;
  }

  public function allowedFileExtensions()
  {
    return $this->allowed_file_extensions;
  }

  public function allowedUploadExtensions()
  {
    return $this->allowed_upload_extensions;
  }

  public function excludeItems()
  {
    return $this->exclude_items;
  }

  public function maxUploadSize()
  {
    return $this->max_upload_size_bytes;
  }

  public function showHiddenFiles()
  {
    return $this->show_hidden_files;
  }

  public function showDirectorySize()
  {
    return $this->show_directory_size;
  }

  public function hideCols()
  {
    return $this->hide_cols;
  }

  public function onlineViewer()
  {
    return $this->online_viewer;
  }
}

/**
 * Class to work with zip files (using ZipArchive)
 */
class CS_Zipper
{
  private $zip;

  public function __construct()
  {
    $this->zip = new \ZipArchive();
  }

  /**
   * Create archive with name $filename and files $files (RELATIVE PATHS!)
   * @param string $filename
   * @param array|string $files
   * @return bool
   */
  public function create($filename, $files)
  {
    $res = $this->zip->open($filename, \ZipArchive::CREATE);
    if ($res !== true) {
      return false;
    }
    if (is_array($files)) {
      foreach ($files as $f) {
        if (!$this->addFileOrDir($f)) {
          $this->zip->close();
          return false;
        }
      }
      $this->zip->close();
      return true;
    } else {
      if ($this->addFileOrDir($files)) {
        $this->zip->close();
        return true;
      }
      return false;
    }
  }

  /**
   * Extract archive $filename to folder $path (RELATIVE OR ABSOLUTE PATHS)
   * @param string $filename
   * @param string $path
   * @return bool
   */
  public function unzip($filename, $path)
  {
    $res = $this->zip->open($filename);
    if ($res !== true) {
      return false;
    }
    if ($this->zip->extractTo($path)) {
      $this->zip->close();
      return true;
    }
    return false;
  }

  /**
   * Add file/folder to archive
   * @param string $filename
   * @return bool
   */
  private function addFileOrDir($filename)
  {
    if (is_file($filename)) {
      return $this->zip->addFile($filename);
    } elseif (is_dir($filename)) {
      return $this->addDir($filename);
    }
    return false;
  }

  /**
   * Add folder recursively
   * @param string $path
   * @return bool
   */
  private function addDir($path)
  {
    if (!$this->zip->addEmptyDir($path)) {
      return false;
    }
    $objects = scandir($path);
    if (is_array($objects)) {
      foreach ($objects as $file) {
        if ($file != '.' && $file != '..') {
          if (is_dir($path . '/' . $file)) {
            if (!$this->addDir($path . '/' . $file)) {
              return false;
            }
          } elseif (is_file($path . '/' . $file)) {
            if (!$this->zip->addFile($path . '/' . $file)) {
              return false;
            }
          }
        }
      }
      return true;
    }
    return false;
  }
}

/**
 * Class to work with Tar files (using PharData)
 */
class CS_Zipper_Tar
{
  private $tar;

  public function __construct()
  {
    $this->tar = null;
  }

  /**
   * Create archive with name $filename and files $files (RELATIVE PATHS!)
   * @param string $filename
   * @param array|string $files
   * @return bool
   */
  public function create($filename, $files)
  {
    $this->tar = new \PharData($filename);
    if (is_array($files)) {
      foreach ($files as $f) {
        if (!$this->addFileOrDir($f)) {
          return false;
        }
      }
      return true;
    } else {
      if ($this->addFileOrDir($files)) {
        return true;
      }
      return false;
    }
  }

  /**
   * Extract archive $filename to folder $path (RELATIVE OR ABSOLUTE PATHS)
   * @param string $filename
   * @param string $path
   * @return bool
   */
  public function unzip($filename, $path)
  {
    $res = $this->tar->open($filename);
    if ($res !== true) {
      return false;
    }
    if ($this->tar->extractTo($path)) {
      return true;
    }
    return false;
  }

  /**
   * Add file/folder to archive
   * @param string $filename
   * @return bool
   */
  private function addFileOrDir($filename)
  {
    if (is_file($filename)) {
      try {
        $this->tar->addFile($filename);
        return true;
      } catch (\Exception $e) {
        return false;
      }
    } elseif (is_dir($filename)) {
      return $this->addDir($filename);
    }
    return false;
  }

  /**
   * Add folder recursively
   * @param string $path
   * @return bool
   */
  private function addDir($path)
  {
    $objects = scandir($path);
    if (is_array($objects)) {
      foreach ($objects as $file) {
        if ($file != '.' && $file != '..') {
          if (is_dir($path . '/' . $file)) {
            if (!$this->addDir($path . '/' . $file)) {
              return false;
            }
          } elseif (is_file($path . '/' . $file)) {
            try {
              $this->tar->addFile($path . '/' . $file);
            } catch (\Exception $e) {
              return false;
            }
          }
        }
      }
      return true;
    }
    return false;
  }
}
