document.querySelectorAll(".js-filter input").forEach(input => {
    input.addEventListener("change", () => {
        const Params = new URLSearchParams();

        Params.append('filter', input.value)

        const Url = new URL(window.location.href);

        fetch(`${Url.pathname}?${Params.toString()}&ajax=1`, {
            headers: {
                "X-Requested-With": "XMLHttPRequest"
            }
        }).then(response =>
            response.json()
        ).then(data => {
            const content = document.querySelector("#content");
            content.innerHTML = data.content;
            dynamicCheckedToggle();
        }).catch(e => {
                console.log(e)
            })
    })
})

//Permet au moment du chargement de la page de checked/unchecked les bouton toggle selon leur valeur
const dynamicCheckedToggle = () => {
    document.querySelectorAll(".switch input").forEach(input => {
        if(input.value === '1') {
            input.checked = true
        }
    });
}

dynamicCheckedToggle();
