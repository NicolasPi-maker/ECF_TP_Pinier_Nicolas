let btnFlashClose = document.getElementsByClassName("btn-close");
let flashAlert = document.getElementsByClassName("alert");

for(let i = 0; i < btnFlashClose.length; i++) {
    btnFlashClose[i].addEventListener('click', () => {
        flashAlert[i].style.display = "none";
    })
}