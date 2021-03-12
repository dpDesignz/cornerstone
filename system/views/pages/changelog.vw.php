<?php

/**
 * The changelog file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = $data->site_name . " changelog";
$pageMetaDescription = "The changelog for the " . $data->site_name . " website.";
$pageMetaKeywords = "changelog";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url();
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = '';
$pageHeadExtras = '
  <link href="//fonts.googleapis.com/css?family=Source+Code+Pro&display=swap" rel="stylesheet">
  <script src="//cdnjs.cloudflare.com/ajax/libs/showdown/1.9.0/showdown.min.js" integrity="sha256-LSUpTY0kkXGKvcBC9kbmgibmx3NVVgJvAEfTZbs51mU=" crossorigin="anonymous"></script>';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<style>
  main#content {
    padding: 1rem;
    font-family: "Source Sans Pro", sans-serif;
    line-height: 1.45;
    color: #3f3f3f;
  }

  code {
    padding: 2px 4px;
    font-size: 90%;
    color: #3f3f3f;
    background-color: rgba(128, 128, 128, 0.075);
    white-space: nowrap;
    border-radius: 4px;
  }

  code,
  pre {
    font-family: "Source Code Pro", monospace;
    font-size: .9em;
  }

  code {
    white-space: normal;
  }
</style>
<main id="content"></main>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var converter = new showdown.Converter(),
      text = `<?php echo $data->contents; ?>`,
      html = converter.makeHtml(text);
    document.getElementById("content").innerHTML = html;
  });
</script>
<!-- End Main ~#~ Start Footer -->
<footer>
  <p>&copy; <?php echo date('Y') . ' ' . $data->site_name; ?> &middot; Built with <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone v<?php echo CS_VERSION; ?></a></p>
</footer>
</body>

</html>