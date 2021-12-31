<?php

/**
 * The Admin File Manager File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "File Manager | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " file manager.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'chosen',
  'jquerymodal',
  'sweetalert',
  'validate'
);
$pageID = 'admin-file-manager';
$pageHeadExtras = '<!-- Highlight.js ~ https://highlightjs.org/ -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/styles/vs.min.css">';
$pageFooterExtras = '<!-- Highlight.js ~ https://highlightjs.org/ -->
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/highlight.min.js"></script>
<script>hljs.highlightAll(); var isHighlightingEnabled = true;</script>';
$currentNav = 'filemanager';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<div class="csc-row cs-mt-3">
  <section class="csc-col csc-col12">
    <nav class="csc-breadcrumbs">
      <?php
      // Check for and output breadcrumbs
      if (!empty($data->breadcrumbs)) {
        // Output breadcrumbs
        echo outputBreadcrumbs((object) $data->breadcrumbs);
      } ?>
    </nav>
  </section>
</div>
<div class="csc-row">
  <section class="csc-col csc-col12 csc-col--md6 cs-mb-md-3">
    <h1 class="cs-mb-0">File Manager</h1>
  </section>
  <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
    <p class="cs-mt-0 cs-mt-md-3">
      <a title="Jump to Folder" class="csc-btn--flat-small" href="#jumpToFolder" rel="modal:open"><i class="fas fa-share csc-bi-left" aria-hidden="true"></i> Jump to Folder</a>
      <?php if ($role->canDo('add_files')) { ?>
        <a title="Upload" class="csc-btn--flat-small" href="<?= get_site_url('admin/files/upload/?p=' . $data->input_path); ?>"><i class="fas fa-cloud-upload-alt csc-bi-left" aria-hidden="true"></i> Upload</a>
        <a title="New Item" class="csc-btn--flat-small" href="#createNewItem" rel="modal:open"><i class="fas fa-plus-square csc-bi-left" aria-hidden="true"></i> New Item</a>
      <?php } ?>
    </p>
  </section>
</div>
<?php flashMsg('admin_filemanager'); ?>
<section class="csc-container">
  <form id="file-manager-form" class="csc-form" action="" method="post">
    <input type="hidden" name="p" value="<?= $data->input_path; ?>">
    <input type="hidden" name="group" value="1">
    <div class="csc-data-table">
      <section class="csc-data-table__table">
        <table id="file-manager-table">
          <thead class="csc-table-header">
            <tr>
              <th style="width:3%" class="custom-checkbox-header">
                <label>
                  <input type="checkbox" class="custom-control-input" id="js-select-all-items" onclick="checkbox_toggle()">
                  <span></span>
                </label>
              </th>
              <th>Name</th>
              <th>Size</th>
              <th>Modified</th>
              <?php if ($data->opPermissionColumns) : ?>
                <th>Perms</th>
                <th>Owner</th>
              <?php endif; ?>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="csc-table-body csc-table-body--zebra">
            <?php
            // link to parent folder
            if ($data->parent !== false) { ?>
              <tr>
                <td>
                </td>
                <td class="cs-border-0"><a href="?p=<?php echo urlencode($data->parent) ?>"><i class="fa fa-chevron-circle-left go-back"></i> ..</a></td>
                <td class="cs-border-0"></td>
                <td class="cs-border-0"></td>
                <td class="cs-border-0"></td>
                <?php if ($data->opPermissionColumns) { ?>
                  <td class="cs-border-0"></td>
                  <td class="cs-border-0"></td>
                <?php } ?>
              </tr>
            <?php }
            // Output Folders
            echo $data->opFolders;
            // Output files
            echo $data->opFiles;

            // Check if folders/files are present
            if (empty($data->folders) && empty($data->files)) { ?>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td colspan="<?php echo ($data->opPermissionColumns) ? '6' : '4' ?>"><em><?= 'Folder is empty'; ?></em></td>
            </tr>
          </tfoot>
        <?php } else { ?>
          <tfoot>
            <tr>
              <td class="gray" colspan="<?php echo ($data->opPermissionColumns) ? '7' : '5' ?>">
                <?= $data->opFullSize . $data->opFileCount . $data->opFolderCount . $data->opPartitionSize; ?>
              </td>
            </tr>
          </tfoot>
        <?php } ?>
        </table>
      </section>
    </div>

    <section class="cs-p-3">
      <ul class="list-inline footer-action">
        <li class="list-inline-item"> <a href="#/select-all" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info" onclick="select_all();return false;"><i class="fas fa-check-square csc-bi-left"></i> Select all </a></li>
        <li class="list-inline-item"><a href="#/unselect-all" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info" onclick="unselect_all();return false;"><i class="fas fa-window-close csc-bi-left"></i> Unselect all </a></li>
        <li class="list-inline-item"><a href="#/invert-all" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info" onclick="invert_all();return false;"><i class="fas fa-th-list csc-bi-left"></i> Invert Selection </a></li>
        <li class="list-inline-item">
          <input type="submit" class="hidden" name="action" id="a-delete" value="delete" onclick="return confirm('Delete selected files and folders?')">
          <a href="javascript:document.getElementById('a-delete').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-trash-alt csc-bi-left"></i> Delete </a>
        </li>
        <li class="list-inline-item">
          <input type="submit" class="hidden" name="action" id="a-zip" value="zip" onclick="return confirm('Create archive?')">
          <a href="javascript:document.getElementById('a-zip').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-file-archive csc-bi-left"></i> Zip </a>
        </li>
        <li class="list-inline-item">
          <input type="submit" class="hidden" name="action" id="a-tar" value="tar" onclick="return confirm('Create archive?')">
          <a href="javascript:document.getElementById('a-tar').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-file-archive csc-bi-left"></i> Tar </a>
        </li>
        <li class="list-inline-item">
          <input type="submit" class="hidden" name="action" id="a-copy" value="bulk-copy">
          <a href="javascript:document.getElementById('a-copy').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-copy csc-bi-left"></i> Copy </a>
        </li>
      </ul>
    </section>

  </form>
</section>

<?php if ($role->canDo('add_files')) { ?>
  <!-- Modal for creating a new item -->
  <div id="createNewItem" class="modal csc-modal--flowable">
    <form action="<?php echo get_site_url('admin/files/create/?p=' . $data->input_path); ?>" method="POST" id="new-item-form" class="csc-form">
      <div class="csc-modal__header">
        <i class="fas fa-plus-square"></i> Create New Item
      </div>
      <div class="csc-modal__content">
        <fieldset>
          <p class="cs-body2">Item Type</p>
          <div class="csc-input-field" id="new-item-types">
            <p>
              <label>
                <input name="item_type" id="type_file" value="file" type="radio" />
                <span>File</span>
              </label>
            </p>
            <p>
              <label>
                <input name="item_type" id="type_folder" value="folder" type="radio" checked />
                <span>Folder</span>
              </label>
            </p>
          </div>
          <div class="csc-input-field">
            <input type="text" name="item_name" id="item_name" tabindex="2" autocapitalize="off" data-lpignore="true" required>
            <label for="item_name">Item Name*</label>
          </div>
        </fieldset>
      </div>
      <div class="csc-modal__actions">
        <a href="#" class="csc-btn csc-btn--flat" rel="modal:close"><span>Cancel</span></a>
        <button type="submit" name="action" value="create" class="csc-btn csc-btn--success">Create</button>
      </div>
    </form>
  </div>

  <!-- Validation -->
  <script>
    // Init validation on document ready
    $(document).ready(function() {
      let validator = $("#new-item-form").validate({
        rules: {
          item_type: {
            required: true
          },
          item_name: {
            required: true,
            minlength: 3
          }
        },
        messages: {
          item_type: {
            required: "Please select an item type"
          },
          item_name: {
            required: "Please enter an item name",
            minlength: "Please enter at least 3 characters"
          }
        }
      });
      <?php
      // Output errors if they exist
      if (!empty($data->err)) {
        // Call the formatting function
        echo 'validator' . showValidationErrors($data->err);
      } ?>
    });
  </script>
<?php } ?>

<!-- Modal for jumping to another folder -->
<div id="jumpToFolder" class="modal csc-modal--flowable">
  <form action="<?php echo get_site_url('admin/files/'); ?>" method="GET" id="jump-to-folder-form" class="csc-form">
    <div class="csc-modal__header">
      <i class="fas fa-share"></i> Jump to Folder
    </div>
    <div class="csc-modal__content">
      <fieldset>
        <div class="csc-input-field">
          <select name="p" id="destination" data-placeholder="Select a folder to jump to" tabindex="1" required>
            <?php echo $data->folder_options; ?>
          </select>
          <label>Destination Folder</label>
        </div>
      </fieldset>
    </div>
    <div class="csc-modal__actions">
      <a href="#" class="csc-btn csc-btn--flat" rel="modal:close"><span>Cancel</span></a>
      <button type="submit" class="csc-btn csc-btn--success">Jump <i class="fas fa-share csc-bi-right"></i></button>
    </div>
  </form>
</div>

<script>
  function template(html, options) {
    var re = /<\%([^\%>]+)?\%>/g,
      reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g,
      code = 'var r=[];\n',
      cursor = 0,
      match;
    var add = function(line, js) {
      js ? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') : (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
      return add
    }
    while (match = re.exec(html)) {
      add(html.slice(cursor, match.index))(match[1], !0);
      cursor = match.index + match[0].length
    }
    add(html.substr(cursor, html.length - cursor));
    code += 'return r.join("");';
    return new Function(code.replace(/[\r\t\n]/g, '')).apply(options)
  }

  // Delete Function
  const deleteButtons = document.getElementsByClassName('delete-this');
  if (deleteButtons) {
    Array.from(deleteButtons).forEach(deleteBtn => {
      deleteBtn.addEventListener("click", function(event) {
        event.preventDefault();
        swal({
            title: "Delete",
            text: `Delete ${deleteBtn.dataset.t} "${deleteBtn.dataset.name}"?`,
            icon: "warning",
            buttons: ["No", "Yes"]
          })
          .then((deleteF) => {
            if (deleteF) {
              // Redirect to delete page
              window.location.href = `<?= get_site_url('admin/files/delete/?p=' . $data->input_path . '&f=') ?>${deleteBtn.dataset.f}`;
            }
          });
      });
    });
  }

  // Rename Function
  const renameButtons = document.getElementsByClassName('rename-this');
  if (renameButtons) {
    Array.from(renameButtons).forEach(renameBtn => {
      renameBtn.addEventListener("click", function(event) {
        event.preventDefault();
        swal({
            text: `Rename ${renameBtn.dataset.name}`,
            content: {
              element: "input",
              attributes: {
                placeholder: "Enter the new name",
                type: "text",
                value: `${renameBtn.dataset.name}`
              }
            },
            buttons: ["Cancel", "Rename"]
          })
          .then((newName) => {
            if (!newName) throw null;
            // Redirect to rename page
            window.location.href = `<?= get_site_url('admin/files/rename/?p=' . $data->input_path . '&current=') ?>${renameBtn.dataset.name}&new=${newName}`;
          });
      });
    });
  }

  function change_checkboxes(e, t) {
    for (var n = e.length - 1; n >= 0; n--) e[n].checked = "boolean" == typeof t ? t : !e[n].checked
  }

  function get_checkboxes() {
    for (var e = document.getElementsByName("file[]"), t = [], n = e.length - 1; n >= 0; n--)(e[n].type = "checkbox") && t.push(e[n]);
    return t
  }

  function select_all() {
    change_checkboxes(get_checkboxes(), !0)
  }

  function unselect_all() {
    change_checkboxes(get_checkboxes(), !1)
  }

  function invert_all() {
    change_checkboxes(get_checkboxes())
  }

  function checkbox_toggle() {
    var e = get_checkboxes();
    e.push(this), change_checkboxes(e)
  }

  // Document Ready set
  $(document).ready(function() {
    $("#destination").chosen({
      disable_search_threshold: 5,
      no_results_text: "Sorry, nothing was found matching",
      width: "100%",
      search_contains: "true"
    });
  });
</script>
<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>