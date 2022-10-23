const openBurgerBtn = document.querySelector('.jsOpenBurgerButton');
const closeBurgerBtn = document.querySelector('.closeBurgerButton');
const burgerMenu = document.querySelector('.jsBurgerMenu');

if (openBurgerBtn) {
  openBurgerBtn.addEventListener('click', () => {
    burgerMenu.classList.add('opacity-100');
    burgerMenu.classList.remove('translate-x-full');
  })
}
if (closeBurgerBtn) {
  closeBurgerBtn.addEventListener('click', () => {
    burgerMenu.classList.remove('opacity-100');
    burgerMenu.classList.add('translate-x-full');
  })
}