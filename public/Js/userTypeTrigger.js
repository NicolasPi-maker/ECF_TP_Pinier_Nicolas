let setLinkedFranchise = document.getElementById("select_user_franchise");

document.querySelectorAll("#user_roles" ).forEach(input => {
    input.addEventListener('click', () => {
        if(input.value === 'ROLE_STRUCTURE') {
            setLinkedFranchise.style.display = "block";
        } else {
            setLinkedFranchise.style.display = "none";
        }
    })
})

