// Toggle mobile nav
const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
mobileNavToggle.addEventListener('click', () => {
  document.getElementById('navbar').classList.toggle('navbar-mobile');
});
