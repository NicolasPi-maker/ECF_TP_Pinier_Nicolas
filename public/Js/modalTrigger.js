let franchiseForm = document.getElementById("form-container");
let structurePermsForm = document.getElementsByClassName("structure-perms-form");

let buttonGlobalPerms = document.getElementById("btn-global-perms");
let globalPermsModal = document.getElementById("globalPermsModal");
let closeGlobalModal = document.getElementById("close-global-perms");

// Get the modal
let closeButton = document.getElementsByClassName("close-button");

let deleteModal = document.getElementsByClassName("deleteModal");

let confirmDelete = document.getElementsByClassName("confirmDelete");
let closeDelete = document.getElementsByClassName("close-delete");

let updateActive = document.getElementsByClassName("updateActive");
let buttonUpdateStructure = document.getElementsByClassName("btn-update-structure");

let structurePerms = document.getElementsByClassName("structure-perms-toggle");
let openStructurePerms = document.getElementsByClassName("more-perms");
let closeStructurePerms = document.getElementsByClassName("less-perms");

let structurePermsModal = document.getElementsByClassName("update-structure-perms-modal");
let openStructurePermsModal = document.getElementsByClassName("open-structure-perms-modal");
let closeStructurePermsModal = document.getElementsByClassName("close-update-structure-perms");

if(openStructurePermsModal) {
    for(let i=0; i < structurePermsModal.length; i++) {
        openStructurePermsModal[i].addEventListener('click', () => {
            structurePermsModal[i].style.display = "block";
        })
    }
}

if(closeStructurePermsModal) {
    for(let i=0; i < structurePermsModal.length; i++) {
        closeStructurePermsModal[i].addEventListener('click', () => {
            structurePermsModal[i].style.display = "none";
        })
    }
}


if(openStructurePerms) {
    for(let i=0; i < structurePerms.length; i++) {
        openStructurePerms[i].addEventListener('click', () => {
            structurePerms[i].style.display = "block";
            openStructurePerms[i].style.display = "none";
            closeStructurePerms[i].style.display = "block";
        })
    }
}

if(closeStructurePerms) {
    for(let i=0; i < structurePerms.length; i++) {
        closeStructurePerms[i].addEventListener('click', () => {
            structurePerms[i].style.display = "none";
            openStructurePerms[i].style.display = "block";
            closeStructurePerms[i].style.display = "none";
        })
    }
}

if(confirmDelete) {
    for(let i=0; i < deleteModal.length; i++) {
        confirmDelete[i].addEventListener('click', () => {
            deleteModal[i].style.display = "block";
        })
    }
}

if(closeDelete) {
    for(let i=0; i < deleteModal.length; i++) {
        closeDelete[i].addEventListener('click', () => {
            deleteModal[i].style.display = "none";
        })
    }
}

if(buttonUpdateStructure) {
    for(let i=0; i < updateActive.length; i++) {
        buttonUpdateStructure[i].addEventListener('click', () => {
            updateActive[i].style.display = "block";
        })
    }
}

if(closeButton) {
    for(let i=0; i < updateActive.length; i++) {
        closeButton[i].addEventListener('click', () => {
            updateActive[i].style.display = "none";
        })
    }
}

if(franchiseForm) {
    franchiseForm.addEventListener('change', () => {
        buttonGlobalPerms.style.display = "block";
    })
}

if(structurePermsForm) {
    for(let i=0; i < structurePermsForm.length; i++) {
        structurePermsForm[i].addEventListener('change', () => {
            openStructurePermsModal[i].style.display = "block";
        })
    }
}

if(buttonGlobalPerms) {
    buttonGlobalPerms.addEventListener('click', () => {
        globalPermsModal.style.display = "block";
    })
}

if(closeGlobalModal) {
    closeGlobalModal.addEventListener('click', () => {
        globalPermsModal.style.display = "none";
    })
}


