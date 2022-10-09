document.querySelectorAll(".js-filter input").forEach(input => {
    input.addEventListener("change", () => {
        const Params = new URLSearchParams();

        Params.append('filter', input.value);

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
        }).catch(e => {
            throw new Error(e)
        })
    })
})

