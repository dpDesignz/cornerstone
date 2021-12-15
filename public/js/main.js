// Place any custom javascript code for your project in here

// Open mobile menu
function toggleMM(elm) {
  // Get the menu
  const menu = document.querySelector('#sidebar__nav');
  const menuBtn =
    elm.target.nodeName === 'BUTTON' ? elm.target : elm.target.parentNode;
  // Check if the menu is open
  if (menu.classList.contains('nav--open')) {
    // Menu is open. Close and toggle aria states
    menu.classList.remove('nav--open');
    menuBtn.classList.remove('on');
    menuBtn.attributes['aria-hidden'] = 'false';
  } else {
    // Menu is closed. Open and toggle aria states
    menu.classList.add('nav--open');
    menuBtn.classList.add('on');
    menuBtn.attributes['aria-hidden'] = 'true';
  }
}
const hmmBtn = document.querySelector('#header__mobile-nav__btn');
if (hmmBtn) {
  hmmBtn.addEventListener('click', function(elm) {
    toggleMM(elm);
  });
}
