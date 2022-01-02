<?php

use Cornerstone\FileManager;

// Based on Tiny File Manager ~ https://tinyfilemanager.github.io/

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

    // Get path
    $p = isset($this->request->get['p']) ? $this->request->get['p'] : (isset($this->request->post['p']) ? $this->request->post['p'] : '');

    // Clean the path
    $p = $this->cfmh->clean_path($p);

    // Set current path
    $this->cfmh->setCurrentPath($p);

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

    // Set Breadcrumbs
    $this->data['breadcrumbs'] = array(
      array(
        'text' => 'Dashboard',
        'href' => get_site_url('admin')
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

    // for ajax request - save
    $input = file_get_contents('php://input');
    $_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

    /*************************** ACTIONS ***************************/

    // TODO AJAX Request
    if (isset($_POST['ajax'])) {

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

      exit();
    }

    // Mass copy files/ folders
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "bulk-copy") {
      $this->bulkcopy(...$params);
      exit;
    }

    // Pack files
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group'], $_POST['action']) && ($_POST['action'] == "zip" || $_POST['action'] == "tar")) {
      $path = $this->cfmh->rootPath();
      $ext = 'zip';
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      //set pack type
      $ext = (isset($_POST['action']) && $_POST['action'] == "tar") ? 'tar' : 'zip';

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
          $zipper = new Cornerstone\CS_Zipper();
          $res = $zipper->create($zipname, $files);
        } elseif ($ext == 'tar') {
          $tar = new Cornerstone\CS_Zipper_Tar();
          $res = $tar->create($zipname, $files);
        }

        if ($res) {
          flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Archive "<em>%s</em>" Created', $this->cfmh->enc($zipname)), 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Archive not created', 'warning');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Nothing selected', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      exit;
    }

    // Mass deleting
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group'], $_POST['action']) && $_POST['action'] == "delete") {
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      $errors = 0;
      $files = (!empty($_POST['file'])) ? $_POST['file'] : '';
      if (is_array($files) && count($files)) {
        foreach ($files as $f) {
          if ($f != '') {
            $new_path = $path . '/' . $f;
            if (!$this->cfmh->rdelete($new_path)) {
              $errors++;
            }
          }
        }
        if ($errors == 0) {
          flashMsg('admin_filemanager', '<strong>Success</strong> Selected file(s) / folder(s) deleted', 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Error while deleting items', 'danger');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Nothing selected', 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      exit;
    }

    /*************************** /ACTIONS ***************************/

    //--- FILE MANAGER MAIN

    // Set headers
    header("Content-Type: text/html; charset=utf-8");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");

    // Page specific outputs
    $this->data['opPermissionColumns'] = (!$this->cfmh->isWindows() && !$this->cfmh->hideCols()) ? True : False;

    // get current path
    $this->data['path'] = $this->cfmh->rootPath();
    $this->data['input_path'] = '';
    if ($this->cfmh->currentPath() != '') {
      $this->data['path'] .= '/' . $this->cfmh->currentPath();
      $this->data['input_path'] = $this->cfmh->currentPath();
    }

    // Jump to folder options
    $this->getDestinationFolders(true);

    // check path
    if (!is_dir($this->data['path'])) {
      redirectTo('admin/files/');
    }

    // get parent folder
    $this->data['parent'] = $this->cfmh->get_parent_path($this->cfmh->currentPath());

    $this->data['objects'] = is_readable($this->data['path']) ? scandir($this->data['path']) : array();
    $this->data['folders'] = array();
    $this->data['files'] = array();
    $this->data['current_path'] = array_slice(explode("/", $this->data['path']), -1)[0];
    if (is_array($this->data['objects']) && !$this->cfmh->is_exclude_items($this->data['current_path'])) {
      foreach ($this->data['objects'] as $file) {
        if ($file == '.' || $file == '..') {
          continue;
        }
        if (!$this->cfmh->showHiddenFiles() && substr($file, 0, 1) === '.') {
          continue;
        }
        $new_path = $this->data['path'] . '/' . $file;
        if (@is_file($new_path) && !$this->cfmh->is_exclude_items($file)) {
          $this->data['files'][] = $file;
        } elseif (
          @is_dir($new_path) && $file != '.' && $file != '..' && !$this->cfmh->is_exclude_items($file)
        ) {
          $this->data['folders'][] = $file;
        }
      }
    }

    $this->data['num_files'] = count($this->data['files']);
    $this->data['num_folders'] = count($this->data['folders']);
    $this->data['all_files_size'] = 0;

    // Sort and output files and folders
    if (!empty($this->data['files'])) {
      natcasesort($this->data['files']);
    }
    $this->data['opFiles'] = '';
    $ik = 6070;
    foreach ($this->data['files'] as $f) {
      $is_link = is_link($this->data['path'] . '/' . $f);
      $img = $is_link ? 'fa fa-file-text-o' : $this->cfmh->get_file_icon_class($this->data['path'] . '/' . $f);
      $modif_raw = filemtime($this->data['path'] . '/' . $f);
      $modif = date('d/m/Y H:i', $modif_raw);
      $filesize_raw = $this->cfmh->get_size($this->data['path'] . '/' . $f);
      $filesize = $this->cfmh->get_file_size($filesize_raw);
      $filelink = get_site_url('admin/files/view/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;file=' . urlencode($f));
      $this->data['all_files_size'] += $filesize_raw;
      $perms = substr(decoct(fileperms($this->data['path'] . '/' . $f)), -4);
      if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
        $owner = posix_getpwuid(fileowner($this->data['path'] . '/' . $f));
        $group = posix_getgrgid(filegroup($this->data['path'] . '/' . $f));
      } else {
        $owner = array('name' => '?');
        $group = array('name' => '?');
      }

      // Check for image type
      if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg', 'webp', 'avif'))) {
        $imagePreview = $this->cfmh->enc(get_site_url(($this->cfmh->currentPath() != '' ? '/' . $this->cfmh->currentPath() : '') . '/' . $f));
        $file_name_link = '<a href="' . $filelink . '" data-preview-image="' . $imagePreview . '" title="' . $this->cfmh->enc($f) . '">';
      } else {
        $file_name_link = '<a href=" ' . $filelink . '" title="' . $this->cfmh->enc($f) . '">';
      }

      // Check to output permission columns
      $op_file_cols = ($this->data['opPermissionColumns']) ? '<td><a title="Change Permissions" href="' . get_site_url('admin/files/chmod/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;item=' . urlencode($f)) . '">' . $perms . '</a></td><td> ' . $owner['name'] . ':' . $group['name'] . '</td>' : '';

      // Check if allowed to delete
      $deleteOP = $this->role->canDo('delete_files') ? '<button type="button" title="Delete File" data-tippy-content="Delete File" class="delete-this" data-t="File" data-f="' . urlencode($f) . '" data-name="' . $f . '"><i class="far fa-trash-alt"></i></button>' : '';

      // Check if allowed to rename
      $renameOP = $this->role->canDo('rename_files') ? '<button type="button" title="Rename File" data-tippy-content="Rename File" class="rename-this" data-name="' . $this->cfmh->enc(addslashes($f)) . '"><i class="fas fa-i-cursor"></i></button>' : '';

      // Check if allowed to copy
      $copyOP = $this->role->canDo('copy_files') ? '<a title="Copy file" data-tippy-content="Copy file" href="' . get_site_url('admin/files/copy/') . '?p=' . urlencode($this->cfmh->currentPath()) . '&amp;copy=' . urlencode(trim($f, '/')) . '" rel="modal:open"><i class="far fa-copy"></i></a>' : '';

      // Set to output
      $this->data['opFiles'] .= '<tr>
          <td class="custom-checkbox-td">
            <label>
              <input type="checkbox"  id="' . $ik . '" name="file[]" value="' . $this->cfmh->enc($f) . '">
              <span></span>
            </label>
          </td>
          <td>
            <div class="filename">
              ' . $file_name_link . '
                <i class="' . $img . '"></i> ' . $this->cfmh->convert_win($this->cfmh->enc($f)) . '
              </a>
              ' . ($is_link ? ' &rarr; <i>' . readlink($this->data['path'] . '/' . $f) . '</i>' : '') . '
            </div>
          </td>
          <td data-sort=b-"' . str_pad($filesize_raw, 18, "0", STR_PAD_LEFT) . '">
            <span title="' . $filesize_raw . ' bytes">' . $filesize . '</span>
          </td>
          <td data-sort="b-' . $modif_raw . '">' . $modif  . '</td>
          ' . $op_file_cols . '
          <td class="inline-actions">
            <a title="Preview" href="' . $filelink . '&quickView=1' . '" rel="modal:open"><i class="far fa-eye"></i></a>
            ' . $deleteOP . $renameOP . $copyOP . '
            <a title="Direct link" href="' . $this->cfmh->enc(get_site_url('files' . ($this->cfmh->currentPath() != '' ? '/' . $this->cfmh->currentPath() : '') . '/' . $f)) . '" target="_blank"><i class="fas fa-link"></i></a>
            <a title="Download" href="' . get_site_url('admin/files/download/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;file=' . urlencode($f)) . '"><i class="fas fa-download"></i></a>
          </td>
        </tr>';
      flush();
      $ik++;
    }

    if (!empty($this->data['folders'])) {
      natcasesort($this->data['folders']);
    }
    $this->data['opFolders'] = '';
    $ii = 3399;
    foreach ($this->data['folders'] as $f) {
      $is_link = is_link($this->data['path'] . '/' . $f);
      $img = $is_link ? 'icon-link_folder' : 'fa fa-folder-o';
      $modif_raw = filemtime($this->data['path'] . '/' . $f);
      $modif = date('d/m/Y H:i', $modif_raw);
      if ($this->cfmh->showDirectorySize()) {
        $directory_info = $this->cfmh->get_directory_info($this->data['path'] . '/' . $f);
        $filesize_raw = $directory_info[0];
        $filesize = $this->cfmh->get_file_size($filesize_raw);
        $directory_info_op = ' data-tippy-content="Folders: ' . $directory_info[2] . ', Files: ' . $directory_info[1] . '"';
      } else {
        $filesize_raw = "";
        $filesize = "Folder";
        $directory_info_op = "";
      }
      $perms = substr(decoct(fileperms($this->data['path'] . '/' . $f)), -4);
      if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
        $owner = posix_getpwuid(fileowner($this->data['path'] . '/' . $f));
        $group = posix_getgrgid(filegroup($this->data['path'] . '/' . $f));
      } else {
        $owner = array('name' => '?');
        $group = array('name' => '?');
      }

      // Check to output permission columns
      $op_folder_cols = ($this->data['opPermissionColumns']) ? '<td><a title="Change Permissions" href="' . get_site_url('admin/files/chmod/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;item=' . urlencode($f)) . '">' . $perms . '</a></td><td> ' . $owner['name'] . ':' . $group['name'] . '</td>' : '';

      // Check if allowed to delete
      $deleteOP = $this->role->canDo('delete_files') ? '<button type="button" title="Delete Folder" data-tippy-content="Delete Folder" class="delete-this" data-t="Folder" data-f="' . urlencode($f) . '" data-name="' . $f . '"><i class="far fa-trash-alt"></i></button>' : '';

      // Check if allowed to rename
      $renameOP = $this->role->canDo('rename_files') ? '<button type="button" title="Rename Folder" data-tippy-content="Rename Folder" class="rename-this" data-name="' . $this->cfmh->enc(addslashes($f)) . '"><i class="fas fa-i-cursor"></i></button>' : '';

      // Check if allowed to copy
      $copyOP = $this->role->canDo('copy_files') ? '<a title="Copy folder" data-tippy-content="Copy folder" href="' . get_site_url('admin/files/copy/') . '?p=' . urlencode($this->cfmh->currentPath()) . '&amp;copy=' . urlencode(trim($f, '/')) . '" rel="modal:open"><i class="far fa-copy"></i></a>' : '';

      // Set to output
      $this->data['opFolders'] .= '<tr>
          <td class="custom-checkbox-td">
            <label>
              <input type="checkbox"  id="' . $ii . '" name="file[]" value="' . $this->cfmh->enc($f) . '">
              <span></span>
            </label>
          </td>
          <td>
            <div class="filename"><a href="?p=' . urlencode(trim($this->cfmh->currentPath() . '/' . $f, '/')) . '"><i class="' . $img . '"></i> ' . $this->cfmh->convert_win($this->cfmh->enc($f)) . '
              </a>' . ($is_link ? ' &rarr; <i>' . readlink($this->data['path'] . '/' . $f) . '</i>' : '') . '</div>
          </td>
          <td data-sort="a-' . str_pad($filesize_raw, 18, "0", STR_PAD_LEFT) . '">
            <span' . $directory_info_op . '>' . $filesize . '</span>
          </td>
          <td data-sort="a-' . $modif_raw . '">' . $modif . '</td>
          ' . $op_folder_cols . '
          <td class="inline-actions">
            ' . $deleteOP . $renameOP . $copyOP . '
          </td>
        </tr>';
      flush();
      $ii++;
    }

    // Calculate footer information
    $this->data['opFullSize'] = 'Full Size: <span class="badge badge-light">' . $this->cfmh->get_file_size($this->data['all_files_size']) . '</span>';
    $this->data['opFileCount'] = 'File: <span class="badge badge-light">' . $this->data['num_files'] . '</span>';
    $this->data['opFolderCount'] = 'Folder: <span class="badge badge-light">' . $this->data['num_folders'] . '</span>';
    $this->data['opPartitionSize'] = 'Partition Size: <span class="badge badge-light">' . $this->cfmh->get_file_size(@disk_free_space($this->data['path'])) . '</span> free of <span class="badge badge-light">' . $this->cfmh->get_file_size(@disk_total_space($this->data['path'])) . '</span>';

    // Set Breadcrumbs
    if ($this->cfmh->currentPath() != '') {
      $exploded = explode('/', $this->cfmh->currentPath());
      $parent = '';
      foreach ($exploded as $folderName) {
        $parent = trim($parent . '/' . $folderName, '/');
        $parent_enc = urlencode($parent);
        $this->data['breadcrumbs'][] = array(
          'text' => $folderName,
          'href' => get_site_url("admin/files/?p={$parent_enc}")
        );
      }
    }

    // Load view
    $this->load->view('files/index', $this->data, 'admin');
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
    if (!$this->role->canDo('add_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to upload files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    //Check if page posted and process form if it is - UPLOAD FROM URL
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "upload-url") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get path
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }

      $this->data['uploadurl'] = !empty($_POST["uploadurl"]) && preg_match("|^http(s)?://.+$|", stripslashes($_POST["uploadurl"])) ? stripslashes($_POST["uploadurl"]) : null;

      //prevent 127.* domain and known ports
      $domain = parse_url($this->data['uploadurl'], PHP_URL_HOST);
      $port = parse_url($this->data['uploadurl'], PHP_URL_PORT);
      $knownPorts = [22, 23, 25, 3306];

      if (preg_match("/^localhost$|^127(?:\.[0-9]+){0,2}\.[0-9]+$|^(?:0*\:)*?:?0*1$/i", $domain) || in_array($port, $knownPorts)) {
        flashMsg('files_upload', '<strong>Error</strong> URL is not allowed. Please try again.', 'danger');
      }

      $use_curl = false;
      $temp_file = tempnam(sys_get_temp_dir(), "upload-");
      $fileinfo = new stdClass();
      $fileinfo->name = trim(basename($this->data['uploadurl']), ".\x00..\x20");

      $allowed = ($this->cfmh->allowedUploadExtensions()) ? explode(',', $this->cfmh->allowedUploadExtensions()) : false;
      $ext = strtolower(pathinfo($fileinfo->name, PATHINFO_EXTENSION));
      $isFileAllowed = ($allowed) ? in_array($ext, $allowed) : true;

      $err = false;

      if (!$isFileAllowed) {
        flashMsg('files_upload', '<strong>Error</strong> File extension is not allowed. Please try again.', 'danger');
      }

      if (!$this->data['uploadurl']) {
        $success = false;
      } else if ($use_curl) {
        @$fp = fopen($temp_file, "w");
        @$ch = curl_init($this->data['uploadurl']);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        @$success = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        if (!$success) {
          $err = curl_error($ch);
        }
        @curl_close($ch);
        fclose($fp);
        $fileinfo->size = $curl_info["size_download"];
        $fileinfo->type = $curl_info["content_type"];
      } else {
        $ctx = stream_context_create();
        @$success = copy($this->data['uploadurl'], $temp_file, $ctx);
        if (!$success) {
          $err = error_get_last();
        }
      }

      if ($success) {
        $success = rename($temp_file, $this->cfmh->get_file_path($path, $fileinfo));
      }

      if ($success) {
        flashMsg('admin_filemanager', '<strong>Success</strong> File "<em>' . $fileinfo->name . '</em>" has uploaded from the URL provided', 'success');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      } else {
        unlink($temp_file);
        if (!$err) {
          flashMsg('files_upload', '<strong>Error</strong> Invalid url parameter. Please try again.', 'danger');
        }
        flashMsg('files_upload', '<strong>Error</strong> ' . $err . '. Please try again.', 'danger');
      }
    } else if (empty($_POST['action']) && !empty($_FILES)) { // Dropzone Upload
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

    // Set paths
    $this->data['current_path'] = $this->cfmh->currentPath();
    $this->data['current_path_enc'] = $this->cfmh->enc($this->cfmh->currentPath());
    $this->data['destination_folder'] = $this->cfmh->enc($this->cfmh->convert_win($this->cfmh->currentPath()));

    // Set options for Dropzone
    $this->data['max_file_size'] = $this->cfmh->maxUploadSize();
    $this->data['accepted_files'] = '';
    $extArr = explode(',', $this->cfmh->allowedUploadExtensions());
    if ($this->cfmh->allowedUploadExtensions() && $extArr) {
      array_walk($extArr, function (&$x) {
        $x = ".$x";
      });
      $this->data['accepted_files'] = implode(',', $extArr);
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'File Upload',
      'href' => get_site_url("admin/files/upload/?p={$this->cfmh->currentPath()}")
    );

    // Load view
    $this->load->view('files/upload', $this->data, 'admin');
    exit;
  }

  /**
   * Create Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function create(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('add_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to add files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "create") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Create item
      $type = htmlspecialchars(trim($_POST['item_type']));
      $new = str_replace('/', '', $this->cfmh->clean_path(strip_tags($_POST['item_name'])));
      if ($this->cfmh->is_valid_filename($new) && $new != '' && $new != '..' && $new != '.') {
        // set path
        $path = $this->cfmh->rootPath();
        if ($this->cfmh->currentPath() != '') {
          $path .= '/' . $this->cfmh->currentPath();
        }
        if ($type == "file") {
          if (!file_exists($path . '/' . $new)) {
            if ($this->cfmh->is_valid_ext($new)) {
              @fopen($path . '/' . $new, 'w') or die('Cannot open file:  ' . $new);
              flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('File "<em>%s</em>" created', $this->cfmh->enc($new)), 'success');
            } else {
              flashMsg('admin_filemanager', '<strong>Error</strong> File extension is not allowed. Please try again.', 'danger');
            }
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('File "<em>%s</em>" already exists', $this->cfmh->enc($new)), 'danger');
          }
        } else {
          if ($this->cfmh->mkdir($path . '/' . $new, false) === true) {
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Folder "<em>%s</em>" created', $this->cfmh->enc($new)), 'success');
          } elseif ($this->cfmh->mkdir($path . '/' . $new, false) === $path . '/' . $new) {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Folder "<em>%s</em>" already exists', $this->cfmh->enc($new)), 'danger');
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Folder "<em>%s</em>" not created. Please try again.', $this->cfmh->enc($new)), 'danger');
          }
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Invalid characters in file or folder name. Please try again.', 'danger');
      }
    } else { // Page wasn't posted.
      flashMsg('admin_filemanager', '<strong>Error</strong> There was an error creating your new item. Please try again.', 'warning');
    }
    redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    exit;
  }

  /**
   * View File Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function view(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('view_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to view files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // get current path
    $path = $this->cfmh->rootPath();
    if ($this->cfmh->currentPath() != '') {
      $path .= '/' . $this->cfmh->currentPath();
    }

    // Get file information
    $this->data['quickView'] = (isset($this->request->get['quickView']) && $this->request->get['quickView'] == 1) ? true : false;
    $file = str_replace('/', '', $this->cfmh->clean_path($this->request->get['file'], false));
    $this->data['file'] = $file;
    if ($file == '' || !is_file($path . '/' . $file) || $this->cfmh->is_exclude_items($file)) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> File not found. Please try again.', 'warning');
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      exit;
    }

    $file_url = get_site_url('files' . $this->cfmh->convert_win(($this->cfmh->currentPath() != '' ? '/' . $this->cfmh->currentPath() : '') . '/' . $file));
    $file_path = $path . '/' . $file;

    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    $mime_type = $this->cfmh->get_mime_type($file_path);
    $filesize_raw = $this->cfmh->get_size($file_path);
    $filesize = $this->cfmh->get_file_size($filesize_raw);

    $is_zip = false;
    $is_gzip = false;
    $is_image = false;
    $is_audio = false;
    $is_video = false;
    $is_text = false;
    $is_onlineViewer = false;

    $view_title = 'File';
    $filenames = false; // for zip
    $content = ''; // for text
    $online_viewer = strtolower($this->cfmh->onlineViewer());

    if ($online_viewer && $online_viewer !== 'false' && in_array($ext, $this->cfmh->get_onlineViewer_exts())) {
      $is_onlineViewer = true;
    } elseif ($ext == 'zip' || $ext == 'tar') {
      $is_zip = true;
      $view_title = 'Archive';
      $filenames = $this->cfmh->get_zip_info(
        $file_path,
        $ext
      );
    } elseif (in_array($ext, $this->cfmh->get_image_exts())) {
      $is_image = true;
      $view_title = 'Image';
    } elseif (in_array($ext, $this->cfmh->get_audio_exts())) {
      $is_audio = true;
      $view_title = 'Audio';
    } elseif (in_array($ext, $this->cfmh->get_video_exts())) {
      $is_video = true;
      $view_title = 'Video';
    } elseif (
      in_array($ext, $this->cfmh->get_text_exts()) || substr($mime_type, 0, 4) == 'text' || in_array($mime_type, $this->cfmh->get_text_mimes())
    ) {
      $is_text = true;
      $content = file_get_contents($file_path);
    }

    // Set Breadcrumbs
    if ($this->cfmh->currentPath() != '') {
      $exploded = explode('/', $this->cfmh->currentPath());
      $parent = '';
      foreach ($exploded as $folderName) {
        $parent = trim($parent . '/' . $folderName, '/');
        $parent_enc = urlencode($parent);
        $this->data['breadcrumbs'][] = array(
          'text' => $folderName,
          'href' => get_site_url("admin/files/?p={$parent_enc}")
        );
      }
    }
    $this->data['breadcrumbs'][] = array(
      'text' => $this->cfmh->enc($this->cfmh->convert_win($file)),
      'href' => get_site_url("admin/files/view/?p={$this->cfmh->currentPath()}&file={$this->request->get['file']}")
    );

    // Set paths
    $this->data['current_path'] = $this->cfmh->currentPath();

    // Get file icon
    $is_link = is_link($file_path);
    $this->data['file_icon'] = $is_link ? 'fa fa-file-text-o' : $this->cfmh->get_file_icon_class($file_path);

    // Set content OP
    $this->data['headerOP'] = '';
    $this->data['contentOP'] = '';
    // Check if Quick View and output file information if not
    if (!$this->data['quickView']) {
      // Get file size
      $file_size_op = ($filesize_raw <= 1000) ? "$filesize_raw bytes" : $filesize;

      // Set to output
      $this->data['headerOP'] .= '<h1 class="cs-mb-0"><strong>' . $view_title . ' "' . $this->cfmh->enc($this->cfmh->convert_win($file)) . '" <i class="' . $this->data['file_icon'] . '"></i></strong></h1>
      <p class="cs-subtitle1" style="word-wrap:break-word;">
        File path: <span data-tippy-content="Full Path: ' . $this->cfmh->enc($this->cfmh->convert_win($file_path)) . '">' . $this->cfmh->enc($this->cfmh->convert_win('/' . $this->cfmh->currentPath() . '/' . $file)) . '</span><br>
        File size: ' . $file_size_op . '<br>
        MIME-type: ' . $mime_type . '<br>';

      // If ZIP info
      if (($is_zip || $is_gzip) && $filenames !== false) {
        $total_files = 0;
        $total_comp = 0;
        $total_uncomp = 0;
        foreach ($filenames as $fn) {
          if (!$fn['folder']) {
            $total_files++;
          }
          $total_comp += $fn['compressed_size'];
          $total_uncomp += $fn['filesize'];
        }
        // Set to output
        $this->data['headerOP'] .= 'Files in archive: ' . $total_files . '<br>
          Total size: ' . $this->cfmh->get_file_size($total_uncomp) . '<br>
          Size in archive: ' . $this->cfmh->get_file_size($total_comp) . '<br>
          Compression: ' . round(($total_comp / $total_uncomp) * 100) . '%<br>';
      }

      // If Image info
      if ($is_image) {
        $image_size = getimagesize($file_path);
        $this->data['headerOP'] .= 'Image sizes: ' . (isset($image_size[0]) ? $image_size[0] : '0') . ' x ' . (isset($image_size[1]) ? $image_size[1] : '0') . '<br>';
      }

      // If Text info
      if ($is_text) {
        $is_utf8 = $this->cfmh->is_utf8($content);
        if (function_exists('iconv')) {
          if (!$is_utf8) {
            $content = iconv($this->cfmh->iconvInputEnc(), 'UTF-8//IGNORE', $content);
          }
        }
        $this->data['headerOP'] .= 'Charset: ' . ($is_utf8 ? 'utf-8' : '8 bit') . '<br>';
      }

      // Add actions to output
      $this->data['headerOP'] .= '<p>
        <strong><a href="' . get_site_url('admin/files/download/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;file=' . urlencode($file)) . '"><i class="fas fa-download"></i> Download</a></strong> &nbsp; <strong><a href="' . $this->cfmh->enc($file_url) . '" target="_blank"><i class="fas fa-external-link-square-alt"></i> Open</a></strong> &nbsp;';

      // Add ZIP actions to output
      if (($is_zip || $is_gzip) && $filenames !== false) {
        $zip_name = pathinfo($file_path, PATHINFO_FILENAME);
        $this->data['headerOP'] .= ' <strong><a href="' . get_site_url('admin/files/unpack/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;archive=' . urlencode($file)) . '"><i class="fas fa-box-open"></i> Unpack</a></strong> &nbsp; <strong><a href="' . get_site_url('admin/files/unpack/?p=' . urlencode($this->cfmh->currentPath()) . '&amp;archive=' . urlencode($file) . '&amp;tofolder=1') . '" title="Unpack to ' . $this->cfmh->enc($zip_name) . '"><i class="fas fa-box-open"></i> Unpack to Folder</a></strong> &nbsp;';
      }

      // Close actions for output
      $this->data['headerOP'] .= '</p>';
    }
    // Check for online viewer
    if ($is_onlineViewer) {
      // Output online viewer
      if ($online_viewer == 'google') {
        $this->data['contentOP'] .= '<iframe src="https://docs.google.com/viewer?embedded=true&hl=en&url=' . $this->cfmh->enc($file_url) . '" frameborder="no" style="width:100%;min-height:460px;height:100%;"></iframe>';
      } else if ($online_viewer == 'microsoft') {
        $this->data['contentOP'] .= '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' . $this->cfmh->enc($file_url) . '" frameborder="no" style="width:100%;min-height:460px;height:100%;"></iframe>';
      }
    } elseif ($is_zip) { // Check for ZIP content
      // ZIP content
      if ($filenames !== false) {
        $this->data['contentOP'] .= '<code class="maxheight">';
        foreach ($filenames as $fn) {
          if ($fn['folder']) {
            $this->data['contentOP'] .= '<strong>' . $this->cfmh->enc($fn['name']) . '</strong><br>';
          } else {
            $this->data['contentOP'] .= $this->cfmh->enc($fn['name']) . ' (' . $this->cfmh->get_file_size($fn['filesize']) . ')<br>';
          }
        }
        $this->data['contentOP'] .= '</code>';
      } else {
        $this->data['contentOP'] .= '<p>Error while fetching archive info</p>';
      }
    } elseif ($is_image) { // Check for image content
      // Image content
      if (in_array($ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg', 'webp', 'avif'))) {
        $this->data['contentOP'] .= '<figure><img src="' . $this->cfmh->enc($file_url) . '" alt="" class="preview-img"></figure>';
      }
    } elseif ($is_audio) { // Check for audio content
      // Audio content
      $this->data['contentOP'] .= '<p><audio src="' . $this->cfmh->enc($file_url) . '" controls preload="metadata"></audio></p>';
    } elseif ($is_video) { // Check for video content
      // Video content
      $this->data['contentOP'] .= '<div class="preview-video"><video src="' . $this->cfmh->enc($file_url) . '" width="640" height="360" controls preload="metadata"></video></div>';
    } elseif ($is_text) { // Check if text content
      // highlight
      $hljs_classes = array(
        'shtml' => 'xml',
        'htaccess' => 'apache',
        'phtml' => 'php',
        'lock' => 'json',
        'svg' => 'xml',
      );
      $hljs_class = isset($hljs_classes[$ext]) ? 'lang-' . $hljs_classes[$ext] : 'lang-' . $ext;
      if (empty($ext) || in_array(strtolower($file), $this->cfmh->get_text_names()) || preg_match('#\.min\.(css|js)$#i', $file)) {
        $hljs_class = 'nohighlight';
      }
      $this->data['contentOP'] .= '<pre class="with-hljs"><code class="' . $hljs_class . '">' . $this->cfmh->enc($content) . '</code></pre>';
    }

    // Load view
    $this->load->view('files/view', $this->data, 'admin');
    exit;
  }

  /**
   * get list of destination folders
   *
   * @param bool $relative Set if options should be relative paths
   */
  private function getDestinationFolders($relative = false)
  {
    // Get list of folders
    $this->data['folder_options'] = '<option value="">Root Folder</option>';
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($this->cfmh->rootPath(), RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
    );
    $currentPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->data['path']);
    $rootPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->cfmh->rootPath());
    $checkPath = (strpos($currentPath, $rootPath) === false) ? $rootPath . _DS . $currentPath : $currentPath;
    foreach ($iterator as $file) {
      if ($file->isDir()) {
        $selected = ($checkPath === $file->getRealpath()) ? ' selected' : '';
        $valueOP = ($relative) ? $this->cfmh->get_relative_path_folder($file->getRealpath()) : $file->getRealpath();
        $this->data['folder_options'] .= '<option value="' . $valueOP . '"' . $selected . '>' . str_replace($rootPath, '', $file->getRealpath()) . '</option>';
      }
    }
  }

  /**
   * Copy Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function copy(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('add_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to copy files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    //Check if page posted and process form if it is
    if (
      $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && ($_POST['action'] == "copy" || $_POST['action'] == "move")
    ) {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // from
      $path = $this->cfmh->clean_path($_POST['path']);
      $copy = $this->cfmh->clean_path($path . _DS . $_POST['item']);
      // empty path
      if ($copy == '') {
        flashMsg('admin_filemanager', '<strong>Error</strong> Source path not defined', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }
      // abs path from
      $from = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->cfmh->rootPath() . _DS . $copy);
      // abs path to
      $dest = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->cfmh->rootPath());
      if (!empty($_POST['destination'])) {
        $dest = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->cfmh->clean_path($_POST['destination']));
      }
      $dest .= _DS . $this->cfmh->clean_path($_POST['item']);
      // move?
      $move = isset($_POST['action']) && $_POST['action'] == "move";
      // copy/move/duplicate
      if ($from != $dest) {
        if ($move) { // Move and to != from so just perform move
          $rename = $this->cfmh->rename($from, $dest);
          if ($rename) {
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Moved from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($this->cfmh->get_relative_path($copy)), $this->cfmh->enc($this->cfmh->get_relative_path($dest))), 'success');
            redirectTo('admin/files/?p=' . urlencode($this->cfmh->get_relative_path_folder($dest)));
            exit;
          } elseif ($rename === null) {
            flashMsg('admin_filemanager', '<strong>Error</strong> File or folder with this path already exists. Please try again.', 'warning');
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while moving from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($this->cfmh->get_relative_path($copy)), $this->cfmh->enc($this->cfmh->get_relative_path($dest))), 'danger');
          }
        } else { // Not move and to != from so copy with original name
          if ($this->cfmh->rcopy($from, $dest)) {
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Copied from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($this->cfmh->get_relative_path($copy)), $this->cfmh->enc($this->cfmh->get_relative_path($dest))), 'success');
            redirectTo('admin/files/?p=' . urlencode($this->cfmh->get_relative_path_folder($dest)));
            exit;
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while copying from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($this->cfmh->get_relative_path($copy)), $this->cfmh->enc($this->cfmh->get_relative_path($dest))), 'danger');
          }
        }
      } else {
        if (!$move) { //Not move and to = from so duplicate
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
            flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Copied from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($this->cfmh->get_relative_path($copy)), $this->cfmh->enc($this->cfmh->get_relative_path($fn_duplicate))), 'success');
            redirectTo('admin/files/?p=' . urlencode($this->cfmh->get_relative_path_folder($fn_duplicate)));
            exit;
          } else {
            flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while copying from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($this->cfmh->get_relative_path($copy)), $this->cfmh->enc($this->cfmh->get_relative_path($fn_duplicate))), 'danger');
          }
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> Paths must not match. Please try again.', 'warning');
        }
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->get_relative_path_folder($from)));
    } else { // Page wasn't posted.

      // get source path
      $this->data['path'] = '';
      if ($this->cfmh->currentPath() != '') {
        $this->data['path'] = $this->cfmh->currentPath();
      }

      // Check for item
      if (!empty($this->request->get['copy'])) {
        $this->data['item'] = $this->request->get['copy'];
      } else {
        // No item found. Output error
        echo '<div class="modal csc-modal--flowable" style="display: block;"><div class="csc-modal__content"><p><em>No file / folder was selected.<br>Please try again</em></p><p><a href="#" class="csc-btn--flat" rel="modal:close"><span>Cancel</span></a></p></div></div>';
        exit;
      }

      // Get list of folders
      $this->getDestinationFolders();

      // Load view
      $this->load->view('files/copy', $this->data, 'admin');
      exit;
    }
  }

  /**
   * Bulk Copy Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function bulkcopy(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('add_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to bulk copy. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // get current path
    $this->data['path'] = $this->cfmh->rootPath();
    if ($this->cfmh->currentPath() != '') {
      $this->data['path'] .= '/' . $this->cfmh->currentPath();
    }

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && ($_POST['action'] == "copy" || $_POST['action'] == "move")) {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // from
      $path = $this->cfmh->clean_path($_POST['path']);
      // to
      $copy_to_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->cfmh->rootPath());
      if (!empty($_POST['destination'])) {
        $copy_to_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->cfmh->clean_path($_POST['destination']));
      }
      // empty path
      if ($path == $copy_to_path) {
        flashMsg('admin_filemanager', '<strong>Error</strong> Paths must not match. Please try again.', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }
      if (!is_dir($copy_to_path)) {
        if (!$this->cfmh->mkdir($copy_to_path, true)) {
          flashMsg('admin_filemanager', '<strong>Error</strong> Unable to create destination folder. Please try again.', 'danger');
          redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
        }
      }
      // move?
      $move = isset($_POST['action']) && $_POST['action'] == "move";
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
        if ($errors == 0) {
          $msg = $move ? 'Selected files and folders moved' : 'Selected files and folders copied';
          flashMsg('admin_filemanager', "<strong>Success</strong> {$msg}", 'success');
          redirectTo('admin/files/?p=' . urlencode($this->cfmh->get_relative_path_folder($copy_to_path)));
          exit;
        } else {
          $msg = $move ? 'Error while moving items' : 'Error while copying items';
          flashMsg('admin_filemanager', "<strong>Error</strong> {$msg}", 'danger');
        }
      } else {
        flashMsg('admin_filemanager', "<strong>Error</strong> Nothing selected", 'warning');
      }
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    } else { // Page wasn't posted.

      // Get list of files
      $this->data['copy_files'] = isset($_POST['file']) ? $_POST['file'] : null;
      $this->data['file_inputs'] = '';
      if (!is_array($this->data['copy_files']) || empty($this->data['copy_files'])) {
        // Redirect user with error
        flashMsg('admin_filemanager', '<strong>Error</strong> Nothing selected. Please try again.', 'warning');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      }
      foreach ($this->data['copy_files'] as $cf) {
        $this->data['file_inputs'] .= '<input type="hidden" name="file[]" value="' . $this->cfmh->enc($cf) . '">' . PHP_EOL;
      }

      // Set paths
      $this->data['action_link'] = get_site_url('admin/files/bulk-copy/?p=' . $this->cfmh->enc($this->cfmh->currentPath()));
      $this->data['current_path'] = $this->cfmh->currentPath();
      $this->data['current_path_enc'] = $this->cfmh->enc($this->cfmh->currentPath());

      // Set Breadcrumbs
      $this->data['breadcrumbs'][] = array(
        'text' => 'Bulk Copy',
        'href' => get_site_url("admin/files/bulk-copy/?p={$this->cfmh->currentPath()}")
      );

      // Get list of folders
      $this->getDestinationFolders();

      // Load view
      $this->load->view('files/bulkcopy', $this->data, 'admin');
      exit;
    }
  }

  /**
   * Rename file / folder
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function rename(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('rename_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to rename files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Check that the file / folder is set
    if (isset($this->request->get['current']) && isset($this->request->get['new'])) {
      // old name
      $old = str_replace('/', '', $this->cfmh->clean_path($this->request->get['current']));
      // new name
      $new = str_replace('/', '', $this->cfmh->clean_path(strip_tags($this->request->get['new'])));
      // path
      $path = $this->cfmh->rootPath();
      if ($this->cfmh->currentPath() != '') {
        $path .= '/' . $this->cfmh->currentPath();
      }
      // rename
      if ($this->cfmh->is_valid_filename($new) && $old != '' && $new != '') {
        if ($this->cfmh->rename($path . '/' . $old, $path . '/' . $new)) {
          flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf('Renamed from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($old), $this->cfmh->enc($new)), 'success');
        } else {
          flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf('Error while renaming from "<em>%s</em>" to "<em>%s</em>"', $this->cfmh->enc($old), $this->cfmh->enc($new)), 'danger');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Invalid characters in file name. Please try again.', 'warning');
      }
    } else {
      flashMsg('admin_filemanager', '<strong>Error</strong> Invalid file or folder name', 'warning');
    }
    redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    exit;
  }

  /**
   * Change Perms (not for Windows) Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function chmod(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('chmod_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to change file permissions. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Check isn't windows
    if ($this->cfmh->isWindows()) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, changing file permissions is not available on the Windows system.', 'danger');
      redirectTo('admin/files/?p=' . $this->cfmh->currentPath());
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // get current path
    $this->data['path'] = $this->cfmh->rootPath();
    if ($this->cfmh->currentPath() != '') {
      $this->data['path'] .= '/' . $this->cfmh->currentPath();
    }

    //Check if page posted and process form if it is - UPLOAD FROM URL
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "change") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $file = $_POST['item'];
      $file = $this->cfmh->clean_path($file);
      $file = str_replace('/', '', $file);
      if ($file == '' || (!is_file($this->data['path'] . '/' . $file) && !is_dir($this->data['path'] . '/' . $file))) {
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

      if (@chmod($this->data['path'] . '/' . $file, $mode)) {
        flashMsg('admin_filemanager', '<strong>Success</strong> Permissions changed.', 'success');
        redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      } else {
        flashMsg('files_chmod', '<strong>Error</strong> Permissions not changed. Please try again', 'warning');
      }
    }

    // Check file found
    $file = str_replace('/', '', $this->cfmh->clean_path($this->request->get['item']));
    if ($file == '' || (!is_file($this->data['path'] . '/' . $file) && !is_dir($this->data['path'] . '/' . $file))) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Item not found. Please try again.', 'warning');
      redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
      exit;
    }

    // Item type
    $this->data['item_type'] = 'Item';
    if (is_file($this->data['path'] . '/' . $file)) {
      $this->data['item_type'] = 'File';
    } else if (is_dir($this->data['path'] . '/' . $file)) {
      $this->data['item_type'] = 'Folder';
    }

    // Set paths
    $this->data['current_path'] = $this->cfmh->currentPath();
    $this->data['current_path_enc'] = $this->cfmh->enc($this->cfmh->currentPath());
    $this->data['file_enc'] = $this->cfmh->enc($file);
    $this->data['file_path'] = $this->data['current_path'] . '/' . $file;
    $this->data['file_mode'] = fileperms($this->data['path'] . '/' . $file);

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Change ' . $this->data['item_type'] . ' Permissions',
      'href' => get_site_url("admin/files/chmod/?p={$this->cfmh->currentPath()}?item={$this->request->get['item']}")
    );

    // Load view
    $this->load->view('files/chmod', $this->data, 'admin');
    exit;
  }

  /**
   * Download file
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function download(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('view_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to download files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Download
    $dl = $this->request->get['file'];
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

  /**
   * Unpack archive
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function unpack(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('add_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to unpack archives. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Get file
    $unzip = $this->request->get['archive'];
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
      if (isset($this->request->get['tofolder'])) {
        $tofolder = pathinfo($zip_path, PATHINFO_FILENAME);
        if ($this->cfmh->mkdir($path . '/' . $tofolder, true)) {
          $path .= '/' . $tofolder;
        }
      }

      if ($ext == "zip") {
        $zipper = new Cornerstone\CS_Zipper();
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
    exit;
  }

  /**
   * Delete file / folder
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function delete(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('delete_files')) {
      // Redirect user with error
      flashMsg('admin_filemanager', '<strong>Error</strong> Sorry, you are not allowed to delete files. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/files/');
      exit;
    }

    // Set params to request
    $this->request->set_params($params);

    // Check that the file / folder is set
    if (isset($this->request->get['f'])) {
      $del = str_replace('/', '', $this->cfmh->clean_path($this->request->get['f']));
      if ($del != '' && $del != '..' && $del != '.') {
        $path = $this->cfmh->rootPath();
        if ($this->cfmh->currentPath() != '') {
          $path .= '/' . $this->cfmh->currentPath();
        }
        $is_dir = is_dir($path . '/' . $del);
        if ($this->cfmh->rdelete($path . '/' . $del)) {
          $msg = $is_dir ? 'Folder "<em>%s</em>"' : 'File "<em>%s</em>"';
          flashMsg('admin_filemanager', '<strong>Success</strong> ' . sprintf($msg, $this->cfmh->enc($del)) . ' was deleted', 'success');
        } else {
          $msg = $is_dir ? 'Folder "<em>%s</em>" ' : 'File "<em>%s</em>"';
          flashMsg('admin_filemanager', '<strong>Error</strong> ' . sprintf($msg, $this->cfmh->enc($del)) . ' was unable to be deleted. Please try again.', 'danger');
        }
      } else {
        flashMsg('admin_filemanager', '<strong>Error</strong> Invalid file or folder name', 'warning');
      }
    } else {
      flashMsg('admin_filemanager', '<strong>Error</strong> Invalid file or folder name', 'warning');
    }
    redirectTo('admin/files/?p=' . urlencode($this->cfmh->currentPath()));
    exit;
  }
}
