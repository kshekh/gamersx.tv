const openBurgerBtn = document.querySelector('.jsOpenBurgerButton');
const closeBurgerBtn = document.querySelector('.closeBurgerButton');
const burgerMenu = document.querySelector('.jsBurgerMenu');

console.log(openBurgerBtn)
console.log(closeBurgerBtn)

openBurgerBtn.addEventListener('click', () => {
  burgerMenu.classList.add('opacity-100');
  burgerMenu.classList.remove('translate-x-full');
})

closeBurgerBtn.addEventListener('click', () => {
  burgerMenu.classList.remove('opacity-100');
  burgerMenu.classList.add('translate-x-full');
})