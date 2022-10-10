//Permet au moment du chargement de la page de checked/unchecked les bouton toggle selon leur valeur
window.onload = () => {
    document.querySelectorAll(".global").forEach(input => {
        if(input.value === '1') {
            input.checked = true
            input.style.display = "none";
        }
    })
}

updatePermsDisplay();


