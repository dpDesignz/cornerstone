<?php if(!isset($hideThemeFooter) || !$hideThemeFooter) : ?>
  <footer>
    <p>&copy; <?php echo date('Y') . ' ' . SITE_NAME; ?> &middot; Built with <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone v<?php echo CS_VERSION; ?></a></p>
  </footer>
<?php endif; ?>
	<!-- End Footer -->
  <!-- Waves ~ http://fian.my.id/Waves/ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.js"></script>
  <!-- jQuery Modal ~ https://jquerymodal.com/ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
  <!-- Tooltipster ~ http://iamceege.github.io/tooltipster/ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/js/jquery.tooltipster.min.js"></script>
  <!-- Validation ~ https://github.com/posabsolute/jQuery-Validation-Engine -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/languages/jquery.validationEngine-en.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/jquery.validationEngine.min.js" type="text/javascript" charset="utf-8"></script>
  <!-- TinyLimiter -->
  <script src="<?php echo get_site_url('js/jquery.tinylimiter.js'); ?>" type="text/javascript" charset="utf-8"></script>
  <!-- Chosen ~ https://harvesthq.github.io/chosen/ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" type="text/javascript" charset="utf-8"></script>
  <!-- Cornerstone Scripts -->
  <script src="<?php echo get_site_url('js/cornerstone.js'); ?>"></script>
  <!-- User Scripts -->
  <script src="<?php echo get_site_url('js/main.js'); ?>"></script>
  <script src="<?php echo get_site_url('js/plugins.js'); ?>"></script>
  <?php
    // Output any page specific extras if it exists
    if(!empty($pageFooterExtras)) {
      echo $pageFooterExtras;
    } ?>
</body>
</html>