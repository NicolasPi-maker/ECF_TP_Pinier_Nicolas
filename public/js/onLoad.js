window.onload = () => {
    document.querySelectorAll(".global").forEach((input, index) => {
        if (input.value === '1') {
            input.checked = true
            input.style.display = "none";
        }
    })
}





