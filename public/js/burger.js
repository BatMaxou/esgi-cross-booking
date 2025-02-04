const burgerBtn = document.querySelector('.nav .btn.burger');
const burgerMenu = document.querySelector('.nav .list.burger')

burgerBtn.addEventListener('click', () => {
    burgerMenu.classList.toggle('active')
});
