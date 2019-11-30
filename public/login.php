<?php
// Include the core config file
$fear = 'hidden';require_once( $_SERVER['DOCUMENT_ROOT'] . '/cs-config.php' );
// Set the meta/og information for the page
$pageMetaTitle = "Login | ".SITE_NAME;
$pageMetaDescription = "Cornerstone example login page.";
$pageMetaKeywords = "cornerstone, php, framework, login, sign in";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('login');
$pageMetaType = "website";
include( DIR_CLASS . 'user/userauth.class.php' );
Cornerstone\User\UserAuth::logoutUser(); ?>
<!doctype html>
<html class="no-js" lang="en">
<head>
  <?php include_once(get_sys_path('theme/head.php')); ?>
  <!-- Inject page specific CSS -->
  <!-- <link href="<?php echo get_site_url('css/home.css?v=1.0.0'); ?>" rel="stylesheet" type="text/css"> -->
  <style type="text/css">
    body {
      background-image: linear-gradient(to bottom right, #d13f2d, #da4230, #8e44ad);
    }
    body > header > section#cs--header__logo {
      width: 100%;
      min-width: 100vw;
    }
    body > header > section#cs--header__logo img {
      width: 100%;
      height: auto;
      max-width: 250px;
      text-align: center;
    }
    body > main {
      justify-items: center;
      align-items: center;
    }
    body > main > section#cs--login {
      padding: 10px;
    }
    body > main > section#cs--login h3 {
      margin: 0;
    }
    body > footer {
      text-align: center;
    }
    body > footer > p {
      font-size: 0.7em;
      font-style: italic;
      color: #eeeeee;
    }
  </style>
</head>
<body class="cs-page cs-components">
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->
  <!-- Start Header -->
  <header>
    <section id="cs--header__logo">
        <img src="<?php echo get_site_url('/img/cornerstone_framework_logo_white.svg'); ?>" alt="Cornerstone PHP Framework">
    </section>
  </header>
  <!-- End Header ~#~ Start Main -->
  <main>
    <section id="cs--login" class="csc-card center">
      <form action="<?php echo get_site_url('includes/cornerstone/login.inc.php'); ?>" method="POST" class="csc-form">
        <h3 class="csc-h3">Welcome to Cornerstone</h3>
        <p class="csc-body1">Sign in to continue</p>
        <?php if(isset($_GET['errors'])) {
          $errors = explode(',', $_GET['errors']);
          if (in_array('dbe', $errors)) {
            echo '<p class="csc-body1 csc-red-text">There was an error with the login details you provided. Please try again.</p>';
          }
        } ?>
        <div class="csc-input-field">
          <input type="text" name="udata" id="udata" autocapitalize="off">
          <label for="udata">Email/Username</label>
        </div>
        <div class="csc-input-field">
          <input type="password" name="pwd" id="pwd">
          <label for="pwd">Password</label>
        </div>
        <button type="submit" name="action" value="log-in" class="csc-btn">Sign In <i class="material-icons csc-bi-right">exit_to_app</i></button>
      </form>
    </section>
  </main>
  <!-- End Main ~#~ Start Footer -->
  <footer>
    <p>&copy; 2019 <?php echo SITE_NAME; ?> &middot; Built with Cornerstone v<?php echo CS_VERSION; ?></p>
  </footer>
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
  <!-- Cornerstone Scripts -->
  <script src="<?php echo get_site_url('js/cornerstone.js'); ?>"></script>
</body>
</html>