    </main>
    <!-- End Main ~#~ Start Footer -->
    <?php if(!isset($hideThemeFooter) || !$hideThemeFooter) : ?>
    <footer>
      <p id="footer__copyright">&copy; <?php echo date('Y') . ' ' . SITE_NAME; ?></p>
      <p id="footer__thankyou">Thank you for building with <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework GitHub">Cornerstone</a> v<?php echo CS_VERSION; ?></p>
    </footer>
    <!-- End Footer -->
  </main>
<?php endif; ?>
	<!-- End Footer -->
  <!-- Waves ~ http://fian.my.id/Waves/ -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.js" integrity="sha256-R//ABCk0LbG1/TvQQ4+sbwjzmPxJn9SF5f7FJ2AwJ4o=" crossorigin="anonymous"></script>
  <!-- JavaScript Cookie ~ https://github.com/js-cookie/js-cookie -->
  <script src="//cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
  <!-- jQuery Modal ~ https://jquerymodal.com/ -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.min.js" integrity="sha256-lw0IsO3Ev8CSVJXYsRVk88L9No90X3s1EKf87RGEiJQ=" crossorigin="anonymous"></script>
  <!-- Tooltipster ~ http://iamceege.github.io/tooltipster/ -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/js/jquery.tooltipster.min.js" integrity="sha256-lenj6loHcdfu6tFQPUHN6S2O7G2gv7yX/P9OnRc8lno=" crossorigin="anonymous"></script>
  <?php if(!empty($pageHasForm) && $pageHasForm === TRUE) : ?>
  <!-- Validation ~ https://github.com/jquery-validation/jquery-validation -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js" integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>
  <!-- Inputmask ~ https://github.com/RobinHerbots/Inputmask -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.9/inputmask/inputmask.min.js" integrity="sha256-OeQqhQmzxOCcKP931DUn3SSrXy2hldqf21L920TQ+SM=" crossorigin="anonymous"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.9/inputmask/bindings/inputmask.binding.min.js" integrity="sha256-K+in8BApZHotp9IE5AVgmNdvvI70rXocDD4wV036pJM=" crossorigin="anonymous"></script>
  <!-- Chosen ~ https://harvesthq.github.io/chosen/ -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" integrity="sha256-c4gVE6fn+JRKMRvqjoDp+tlG4laudNYrXI1GncbfAYY=" crossorigin="anonymous"></script>
  <!-- Trumbowyg ~ https://alex-d.github.io/Trumbowyg/ -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.19.1/trumbowyg.min.js" integrity="sha256-1ifXbvyVBZsVmsqwqcoow46rXHi4976VpOWpaMVu2qM=" crossorigin="anonymous"></script>
  <!-- Dropzone ~ https://www.dropzonejs.com/ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js" integrity="sha256-cs4thShDfjkqFGk5s2Lxj35sgSRr4MRcyccmi0WKqCM=" crossorigin="anonymous"></script>
  <?php endif; ?>
  <!-- Perfect Scrollbar ~ https://github.com/mdbootstrap/perfect-scrollbar -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/perfect-scrollbar.min.js" integrity="sha256-pOydVY7re8c1n+fEgg3uoslR/di9NMsOFXJ0Esf2xjQ=" crossorigin="anonymous"></script>
  <!-- lazysizes ~ https://github.com/mdbootstrap/perfect-scrollbar -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.1.2/lazysizes.min.js" integrity="sha256-Md1qLToewPeKjfAHU1zyPwOutccPAm5tahnaw7Osw0A=" crossorigin="anonymous" async=""></script>
  <!-- Cornerstone Scripts -->
  <script src="<?php echo get_site_url('js/cornerstone.js'); ?>"></script>
  <script src="<?php echo get_site_url('admin-files/js/admin.js'); ?>"></script>
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