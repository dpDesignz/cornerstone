# Cornerstone Change Log

## 0.2.19 - Content (2020-03-27)

- ➕ Added the vanilla JS ready state function
- ➕ Added the `cs_content`, `cs_content_meta`, `cs_content_section`, and `cs_seo_url` tables
- ➕ Added the `cornerstonecore.model.php` file to the system models for loading seo and content data
- 🖊️ Changed the `core.class.php` default `$currentMethod` from `error` to `loadpage`
- ➕ Added the [Html2Text](https://github.com/mtibben/html2text) library

## 0.2.18 - Options (2020-03-14)

- ➕ Added the options class
- ➕ Added the setting to set option data in the load view data
- 🧹 Fixed session and bootstrap EOF bugs
- 🧹 Fixed an error where `request->set_params()` fell over if a data item was an array or object
- ➕ Added files and documentation for personal usage

## 0.2.17 - Composer Updates (2020-02-03)

- 🔄 Updated ezSQL to v4.0.9
- 🔄 Updated whichbrowser to v6.1.4
- 🔄 Updated phpmailer to v6.1.4
- 🔄 Updated symfony polyfills (php72 v1.13.1, mbstring v1.13.1, intl-idn v1.13.1, iconv v 1.13.1)
- 🔄 Updated egulias email validator to v2.1.15
- ➕ Added loader, pagination, registry, and request classes
- ➕ Added the `outputBreadcrumbs()` function to the `output` helper file
- 🧹 Refractored the core controller
- ➕ Added the ability to debug load view data
- ➖ Removed the `outputPagination()` function from the output helper file

## 0.2.16 - Notification System (2020-01-19)

- ➕ Added the `cs_notification` table
- ➕ Added the `notification` and `notificationManager` classes
- ➕ Added the [Toastify](https://apvarun.github.io/toastify-js/) script

## 0.2.15 - UX Changes (2020-01-06)

- ➕ Added the `.csc-btn--inlineunder` modified button class
- ➕ Added to `getUrl()` function in the core loader to change dashes to underscores
- ➕ Added styling to allow the use of the [select2](https://select2.org/) script
- ➕ Added the ability to set an alert as not closable by setting `data-clsoable="false"`
- Fixed issue where error was being thrown if an  input didn't have a label
- Changed the styling for the `.csc-helper-text` class to be positioned so it can "float" rather than shift the page

## 0.2.14 - Pagination (2019-12-28 to 2020-01-01)

- ➕ Added the script to reload a table when the page limit is changed
- ➕ Added the pagination output function (`outputPagination()`)
- ➕ Added the sort URL builder function (`get_sort_url()`)
- ➕ Added the check sort item function (`check_sort_item()`)
- ➕ Added `.csc-pagemenu` styling
- ➕ Added the `.csc-data-table__search` styling
- Updated the `get_sort_order()` function to output the sort if set

## 0.2.13 - UI Changes (2019-12-12 to 2019-12-16)

- ➕ Added component subdirectory for example components
- ➕ Added [Inflector](https://github.com/ICanBoogie/Inflector)
- ➕ Added [Pickadate](https://amsul.ca/pickadate.js/)
- ➕ Added custom styling for Pickadate script output
- Changed the Uppy button to follow the button outlined styling as it was most often be a secondary button
- ➕ Added function to remove AJAX modals from the DOM on close
- ➕ Added [Inputmask](http://robinherbots.github.io/Inputmask)

## 0.2.12 - Functions + UI Changes (2019-12-06 to 2019-12-07)

- Cleaned up the cornerstone.js file
- ➕ Added an animation to submit buttons
- Changed button styles to match alert styles and added proper class names (`cs-btn--color` prefixes)
- Changed the alert styles to allow for icons and a cleaner design
- ➕ Add the ability to close alerts on click
- Changed event listeners for active input labels to ES6 Javascript
- ➕ Added a `pageType` property to the controller class
- Fixed no results styling bug on chosen
- Changed `csc-bi` to `csc-bi--left`
- Lowered the `min-height` on the trumbowyg editor

## 0.2.11 - Functions (2019-11-30)

- ➕ Added a filters functions file
- ➕ Added a function to refactor the `sort` filter

## 0.2.10 - UI Changes (2019-11-25)

- ➕ Added styling for uppy script elements
- Fixed autoloading the Trumbowyg editor if not loaded

## 0.2.9 - Webpack + Scripts (2019-11-21)

- Started to integrate webpack
- ➕ Added the [Trumbowyg script](https://alex-d.github.io/Trumbowyg/)
- ➕ Added `$pageHasForm` boolean variable to header and footer tags to allow of loading form related scripts and stylesheets only where needed.
- Removed the default `text-align: center;` on `csc-data-table` elements
- ➕ Added the [Lazysizes script](https://github.com/aFarkas/lazysizes) for lazy image loading
- ➕ Added the [php-image-resize script](https://github.com/gumlet/php-image-resize) for image uploading and dynamic resizing
- ➕ Added the [Uppy script](https://uppy.io/) for image uploading

## 0.2.8 - UI Changes (2019-11-15 to 2019-11-16)

- Installed `include-media` into core scss Helpers
- Moved grid system into its own .scss file in the layout folder and moved relevant media queries here as well
- Updated Users index to reflect new grid styling
- ➕ Added auto resizing textarea thanks to [Stephan Wagner](https://stephanwagner.me/auto-resizing-textarea-with-vanilla-javascript)
- ➕ Added character counter based on [this character countdown script](https://www.jqueryscript.net/form/character-countdown-text-field.html)
- Changed input label active to trigger on change as well as focus
- Removed tinylimiter script
- ➕ Added `$pageHasForm` variable to admin footer to only load form specific scripts when required
- Updated all cdnjs scripts to have integrity checks
- Updated user forms to reflect new styling
- ➕ Added default colour border on `chosen-container-active` to show active chosen element when tabbed onto

## 0.2.7 - UI Changes (2019-11-10)

- Changed flat button styling to relate to good ux. Added underline and
  hover elements
- ➕ Added requirement for span in `csc-btn--flat` to allow for underlined text but not icon
- ➕ Added `csc-btn--outlined` back in to the stylesheet
- Fixed waves selector in `cornerstone.js` to load any `csc-btn*` class
- Removed css grid from `csc-col`
- ➕ Added `align-items:center` to `csc-card`
- ➕ Added `.inline` to `csc-badge` to allow displaying the badge next to text
- ➕ Added `width: 100%;` to `csc-data-table`
- ➕ Added `display: grid;` to `.csc-ga*` to allow positioning
- Fixed button link color in data table
- Fixed `csc-badge` extension styling

## 0.2.6 - Core Changes (2019-10-25)

- Changed how core methods get the url to prevent spaces being stripped and added code to strip tags to prevent malicious code being executed
- Changed how flashMsg class is assigned. No longer requires the full class name
- Add a tiny button size for sidebars
- Changed `.csc-col` from `align-items` to `align-content`
- ➕ Added more styling options for `csc-badge` to allow use of framed layout

## 0.2.5 - UI Changes (2019-10-11 to 2019-10-21)

- Removed `clearCustomAuth` function from logout script
- Cleaned up admin dashboard to allow more breathing room
- ➕ Added `--wide` button style
- Changed admin user form page style
- ➕ Added animation to admin forms
- Re-designed admin menu
- Fixed dead breadcrumb design
- Updated notification styling
- ➕ Added option for card with no max-width set

## 0.2.4 - Views, Models, and Selects (2019-09-22)

- ➕ Added requirement to use `.vw.php` suffix on view files for easier identification in IDE/SCE
- ➕ Added ability to set custom logo in admin header
- ➕ Added `ext.navbar.php` to theme files for extending the sidebar menu
- Moved models into folders and modified controller to handle retrieving the model name
- Removed `clearCustomAuth()` function from `ext.userauth.php` file as it's not longer user in the logout method
- Fixed link to `ext.userauth.php` file in the `authenticateUser()` method
- Initialised protected `$data` property as an array in core controller class
- Defined default param types in model methods
- Changed `listUsers()` method in 'user.model.php' to default to order by `user_first_name`
- Fixed error of infinite loop when loading 404 page

## 0.2.3 - Admin UI (2019-09-16 to 2019-09-21)

- Updated Admin UI sidebar navigation
- Updated password reset templates
- Fixed fallover if method doesn't exist in controller
- ➕ Added [JavaScript Cookie](https://github.com/js-cookie/js-cookie) script
- ➕ Added cookie for saving collapsed menu state
- Changed Admin common file to load 'index' model by default.
- Add "All Users" page
- ➕ Added grid layout options for aligning
- Fixed early closing tag in head that wasn't loading jquery
- Removed async tag from jquery loading to resolve double loading fallover
- ➕ Added support for custom admin css file
- ➕ Added `get_public_path()` function
- Changed csc-data-table to default to 100% width
- ➕ Added link visited colour variable to sass
- Fixed time reset bug when checking if `$_SESSION['_cs']['CREATED']` is expired
- ➕ Added [Chosen selector](https://harvesthq.github.io/chosen/)

## 0.2.2 - Security updates (2019-09-03)

- Moved mail helper into its own class and deleted the `fn.mail` helper file. This can now be called using `new SendMail()`.
- ➕ Added `userPageProtect()` function to the session helper
- Changed how cookie data is set on 'remember me'
- Changed how session data is handled and expired. Added default values for `session.use_strict_mode`, `session.use_trans_sid`, and `session.cookie_domain`.
- ➕ Added `htmlspecialchars` to `$_GET` variables to [restrict XSS](https://www.cloudways.com/blog/php-security/)
- Implemented further security recommendations based on [OWASP cheat sheets](https://github.com/OWASP/CheatSheetSeries)
- Invalidate users existing sessions and cookies if set when changing their password.
- ➕ Added manual session time out and session re-generating for users for extra security.
- ➕ Added `$hideThemeFooter` variable for hiding the theme footer if you want to define your own in the view.
- ➕ Added alpha dashboard UI design

## 0.2.1 - Initial Alpha Release (2019-09-02)

- Core MVC Structure
- Initial admin user functions (login, email based 2FA, remember me, login log, forgot password, password reset)
- Custom database based session handler
- Custom error handler
- PHPMailer and Swiftmailer support
  - HTML and Plain text Email templates for:
    - 2FA (auhorization)
    - Forgot Password - Account exists (password-reset)
    - Forgot Password - Account doesn't exist (password-reset-help)
    - New Password confirmation (new-password)
- Initial Helpers/Functions:
  - Master:
    - redirectTo()
    - get_option()
    - get_site_url()
    - get_sys_path()
    - get_lib_path()
    - get_theme_path()
  - Generate:
    - get_crypto_key()
    - get_crypto_token()
    - get_pin()
  - Mail:
    - get_mail_options()
    - send_phpmail()
    - send_swiftmail()
    - create_email_template()
  - Output:
    - friendlyDtmDiff()
    - showValidationErrors()
  - Session (_this also holds the session controller information_):
    - flashMsg()
    - checkFlashMsg()
    - isLoggedInUser()
- Custom material design based component specific css styling generated from sass file(s)
