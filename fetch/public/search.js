const input = document.getElementById('q');
const results = document.getElementById('results');
const message = document.getElementById('message');

// Each time the visitor types, ask the server, and show the answer without
// loading a new page.
input.addEventListener('input', async () => {
    const url = '/search.php?' + new URLSearchParams({ q: input.value });
    const response = await fetch(url);
    const body = await response.json();

    results.replaceChildren();

    // fetch() only fails when the network does. A 400 or a 500 arrives as
    // an ordinary response, so check.
    if (!response.ok) {
        message.textContent = body.error;
        return;
    }

    message.textContent = body.matches.length ? '' : 'No matches.';
    for (const color of body.matches) {
        const item = document.createElement('li');
        item.textContent = color;
        results.append(item);
    }
});
