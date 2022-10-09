//Permet au moment du chargement de la page de checked/unchecked les bouton toggle selon leur valeur
const dynamicCheckedToggle = () => {
    document.querySelectorAll(".global").forEach(input => {
        if(input.value === '1') {
            input.checked = true
            input.style.display = "none";
        }
    })
}

window.onload = () => {
    dynamicCheckedToggle();
}

