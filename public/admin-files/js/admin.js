/*!
// Admin specific JS file for running Cornerstone Framework scripts
*/

const ps = new PerfectScrollbar('nav#sidebar__nav');
console.log(ps);

// Open sub-menu
$( "li.has-subnav a[data-toggle=\"collapse\"]" ).on("click", function(e) {
  var open = $(this).hasClass("open");
  $(this)[open ? "removeClass" : "addClass"]("open").attr("aria-expanded", [open ? "false" : "true"]);
  $(this).siblings("ol.sidebar__sub-nav").attr("aria-hidden", [open ? "true" : "false"]);
  ps.update();
  console.log(ps);
  e.preventDefault();
});

// Collapse sidebar
$( "#collapse-btn" ).on("click", function() {
  if($("body.cs-admin").hasClass( "sidebar__collapsed" )) {
    $("body.cs-admin").removeClass( "sidebar__collapsed" );
    Cookies.remove('sbar');
  } else {
    $("body.cs-admin").addClass( "sidebar__collapsed" );
    Cookies.set('sbar', 'true', { expires: 365 });
  }
});