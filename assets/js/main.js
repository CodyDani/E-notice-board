// main.js - Notice Board JavaScript

/**
 * Fetches notices from the API and renders them as cards in the DOM.
 */
async function fetchNotices() {
    try {
        const response = await fetch('api/get_notices.php');
        if (!response.ok) {
            throw new Error('Failed to fetch notices');
        }
        const notices = await response.json();

        const container = document.getElementById('notices');
        if (!container) {
            console.error('No element with id="notices" found');
            return;
        }

        container.innerHTML = ''; // Clear existing content

        notices.forEach(notice => {
            const card = document.createElement('div');
            card.className = 'notice-card';

            const title = document.createElement('h3');
            title.textContent = notice.title;

            const content = document.createElement('p');
            content.textContent = notice.content;

            const meta = document.createElement('div');
            meta.className = 'meta';

            const categoryTag = document.createElement('span');
            categoryTag.className = 'tag category';
            categoryTag.textContent = `Category: ${notice.category}`;

            const targetTag = document.createElement('span');
            targetTag.className = 'tag target';
            targetTag.textContent = `Target: ${notice.target_role}`;

            const date = document.createElement('span');
            date.className = 'date';
            date.textContent = new Date(notice.created_at).toLocaleDateString();

            meta.appendChild(categoryTag);
            meta.appendChild(targetTag);
            meta.appendChild(date);

            card.appendChild(title);
            card.appendChild(content);
            card.appendChild(meta);

            container.appendChild(card);
        });
    } catch (error) {
        console.error('Error fetching notices:', error);
        const container = document.getElementById('notices');
        if (container) {
            container.innerHTML = '<p>Error loading notices. Please try again later.</p>';
        }
    }
}

// Optionally, call fetchNotices on page load if the element exists
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('notices')) {
        fetchNotices();
    }
});