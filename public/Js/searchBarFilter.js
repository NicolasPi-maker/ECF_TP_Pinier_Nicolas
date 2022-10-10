let searchBar = document.querySelector("#search-filter input")

searchBar.addEventListener('input', () => {
    const Params = new URLSearchParams();

    Params.append('search_filter', searchBar.value);

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
        updatePermsDisplayOnSearch();
    }).catch(e => {
        throw new Error(e)
    })
})

const updatePermsDisplayOnSearch = () => {
    document.querySelectorAll(".global").forEach(input => {
        if(input.value === '1') {
            input.checked = true
            input.style.display = "none";
        }
    })
}

