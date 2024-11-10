// Example of form validation for login form
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.querySelector('form');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (email === '' || password === '') {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    }
});

// Example of dynamic behavior for listings
function toggleDetails(itemId) {
    const details = document.getElementById(`details-${itemId}`);
    if (details.style.display === 'none' || details.style.display === '') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const imagePreview = document.createElement('img');
    imagePreview.style.display = 'none';
    imageInput.parentNode.insertBefore(imagePreview, imageInput.nextSibling);

    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                imagePreview.style.width = '100px';
                imagePreview.style.height = 'auto';
            };
            reader.readAsDataURL(file);
        }
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const listings = document.querySelectorAll('.listing-item');

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.toLowerCase();
        listings.forEach(function (listing) {
            const title = listing.querySelector('h3').textContent.toLowerCase();
            const author = listing.querySelector('p').textContent.toLowerCase();
            if (title.includes(query) || author.includes(query)) {
                listing.style.display = 'block';
            } else {
                listing.style.display = 'none';
            }
        });
    });
});
// Check for new messages every 30 seconds
setInterval(function () {
    fetch('check_new_messages.php')
        .then(response => response.json())
        .then(data => {
            if (data.newMessages > 0) {
                alert(`You have ${data.newMessages} new message(s)`);
            }
        })
        .catch(error => console.error('Error checking new messages:', error));
}, 30000);

