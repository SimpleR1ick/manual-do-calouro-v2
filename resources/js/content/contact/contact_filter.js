// get filter element
const filterElement = document.querySelector('#filter');

// get cards elements
const cards = document.querySelectorAll('.accordion-item');

// add input event for the filter element
filterElement.addEventListener('input', filterCards);

// filter function
function filterCards() {
    // if the filter is not empty
    if (filterElement.value != '') {
        // for each card of cards
        for (let card of cards) {
            // get card heading (title)
            let title = card.querySelector('h2');
            
            // tranform to lower case
            title = title.textContent.toLocaleLowerCase();

            // transform filter text to lower case
            let filterText = filterElement.value.toLocaleLowerCase();

            // if card title does not include the filter text
            if (!title.includes(filterText)) {
                // hide the card element
                card.style.display = "none";
            } else {
                // unhide the card element
                card.style.display = "block";
            }
        }
    } else {
        // for each card of cards
        for (let card of cards) {
            // unhide the card element
            card.style.display = "block";
        }
    }
}