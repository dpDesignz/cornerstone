/*!
// Admin specific JS file for running Cornerstone Framework scripts
*/

// Set the perfect scrollbar where needed
const mainContent = document.querySelector('#main__content');
let ps = '';
if (mainContent) {
  // Add scrollbar to the sidebar if need be
  const contentHeight = mainContent.querySelector('main:first-child')
    .offsetHeight;
  const navbarHeight = document.querySelector('#sidebar__nav-links')
    .offsetHeight;
  if (contentHeight > navbarHeight) {
    document.querySelector('#sidebar__nav').style.height = `${contentHeight}px`;
  }
  ps = new PerfectScrollbar('nav#sidebar__nav');
}

// Open Menu sub nav
const menuSubNav = document.querySelectorAll(
  `li.has-subnav a[data-toggle="collapse"]`
);
if (menuSubNav) {
  menuSubNav.forEach(navItem =>
    navItem.addEventListener('click', function(e) {
      // Check if the nav is currently open
      const open = navItem.classList.contains('open');

      // Modify the classList
      navItem.classList[open ? 'remove' : 'add']('open');

      // Check if item has the 'aria-expanded' attribute
      if (navItem.hasAttribute('aria-expanded')) {
        // Change the aria-expanded value
        navItem.setAttribute('aria-expanded', [open ? 'false' : 'true']);
      }

      // Get sibling sub-nav
      const subNav = navItem.nextElementSibling;
      if (subNav && subNav.classList.contains('sidebar__sub-nav')) {
        // Check if sibling nav has the 'aria-hidden' attribute
        if (subNav.hasAttribute('aria-hidden')) {
          // Change the aria-hidden value
          subNav.setAttribute('aria-hidden', [open ? 'true' : 'false']);
        }
      }

      // Update perfect scrollbar if it exists
      if (ps !== '') {
        ps.update();
      }
      e.preventDefault();
    })
  );
}

// Collapse sidebar
const collapseSidebarBtn = document.querySelector(`#collapse-btn`);
if (collapseSidebarBtn) {
  collapseSidebarBtn.addEventListener('click', () => {
    // Get body tag
    const bodyTag = document.querySelector('body.cs-admin');
    if (bodyTag) {
      // Get sidebar state
      const sidebarState = bodyTag.classList.contains('sidebar__collapsed');

      // Toggle sidebar state
      if (sidebarState) {
        // Remove the collapsed class
        bodyTag.classList.remove('sidebar__collapsed');
        // Remove the sidebar state cookie
        Cookies.remove('csasbs');
      } else {
        // Add the collapsed class
        bodyTag.classList.add('sidebar__collapsed');
        // Add the sidebar state cookie
        Cookies.set('csasbs', 'true', { expires: 365 });
      }
    }
  });
}
