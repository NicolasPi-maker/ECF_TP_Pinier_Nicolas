//Permet au moment du chargement de la page de checked/unchecked les bouton toggle selon leur valeur
let franchiseCard = document.getElementsByClassName("main-wrapper")
let franchiseInfos = document.getElementsByClassName("franchise-info-wrapper");
let franchisePermissions = document.getElementsByClassName("franchise-permissions");


let structurePermissions = document.getElementsByClassName('js-structure-permissions');
let structureCards = document.getElementsByClassName("js-disabled-structure");

let opacity = "0.5";
let borderStyle = "dashed";

window.onload = () => {
    document.querySelectorAll(".global").forEach((input, index) => {
        if(input.value === '1') {
            input.checked = true
            input.style.display = "none";
        } else if(!structureCards[index]) {
            if(franchiseCard[index]) {
                franchiseCard[index].style.opacity = opacity;
                franchiseCard[index].style.border = borderStyle;
            }

            if(franchiseInfos[index]) {
                franchiseInfos[index].style.opacity = opacity;
                franchiseInfos[index].style.border = borderStyle;
            }

            if(franchisePermissions[index]) {
                franchisePermissions[index].style.opacity = opacity;
                franchisePermissions[index].style.border = borderStyle;
            }
        }
    })

    document.querySelectorAll(".btn-update-structure").forEach((input, index) => {
        if(input.value !== '1' && structureCards[index]) {
            structureCards[index].style.opacity = opacity;
            structureCards[index].style.border = borderStyle;

            if(structurePermissions[index]) {
                structurePermissions[index].style.opacity = opacity;
                structurePermissions[index].style.border = borderStyle;
            }
        }
    })
}





