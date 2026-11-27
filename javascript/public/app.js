// This runs in the browser, after the page has loaded: that's what defer does.
const data = JSON.parse(document.getElementById('data').textContent);
const select = document.getElementById('size');
const choice = document.getElementById('choice');

// Build the options from the data PHP put in the page.
for (const size of data.sizes) {
    const option = document.createElement('option');
    option.textContent = size;
    select.append(option);
}

// Answer when the visitor picks one. textContent puts text in the page as
// text; innerHTML would run any HTML in it.
select.addEventListener('change', () => {
    choice.textContent = `${data.name} picked ${select.value}.`;
});
