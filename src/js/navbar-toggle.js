// This script replaces jQuery.js and Bootstrap.js
// Only functionality that is needed for this site is kept
// All HTML needs to stay the same, so it should still work with the original dependencies

(function(document, find, button, target){

    // Fetch first and only toggle button
    button = document[find]('[data-toggle=collapse]')[0];

    // Fetch target based on data-target attribute
    target = document[find](button.dataset.target)[0];

    // Listen to click event
    button.addEventListener('click', function() {

        // Update class
        target.classList.toggle('collapse');
    });

    // Pass querySelectorAll as parameter as it is used twice (saves characters)
})(document, 'querySelectorAll');
