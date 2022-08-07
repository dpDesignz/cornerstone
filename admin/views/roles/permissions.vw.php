<?php

/**
 * User Role Permission Add File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Permission List | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " admin permission list.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = $data->action_url;
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'users';
$currentSubNav = $currentNav . '/roles';

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
<style>
  body.cs-page .csc-wrapper {
    max-width: 768px;
  }

  #permissions__list p {
    margin: 0;
    padding: 16px 8px;
    border-bottom: 2px solid #E4E4E4;
  }

  .hl {
    background: #ffc600;
  }
</style>
<div class="csc-wrapper csc-container">
  <?php flashMsg('roles_permission_list'); ?>
  <section class="cs-px-4 cs-pt-4">
    <form class="csc-data-table__search" action="">
      <label for="csdt-search"><i class="fas fa-search"></i></label>
      <input type="text" name="search" id="csdt-search" tabindex="1" <?php if (!empty($data->search)) echo ' value="' . $data->search . '"'; ?>>
    </form>
  </section>
  <section class="csc-row csc-row--no-gap cs-px-4">
    <p class="csc-col csc-col6"><strong>Name</strong></p>
    <p class="csc-col csc-col6 cs-text-right"><strong>Key</strong></p>
  </section>
  <section id="permissions__list" class="csc-row csc-row--no-gap cs-px-4 cs-pb-4">
    <p class="csc-col csc-col12 cs-body2">Loading permissions...</p>
  </section>
</div>

<script id="permission-json-template" type="application/json">
  <?php echo $data->permissions; ?>
</script>

<script>
  // Load JSON data
  const permissionList = JSON.parse(document.querySelector('#permission-json-template').textContent);

  // Find matches
  function findMatches(wordToMatch, permissionList) {
    return permissionList.filter(permission => {
      const regex = new RegExp(wordToMatch.replace(/\s+/g, "_"), "gi");
      return permission.key.match(regex);
    });
  }

  // Display matches
  function displayMatches() {
    // Get search
    const searchValue = (this.value) ? this.value : '';
    // Search the array
    const matchArray = findMatches(searchValue, permissionList);
    // Init HTML
    let html = '';
    // Check for results
    if (matchArray.length > 0) {
      // Set the HTML
      html = matchArray
        .map(permission => { // Highlight matches
          const regexName = new RegExp(searchValue, 'gi');
          const regexKey = new RegExp(searchValue.replace(/\s+/g, "_"), 'gi');
          const nameOutput = permission.name.replace(
            regexName,
            `<span class="hl">${searchValue}</span>`
          );
          const keyOutput = permission.key.replace(
            regexKey,
            `<span class="hl">${searchValue.replace(/\s+/g, "_")}</span>`
          );
          // Return output
          return `<p class="csc-col csc-col6">${nameOutput}</p>
        <p class="csc-col csc-col6 cs-text-right">${keyOutput}</p>`;
        })
        .join('');
    } else {
      // Set the HTML
      html = `<p class="csc-col csc-col12 cs-body2">Sorry, there are no results for <strong>${searchValue}</strong>.</p>`;
    }
    // Update HTML
    permissions.innerHTML = html;
  }

  // Get search input
  const searchInput = document.querySelector("#csdt-search");

  // Get results element
  const permissions = document.querySelector("#permissions__list");

  // Check if empty results
  if (!permissionList[0]['empty']) {

    // Init live search bar
    searchInput.addEventListener("change", displayMatches);
    searchInput.addEventListener("keyup", displayMatches);

    // Run display matches on page load
    displayMatches();
  } else {
    // Output no results
    permissions.innerHTML = '<p class="csc-col csc-col12 cs-body2">Sorry, there are currently no permissions available to view.</p>';
  }
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>