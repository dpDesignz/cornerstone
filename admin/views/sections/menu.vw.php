<?php

/**
 * Sections Menu File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = $data->page_title . " | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " " . $data->page_title;
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = $data->action_url;
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate',
  'chosen'
);
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $("#available_content").chosen({
    width: "100%",
    disable_search: true,
    allow_single_deselect: true
  });
  // Delegate tippy
  const delegateInstances = tippy.delegate("#menu-items", {
    target: "[data-tippy-content]",
    allowHTML: true,
    delay: 200,
  });
  </script>';
$currentNav = 'cms';
$currentSubNav = $currentNav . '/sections';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<div class="csc-wrapper csc-row cs-my-2">
  <div class="csc-col csc-col12">
    <h1 class="cs-h2 cs-my-2"><?php echo $data->page_title; ?></h1>
  </div>
</div>
<div class="csc-wrapper csc-row csc-container">
  <section class="csc-col csc-col12 csc-col--md8">
    <?php flashMsg('sections_menu'); ?>
    <form action="<?php echo $data->action_url; ?>" method="POST" id="menu-form" class="csc-form cs-p-3 me-form">
      <input type="hidden" name="id" value="<?php echo $data->id; ?>">
      <fieldset>
        <legend>Available Content</legend>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <select id="available_content" data-placeholder="Select a content item" tabindex="1">
              <?php echo $data->available_content;
              ?>
            </select>
            <label>Content</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col10 csc-input-field">
            <input type="text" name="custom_link" id="custom_link" autocapitalize="off">
            <label for="custom_link">Custom URL</label>
          </div>
          <div class="csc-col csc-col2 csc-col--align-middle">
            <button type="button" id="add-custom-url" class="csc-btn csc-btn--outlined csc-btn--small"><i class="fas fa-plus csc-bi-left"></i> Add</button>
          </div>
        </div>
      </fieldset>
      <fieldset id="menu-items">
        <legend>Current Content</legend>
        <?php echo $data->menu_items; ?>
      </fieldset>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?php echo get_site_url('admin/sections/'); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
        <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3"><button type="submit" name="action" tabindex="4" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button></div>
      </div>
      <div class="csc-row csc-row--no-gap cs-hide-md-up">
        <div class="csc-col csc-col12">
          <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
        </div>
      </div>
    </form>
  </section>
  <section class="csc-col csc-col12 csc-col--md4 cs-hide-md-down csc-form-details">
    <h4 class="cs-h4">Form Details</h4>
    <p class="cs-body1"><?php echo $data->instructions; ?></p>
    <h5 class="cs-h5">Form Fields</h5>
    <p class="cs-body2"><strong>Content: </strong> The content available to add to the menu.</p>
    <p class="cs-body2"><strong>Custom URL: </strong> A custom internal URL to add to the menu. Don't include the site address or leading slash.</p>
    <p class="cs-body2"><strong>Custom Title: </strong> The title displayed for the link in this menu. This field is only require for custom urls</p>
    <p class="cs-body2"><strong>Sort: </strong> The sort order of the menu items in the menu.</p>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</div>

<script>
  // Add row
  $('#available_content').on('change', function(evt, params) {
    // Check if set
    if (params !== undefined && params.selected !== undefined) {
      // Get users details
      const itemName = evt.target.selectedOptions[0].innerText;
      const itemID = evt.target.selectedOptions[0].value;

      // Get random row ID number
      const rowID = Math.floor(Math.random() * Math.floor(500));

      // Add item row HTML
      const HTML = `<div id="item_${rowID}_row" class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md5 csc-col--align-middle csc-input-field">
          <input type="hidden" name="item[${rowID}][content_id]" id="item_${rowID}_content_id" value="${itemID}">
          <p id="item_${rowID}_name" class="cs-body1">${itemName}</p>
        </div>
        <div class="csc-col csc-col12 csc-col--md4 csc-col--align-middle csc-input-field">
          <input type="text" name="item[${rowID}][title]" id="item_${rowID}_title" autocapitalize="off">
          <label for="item_${rowID}_title">Custom Title</label>
        </div>
        <div class="csc-col csc-col12 csc-col--md2 csc-input-field">
          <input type="number" name="item[${rowID}][sort]" id="item_${rowID}_sort" value="0">
          <label for="item_${rowID}_sort" data-tippy-content="Change the sort order of the menu item">Sort <i class="far fa-question-circle"></i></label>
        </div>
        <div class="csc-col csc-col12 csc-col--md1 csc-col--align-center csc-col--align-middle">
          <i class="fas fa-trash-alt csc-text-red delete-item" data-row="${rowID}" data-tippy-content="Delete menu item"></i>
        </div>
      </div>`;

      // Append new line
      document.querySelector('#menu-items').insertAdjacentHTML('beforeend', HTML);

      // Add event listeners
      const rowItem = document.querySelector(`#item_${rowID}_row`);
      const sortInput = rowItem.querySelector(`#item_${rowID}_sort`);
      addLabelListeners(sortInput);
      const trashItem = rowItem.querySelector(`.delete-item`);
      trashItem.addEventListener('click', () => {
        // Get row ID
        const rowID = trashItem.dataset.row;
        removeRow(rowID);
      });

      // Remove option from the select
      evt.target.remove(evt.target.selectedIndex);

      // check for remaining options
      if (evt.target.options.length < 2) {
        let select = document.querySelector('#available_content');
        select.options[select.options.length] = new Option('No more items available', 'none', false, false);
        select.options[select.options.length - 1].disabled = true;
      }

      // Update the chosen
      $('#available_content').trigger('chosen:updated');
    }
  });

  // Add custom URL row
  function addCustomURLRow() {

    // Get the custom URL
    const customLink = document.querySelector('#custom_link');

    // Check the custom URL isn't empty
    if (customLink && customLink.value !== '') {

      // Get random row ID number
      const rowID = Math.floor(Math.random() * Math.floor(500));

      // Add item row HTML
      const HTML = `<div id="item_${rowID}_row" class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md5 csc-col--align-middle csc-input-field">
          <input type="text" name="item[${rowID}][custom]" id="item_${rowID}_custom" value="${customLink.value}" autocapitalize="off">
          <label for="item_${rowID}_custom">Custom URL*</label>
        </div>
        <div class="csc-col csc-col12 csc-col--md4 csc-col--align-middle csc-input-field">
          <input type="text" name="item[${rowID}][title]" id="item_${rowID}_title" value="${customLink.value}" autocapitalize="off" required>
          <label for="item_${rowID}_title">Custom Title*</label>
        </div>
        <div class="csc-col csc-col12 csc-col--md2 csc-input-field">
          <input type="number" name="item[${rowID}][sort]" id="item_${rowID}_sort" value="0">
          <label for="item_${rowID}_sort" data-tippy-content="Change the sort order of the menu item">Sort <i class="far fa-question-circle"></i></label>
        </div>
        <div class="csc-col csc-col12 csc-col--md1 csc-col--align-center csc-col--align-middle">
          <i class="fas fa-trash-alt csc-text-red delete-item" data-row="${rowID}" data-tippy-content="Delete menu item"></i>
        </div>
      </div>`;

      // Append new line
      document.querySelector('#menu-items').insertAdjacentHTML('beforeend', HTML);

      // Clear the url value
      customLink.value = "";

      // Add event listeners
      const rowItem = document.querySelector(`#item_${rowID}_row`);
      const customInput = rowItem.querySelector(`#item_${rowID}_custom`);
      addLabelListeners(customInput);
      const sortInput = rowItem.querySelector(`#item_${rowID}_sort`);
      addLabelListeners(sortInput);
      const trashItem = rowItem.querySelector(`.delete-item`);
      trashItem.addEventListener('click', () => {
        // Get row ID
        const rowID = trashItem.dataset.row;
        removeRow(rowID);
      });
    } else { // Show error

      // Show success
      Toastify({
        text: 'Please enter a custom URL value',
        duration: 5000,
        gravity: "bottom",
        position: "right",
        className: "toastify-warning"
      }).showToast();

    }
  }

  // Remove row
  function removeRow(rowID) {
    // Get the row element
    const rowElm = document.querySelector(`#item_${rowID}_row`);
    // Get the content ID
    const contentID = rowElm.querySelector(`#item_${rowID}_content_id`);
    // Get the name
    const itemName = rowElm.querySelector(`#item_${rowID}_name`);
    // Remove the row
    rowElm.parentNode.removeChild(rowElm);

    // Check if adding to the available list
    if (itemName && contentID) {
      // Add to the available list
      const select = document.querySelector('#available_content');
      select.options[select.options.length] = new Option(itemName.innerText, contentID, false, false);

      // Check if needing to remove the empty message
      if (select.options[1].value == "none") {
        select.remove(1);
      }

      // Update the chosen
      $('#available_content').trigger('chosen:updated');
    }
  }

  // Add event listeners
  const trash = document.querySelectorAll('.delete-item');
  if (trash) {
    trash.forEach(
      trashItem => trashItem.addEventListener('click', () => {
        // Get row ID
        const rowID = trashItem.dataset.row;
        removeRow(rowID);
      })
    );
  }
  document.querySelector('#add-custom-url').addEventListener('click', addCustomURLRow);
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>