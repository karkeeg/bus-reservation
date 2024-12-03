
        // Script to handle scrolling of the tour cards
document.addEventListener('DOMContentLoaded', () => {
    const tourCardsContainer = document.querySelector('.tour-cards');
    const scrollLeftBtn = document.getElementById('scrollLeft');
    const scrollRightBtn = document.getElementById('scrollRight');

    // Scroll left button
    scrollLeftBtn.addEventListener('click', () => {
        tourCardsContainer.scrollBy({
            left: -300,
            behavior: 'smooth'
        });
    });

    // Scroll right button
    scrollRightBtn.addEventListener('click', () => {
        tourCardsContainer.scrollBy({
            left: 300,
            behavior: 'smooth'
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const learnMoreButtons = document.querySelectorAll('.learn-more');

    learnMoreButtons.forEach(button => {
        button.addEventListener('click', () => {
            const moreInfo = button.nextElementSibling; // This is the .more-info div

            // Toggle the visibility of the more-info section
            if (moreInfo.style.display === 'none' || moreInfo.style.display === '') {
                moreInfo.style.display = 'block';  // Show the additional information
                button.textContent = 'Show Less';  // Change button text
            } else {
                moreInfo.style.display = 'none';   // Hide the additional information
                button.textContent = 'Learn More'; // Reset button text
            }
        });
    });
});
