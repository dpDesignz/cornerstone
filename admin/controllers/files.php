<?php

use Cornerstone\FileManager;

class Files extends Cornerstone\Controller
{

  // Properties
  protected $cfmh;

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Check if user is allowed admin access
    checkAdminAccess();

    // Load the file manager class
    $this->cfmh = new FileManager(array("rootPath" => DIR_SYSTEM . 'storage' . _DS . 'files'));

    // max upload file size
    define('MAX_UPLOAD_SIZE', $this->cfmh->maxUploadSize());

    // Set encoding
    if (version_compare(PHP_VERSION, '5.6.0', '<') && function_exists('mb_internal_encoding')) {
      mb_internal_encoding('UTF-8');
    }
    if (function_exists('mb_regex_encoding')) {
      mb_regex_encoding('UTF-8');
    }

    // Define the page type
    $this->pageType = 'file';

    // Load the content model
    // $this->contentModel = $this->load->model('sitecontent/content', 'admin');

    // Set Breadcrumbs
    $this->data['breadcrumbs'] = array(
      array(
        'text' => 'Dashboard',
        'href' => get_site_url('admin')
      ),
      array(
        'text' => 'Site Content',
        'href' => ''
      ),
      array(
        'text' => 'File Manager',
        'href' => get_site_url('admin/files/')
      )
    );
  }

  /**
   * Index Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function index(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('view_files')) {
      // Redirect user with error
      flashMsg('admin_dashboard', '<strong>Error</strong> Sorry, you are not allowed to view the file manager. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Get path
    $p = isset($this->request->get['p']) ? $this->request->get['p'] : (isset($this->request->post['p']) ? $this->request->post['p'] : '');

    // Clean the path
    $p = $this->cfmh->clean_path($p);

    // Set current path
    $this->cfmh->setCurrentPath($p);

    // for ajax request - save
    $input = file_get_contents('php://input');
    $_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

    /*************************** ACTIONS ***************************/

    // AJAX Request
    if (isset($_POST['ajax'])) {

      // save
      if (isset($_POST['type']) && $_POST['type'] == "save") {
        // get current path
        $path = $this->cmfh->rootPath();
        if ($this->cfmh->currentPath() != '') {
          $path .= '/' . $this->cfmh->currentPath();
        }
        // check path
        if (!is_dir($path)) {
          flashMsg('admin_filemanager', '<strong>Error</strong> The folder you were trying to save to doesn\'t exist. Please try again', 'warning');
          redirectTo('admin/files/');
        }
        $file = $this->request->get['edit'];
        $file = $this->cfmh->clean_path($file);
        $file = str_replace('/', '', $file);
        if ($file == '' || !is_file($path . '/' . $file)) {
          flashMsg('admin_filemanager', '<strong>Error</strong> File not found', 'warning');
          redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
        }
        header('X-XSS-Protection:0');
        $file_path = $path . '/' . $file;

        $writedata = $_POST['content'];
        $fd = fopen($file_path, "w");
        $write_results = @fwrite($fd, $writedata);
        fclose($fd);
        if (
          $write_results === false
        ) {
          header("HTTP/1.1 500 Internal Server Error");
          die("Could Not Write File! - Check Permissions / Ownership");
        }
        die(true);
      }

      //search : get list of files from the current folder
      if (isset($_POST['type']) && $_POST['type'] == "search") {
        $dir = $this->cmfh->rootPath();
        $response = $this->cmfh->scan($this->cmfh->clean_path($_POST['path']), $_POST['content']);
        echo json_encode($response);
        exit();
      }

      // backup files
      if (isset($_POST['type']) && $_POST['type'] == "backup" && !empty($_POST['file'])) {
        $fileName = $_POST['file'];
        $fullPath = $this->cmfh->rootPath() . '/';
        if (!empty($_POST['path'])) {
          $relativeDirPath = $this->cmfh->clean_path($_POST['path']);
          $fullPath .= "{$relativeDirPath}/";
        }
        $date = date("dMy-His");
        $newFileName = "{$fileName}-{$date}.bak";
        $fullyQualifiedFileName = $fullPath . $fileName;
        try {
          if (!file_exists($fullyQualifiedFileName)) {
            throw new Exception("File {$fileName} not found");
          }
          if (copy($fullyQualifiedFileName, $fullPath . $newFileName)) {
            echo "Backup {$newFileName} created";
          } else {
            throw new Exception("Could not copy file {$fileName}");
          }
        } catch (Exception $e) {
          echo $e->getMessage();
        }
      }

      //upload using url
      if (isset($_POST['type']) && $_POST['type'] == "upload" && !empty($_REQUEST["uploadurl"])) {
        $path = $this->cmfh->rootPath();
        if ($this->cfmh->currentPath() != '') {
          $path .= '/' . $this->cfmh->currentPath();
        }

        $url = !empty($_REQUEST["uploadurl"]) && preg_match("|^http(s)?://.+$|", stripslashes($_REQUEST["uploadurl"])) ? stripslashes($_REQUEST["uploadurl"]) : null;

        //prevent 127.* domain and known ports
        $domain = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        $knownPorts = [22, 23, 25, 3306];

        if (preg_match("/^localhost$|^127(?:\.[0-9]+){0,2}\.[0-9]+$|^(?:0*\:)*?:?0*1$/i", $domain) || in_array($port, $knownPorts)) {
          $err = array("message" => "URL is not allowed");
          $this->cfmh->event_callback(array("fail" => $err));
          exit();
        }

        $use_curl = false;
        $temp_file = tempnam(sys_get_temp_dir(), "upload-");
        $fileinfo = new stdClass();
        $fileinfo->name = trim(basename($url), ".\x00..\x20");

        $allowed = ($this->cfmh->allowedUploadExtensions()) ? explode(',', $this->cfmh->allowedUploadExtensions()) : false;
        $ext = strtolower(pathinfo($fileinfo->name, PATHINFO_EXTENSION));
        $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;

        $err = false;

        if (!$isFileAllowed) {
          $err = array("message" => "File extension is not allowed");
          $this->cfmh->event_callback(array("fail" => $err));
          exit();
        }

        if (!$url) {
          $success = false;
        } else if ($use_curl) {
          @$fp = fopen($temp_file, "w");
          @$ch = curl_init($url);
          curl_setopt($ch, CURLOPT_NOPROGRESS, false);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt($ch, CURLOPT_FILE, $fp);
          @$success = curl_exec($ch);
          $curl_info = curl_getinfo($ch);
          if (!$success) {
            $err = array("message" => curl_error($ch));
          }
          @curl_close($ch);
          fclose($fp);
          $fileinfo->size = $curl_info["size_download"];
          $fileinfo->type = $curl_info["content_type"];
        } else {
          $ctx = stream_context_create();
          @$success = copy($url, $temp_file, $ctx);
          if (!$success) {
            $err = error_get_last();
          }
        }

        if ($success) {
          $success = rename($temp_file, $this->cfmh->get_file_path($path, $fileinfo));
        }

        if ($success) {
          $this->cfmh->event_callback(array("done" => $fileinfo));
        } else {
          unlink($temp_file);
          if (!$err) {
            $err = array("message" => "Invalid url parameter");
          }
          $this->cfmh->event_callback(array("fail" => $err));
        }
      }

      exit();
    }

    // Upload
    if (!empty($_FILES)) {
      $override_file_name = false;
      $f = $_FILES;
      $path = $this->cfmh->rootPath();
      $ds = DIRECTORY_SEPARATOR;
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      $errors = 0;
      $uploads = 0;
      $allowed = ($this->cfmh->allowedUploadExtensions()) ? explode(',', $this->cfmh->allowedUploadExtensions()) : false;
      $response = array(
        'status' => 'error',
        'info'   => 'Oops! Try again'
      );

      $filename = $f['file']['name'];
      $tmp_name = $f['file']['tmp_name'];
      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
      $isFileAllowed = ($allowed) ? in_array(
        $ext,
        $allowed
      ) : true;

      if (!$this->cfmh->is_valid_filename($filename) && !$this->cfmh->is_valid_filename($_REQUEST['fullpath'])) {
        $response = array(
          'status'    => 'error',
          'info'      => "Invalid File name!",
        );
        echo json_encode($response);
        exit();
      }

      $targetPath = $path . $ds;
      if (is_writable($targetPath)) {
        $fullPath = $path . '/' . str_replace("./", "_", $_REQUEST['fullpath']);
        $folder = substr($fullPath, 0, strrpos($fullPath, "/"));

        if (file_exists($fullPath) && !$override_file_name) {
          $ext_1 = $ext ? '.' . $ext : '';
          $fullPath = str_replace(
            $ext_1,
            '',
            $fullPath
          ) . '_' . date('ymdHis') . $ext_1;
        }

        if (!is_dir($folder)) {
          $old = umask(0);
          mkdir($folder, 0777, true);
          umask($old);
        }

        if (empty($f['file']['error']) && !empty($tmp_name) && $tmp_name != 'none' && $isFileAllowed) {
          if (move_uploaded_file($tmp_name, $fullPath)) {
            // Be sure that the file has been uploaded
            if (file_exists($fullPath)) {
              $response = array(
                'status'    => 'success',
                'info' => "file upload successful"
              );
            } else {
              $response = array(
                'status' => 'error',
                'info'   => 'Couldn\'t upload the requested file.'
              );
            }
          } else {
            $response = array(
              'status'    => 'error',
              'info'      => "Error while uploading files. Uploaded files $uploads",
            );
          }
        }
      } else {
        $response = array(
          'status' => 'error',
          'info'   => 'The specified folder for upload isn\'t writeable.'
        );
      }
      // Return the response
      echo json_encode($response);
      exit();
    }

    // Copy folder / file
    if (isset($_GET['copy'], $_GET['finish'])) {
      // from
      $copy = $_GET['copy'];
      $copy = $this->cfmh->clean_path($copy);
      // empty path
      if ($copy == '') {
        flashMsg('admin_filemanager', '<strong>Error</strong> Source path not defined', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }
      // abs path from
      $from = $this->cfmh->rootPath() . '/' . $copy;
      // abs path to
      $dest = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $dest .= '/' . $this->cfmh->currentPath();
      }
      $dest .= '/' . basename($from);
      // move?
      $move = isset($_GET['move']);
      // copy/move/duplicate
      if ($from != $dest) {
        $msg_from = trim($this->cfmh->currentPath() . '/' . basename($from), '/');
        if ($move) { // Move and to != from so just perform move
          $rename = $this->cfmh->rename($from, $dest);
          if ($rename) {
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Moved from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($copy), $this->cfmh->enc($msg_from)), 'success');
          } elseif ($rename === null) {
            flashMsg('admin_filemanager', '<strong>Error</strong> File or folder with this path already exists. Please try again.', 'warning');
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while moving from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($copy), $this->cfmh->enc($msg_from)), 'danger');
          }
        } else { // Not move and to != from so copy with original name
          if ($this->cfmh->rcopy($from, $dest)) {
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Copied from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($copy), $this->cfmh->enc($msg_from)), 'success');
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while copying from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($copy), $this->cfmh->enc($msg_from)), 'danger');
          }
        }
      } else {
        if (!$move) { //Not move and to = from so duplicate
          $msg_from = trim($this->cfmh->currentPath() . '/' . basename($from), '/');
          $fn_parts = pathinfo($from);
          $extension_suffix = '';
          if (!is_dir($from)) {
            $extension_suffix = '.' . $fn_parts['extension'];
          }
          //Create new name for duplicate
          $fn_duplicate = $fn_parts['dirname'] . '/' . $fn_parts['filename'] . '-' . date('YmdHis') . $extension_suffix;
          $loop_count = 0;
          $max_loop = 1000;
          // Check if a file with the duplicate name already exists, if so, make new name (edge case...)
          while (file_exists($fn_duplicate) & $loop_count < $max_loop) {
            $fn_parts = pathinfo($fn_duplicate);
            $fn_duplicate = $fn_parts['dirname'] . '/' . $fn_parts['filename'] . '-copy' . $extension_suffix;
            $loop_count++;
          }
          if ($this->cfmh->rcopy($from, $fn_duplicate, False)) {
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Copied from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($copy), $this->cfmh->enc($fn_duplicate)), 'success');
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while copying from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($copy), $this->cfmh->enc($fn_duplicate)), 'danger');
          }
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Paths must not match. Please try again.', 'warning');
        }
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Mass copy files/ folders
    if (isset($_POST['file'], $_POST['copy_to'], $_POST['finish'])) {
      // from
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }
      // to
      $copy_to_path = $this->cfmh->rootPath();
      $copy_to = $this->cfmh->clean_path($_POST['copy_to']);
      if ($copy_to != '') {
        $copy_to_path .= '/' . $copy_to;
      }
      if ($path == $copy_to_path) {
        flashMsg('admin_filemanager', '<strong>Error</strong> Paths must not match. Please try again.', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }
      if (!is_dir($copy_to_path)) {
        if (!$this->cfmh->mkdir($copy_to_path, true)) {
          flashMsg('admin_filemanager', '<strong>Error</strong> Unable to create destination folder. Please try again.', 'danger');
          fm_set_msg('Unable to create destination folder', 'error');
          redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
        }
      }
      // move?
      $move = isset($_POST['move']);
      // copy/move
      $errors = 0;
      $files = $_POST['file'];
      if (is_array($files) && count($files)) {
        foreach ($files as $f) {
          if ($f != '') {
            // abs path from
            $from = $path . '/' . $f;
            // abs path to
            $dest = $copy_to_path . '/' . $f;
            // do
            if ($move) {
              $rename = $this->cfmh->rename($from, $dest);
              if ($rename === false) {
                $errors++;
              }
            } else {
              if (!$this->cfmh->rcopy($from, $dest)) {
                $errors++;
              }
            }
          }
        }
        if (
          $errors == 0
        ) {
          $msg = $move ? 'Selected files and folders moved' : 'Selected files and folders copied';
          flashMsg('admin_filemanager', "<strong>Success</strong> {$msg}", 'success');
        } else {
          $msg = $move ? 'Error while moving items' : 'Error while copying items';
          flashMsg('admin_filemanager', "<strong>Error</strong> {$msg}", 'danger');
        }
      } else {
        flashMsg('admin_filemanager', "<strong>Error</strong> Nothing selected", 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Rename
    if (isset($_GET['ren'], $_GET['to'])) {
      // old name
      $old = $_GET['ren'];
      $old = $this->cfmh->clean_path($old);
      $old = str_replace('/', '', $old);
      // new name
      $new = $_GET['to'];
      $new = $this->cfmh->clean_path(strip_tags($new));
      $new = str_replace('/', '', $new);
      // path
      $path = FM_ROOT_PATH;
      if (FM_PATH != '') {
        $path .= '/' . FM_PATH;
      }
      // rename
      if ($this->cfmh->is_valid_filename($new) && $old != '' && $new != '') {
        if ($this->cfmh->rename($path . '/' . $old, $path . '/' . $new)) {
          flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Renamed from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($old), $this->cfmh->enc($new)), 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while renaming from <strong>%s</strong> to <strong>%s</strong>', $this->cfmh->enc($old), $this->cfmh->enc($new)), 'danger');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Invalid characters in file name. Please try again.', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Pack files
    if (isset($_POST['group']) && (isset($_POST['zip']) || isset($_POST['tar']))) {
      $path = $this->cfmh->rootPath();
      $ext = 'zip';
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      //set pack type
      $ext = isset($_POST['tar']) ? 'tar' : 'zip';

      if (($ext == "zip" && !class_exists('ZipArchive')) || ($ext == "tar" && !class_exists('PharData'))) {
        flashMsg('admin_filemanager', '<strong>Error</strong> Operations with archives are not available', 'danger');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }

      $files = $_POST['file'];
      if (!empty($files)) {
        chdir($path);

        if (count($files) == 1) {
          $one_file = reset($files);
          $one_file = basename($one_file);
          $zipname = $one_file . '_' . date('ymd_His') . '.' . $ext;
        } else {
          $zipname = 'archive_' . date('ymd_His') . '.' . $ext;
        }

        if ($ext == 'zip') {
          $zipper = new FM_Zipper();
          $res = $zipper->create($zipname, $files);
        } elseif ($ext == 'tar') {
          $tar = new FM_Zipper_Tar();
          $res = $tar->create($zipname, $files);
        }

        if ($res) {
          flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Archive <strong>%s</strong> Created', fm_enc($zipname)), 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Archive not created', 'warning');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Nothing selected', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Unpack
    if (isset($_GET['unzip'])) {
      $unzip = $_GET['unzip'];
      $unzip = $this->cfmh->clean_path($unzip);
      $unzip = str_replace('/', '', $unzip);
      $isValid = false;

      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      if ($unzip != '' && is_file($path . '/' . $unzip)) {
        $zip_path = $path . '/' . $unzip;
        $ext = pathinfo($zip_path, PATHINFO_EXTENSION);
        $isValid = true;
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> File not found', 'danger');
      }


      if (($ext == "zip" && !class_exists('ZipArchive')) || ($ext == "tar" && !class_exists('PharData'))) {
        flashMsg('admin_filemanager', '<strong>Error</strong> Operations with archives are not available', 'danger');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }

      if ($isValid) {
        //to folder
        $tofolder = '';
        if (isset($_GET['tofolder'])) {
          $tofolder = pathinfo($zip_path, PATHINFO_FILENAME);
          if ($this->cfmh->mkdir($path . '/' . $tofolder, true)) {
            $path .= '/' . $tofolder;
          }
        }

        if ($ext == "zip") {
          $zipper = new FM_Zipper();
          $res = $zipper->unzip($zip_path, $path);
        } elseif ($ext == "tar") {
          try {
            $gzipper = new PharData($zip_path);
            if (@$gzipper->extractTo($path, null, true)) {
              $res = true;
            } else {
              $res = false;
            }
          } catch (Exception $e) {
            //TODO:: need to handle the error
            $res = true;
          }
        }

        if ($res) {
          flashMsg('admin_filemanager', '<strong>Success</strong> Archive unpacked', 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Archive not unpacked', 'warning');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> File not found', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Download
    if (isset($_GET['dl'])) {
      $dl = $_GET['dl'];
      $dl = $this->cfmh->clean_path($dl);
      $dl = str_replace('/', '', $dl);
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }
      if ($dl != '' && is_file($path . '/' . $dl)) {
        $this->cfmh->download_file($path . '/' . $dl, $dl, 1024);
        exit;
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> File not found. Please try again.', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }
    }

    // Change Perms (not for Windows)
    if (isset($_POST['chmod']) && $this->cfmh->isWindows()) {
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      $file = $_POST['chmod'];
      $file = $this->cfmh->clean_path($file);
      $file = str_replace('/', '', $file);
      if ($file == '' || (!is_file($path . '/' . $file) && !is_dir($path . '/' . $file))) {
        flashMsg('admin_filemanager', '<strong>Error</strong> File not found. Please try again.', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }

      $mode = 0;
      if (!empty($_POST['ur'])) {
        $mode |= 0400;
      }
      if (!empty($_POST['uw'])) {
        $mode |= 0200;
      }
      if (!empty($_POST['ux'])) {
        $mode |= 0100;
      }
      if (!empty($_POST['gr'])) {
        $mode |= 0040;
      }
      if (!empty($_POST['gw'])) {
        $mode |= 0020;
      }
      if (!empty($_POST['gx'])) {
        $mode |= 0010;
      }
      if (!empty($_POST['or'])) {
        $mode |= 0004;
      }
      if (!empty($_POST['ow'])) {
        $mode |= 0002;
      }
      if (!empty($_POST['ox'])) {
        $mode |= 0001;
      }

      if (@chmod($path . '/' . $file, $mode)) {
        flashMsg('admin_filemanager', '<strong>Success</strong> Permissions changed.', 'success');
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Permissions not changed.', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Delete file / folder
    if (isset($_GET['del'])) {
      $del = str_replace('/', '', $this->cfmh->clean_path($_GET['del']));
      if (
        $del != '' && $del != '..' && $del != '.'
      ) {
        $path = $this->cfmh->rootPath();
        if ($this->cfmh->currentPath() != '') {
          $path .= '/' . $this->cfmh->currentPath();
        }
        $is_dir = is_dir($path . '/' . $del);
        if ($this->cfmh->rdelete($path . '/' . $del)) {
          $msg = $is_dir ? 'Folder <strong>%s</strong> Deleted' : 'File <strong>%s</strong> Deleted';
          flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf($msg, $this->cfmh->enc($del)), 'success');
        } else {
          $msg = $is_dir ? 'Folder <strong>%s</strong> not deleted' : 'File <strong>%s</strong> not deleted';
          $msg = $is_dir ? 'Folder <strong>%s</strong> Deleted' : 'File <strong>%s</strong> Deleted';
          flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf($msg, $this->cfmh->enc($del)), 'danger');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Invalid file or folder name', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    // Mass deleting
    if (isset($_POST['group'], $_POST['delete'])) {
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      $errors = 0;
      $files = $_POST['file'];
      if (is_array($files) && count($files)) {
        foreach ($files as $f) {
          if ($f != '') {
            $new_path = $path . '/' . $f;
            if (!$this->cfmh->rdelete($new_path)) {
              $errors++;
            }
          }
        }
        if (
          $errors == 0
        ) {
          flashMsg('admin_filemanager', '<strong>Success</strong> Selected files and folder deleted', 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Error while deleting items', 'danger');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Nothing selected', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    }

    /*************************** /ACTIONS ***************************/

    // get current path
    $path = $this->cfmh->rootPath();
    if ($this->cfmh->currentPath() != '') {
      $path .= '/' . $this->cfmh->currentPath();
    }

    // check path
    if (!is_dir($path)) {
      redirectTo('admin/files/');
    }

    // get parent folder
    $parent = $this->cfmh->get_parent_path($this->cfmh->currentPath());

    $objects = is_readable($path) ? scandir($path) : array();
    $folders = array();
    $files = array();
    $current_path = array_slice(explode("/", $path), -1)[0];
    if (is_array($objects) && $this->cfmh->is_exclude_items($current_path)) {
      foreach ($objects as $file) {
        if ($file == '.' || $file == '..') {
          continue;
        }
        if (!$this->cfmh->showHiddenFiles() && substr($file, 0, 1) === '.') {
          continue;
        }
        $new_path = $path . '/' . $file;
        if (@is_file($new_path) && $this->cfmh->is_exclude_items($file)) {
          $files[] = $file;
        } elseif (
          @is_dir($new_path) && $file != '.' && $file != '..' && $this->cfmh->is_exclude_items($file)
        ) {
          $folders[] = $file;
        }
      }
    }

    // Sort files and folders
    if (!empty($files)) {
      natcasesort($files);
    }
    if (!empty($folders)) {
      natcasesort($folders);
    }

    // Set headers
    header("Content-Type: text/html; charset=utf-8");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");

    // Load view
    $this->load->view('common/filemanager', $this->data, 'admin');
    exit;
  }

  /**
   * Upload Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function upload(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('upload_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to upload files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Load view
    $this->load->view('common/filemanager', $this->data, 'admin');
    exit;
  }

  /**
   * Copy Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function copy(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('upload_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to upload files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Load view
    $this->load->view('common/filemanager', $this->data, 'admin');
    exit;
  }
}
