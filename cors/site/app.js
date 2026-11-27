// This page is on localhost:8001. The API is on localhost:8000, a different
// port, so a different origin.
const result = document.getElementById('result');

// Sending JSON makes this more than a simple request, so the browser asks
// the API first, with an OPTIONS request: the preflight.
fetch('http://localhost:8000/', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ hello: 'API' }),
})
    .then((response) => response.json())
    .then((body) => {
        result.textContent = body.message;
    })
    .catch((error) => {
        // The browser won't say why, here. The console does.
        result.textContent = `Blocked: ${error.message}`;
    });
