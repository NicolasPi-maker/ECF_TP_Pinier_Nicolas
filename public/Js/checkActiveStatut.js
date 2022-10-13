//Permet au moment du chargement de la page de checked/unchecked les bouton toggle selon leur valeur
let franchiseCard = document.getElementsByClassName("main-wrapper")

const dynamicCheckedToggle = () => {
    document.querySelectorAll(".global").forEach((input, index) => {
        if(input.value === '1') {
            input.checked = true
            input.style.display = "none";
        } else {
            franchiseCard[index].style.opacity = "0.5";
        }
    })
}

dynamicCheckedToggle();
