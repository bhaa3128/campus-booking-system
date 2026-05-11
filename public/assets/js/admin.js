document.querySelectorAll('.admin-sidebar a').forEach(link => {

    link.addEventListener('click', function () {

        const targetId = this.getAttribute('href');
        const target = document.querySelector(targetId);

        if (!target) return;

        setTimeout(() => {

            target.classList.add('highlighted');

            setTimeout(() => {
                target.classList.remove('highlighted');
            }, 2000);

        }, 250);

    });

});



const sidebarLinks = document.querySelectorAll('.admin-sidebar a');
const adminSections = document.querySelectorAll('.admin-section');

function setActiveSidebarLink() {
    let currentSectionId = '';

    adminSections.forEach(section => {
        const sectionTop = section.offsetTop - 160;

        if (window.scrollY >= sectionTop) {
            currentSectionId = section.getAttribute('id');
        }
    });

    sidebarLinks.forEach(link => {
        link.classList.remove('active');

        if (link.getAttribute('href') === '#' + currentSectionId) {
            link.classList.add('active');
        }
    });
}

window.addEventListener('scroll', setActiveSidebarLink);
window.addEventListener('load', setActiveSidebarLink);


function showToast(message, type = 'success') {

    const container = document.getElementById('toastContainer');

    const toast = document.createElement('div');

    toast.className = `toast ${type}`;

    toast.textContent = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 4000);
}
window.addEventListener('load', () => {
    showToast('Admin Panel erfolgreich geladen 🚀', 'info');
});
const themeToggle = document.getElementById('themeToggle');

if (themeToggle) {
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-mode');
        themeToggle.textContent = '☀️';
    }

    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('light-mode');

        if (document.body.classList.contains('light-mode')) {
            localStorage.setItem('theme', 'light');
            themeToggle.textContent = '☀️';

            if (typeof showToast === 'function') {
                showToast('Light Mode aktiviert ☀️', 'info');
            }
        } else {
            localStorage.setItem('theme', 'dark');
            themeToggle.textContent = '🌙';

            if (typeof showToast === 'function') {
                showToast('Dark Mode aktiviert 🌙', 'info');
            }
        }
    });
}
const ordersChartCanvas = document.getElementById('ordersChart');

if (ordersChartCanvas) {

    new Chart(ordersChartCanvas, {
        type: 'bar',

        data: {
            labels: [
                'Benutzer',
                'Bestellungen',
                'Zimmerbuchungen',
                'Verifizierte Studenten'
            ],

            datasets: [{
                label: 'Statistik',

                data: [
                    2,
                    24,
                    1,
                    1
                ],

                borderWidth: 2,
                borderRadius: 14
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            },

            scales: {
                x: {
                    ticks: {
                        color: 'white'
                    }
                },

                y: {
                    ticks: {
                        color: 'white'
                    }
                }
            }
        }
    });
}

const roomsChartCanvas = document.getElementById('roomsChart');

if (roomsChartCanvas) {

    new Chart(roomsChartCanvas, {
        type: 'doughnut',

        data: {
            labels: [
                'Freie Zimmer',
                'Belegte Zimmer'
            ],

            datasets: [{
                data: [
                    99,
                    114
                ],

                borderWidth: 0
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            }
        }
    });
}
document.querySelectorAll('.ajax-order-status-form').forEach(form => {

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const button = form.querySelector('button');

        button.disabled = true;
        button.textContent = 'Speichert...';

        try {
            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');
            } else {
                showToast(result.message, 'error');
            }

        } catch (error) {
            showToast('Fehler beim Speichern.', 'error');
        }

        button.disabled = false;
        button.textContent = 'Status speichern';
    });

});
document.querySelectorAll('.ajax-coupon-toggle-form').forEach(form => {

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const button = form.querySelector('button');
        const statusInput = form.querySelector('input[name="is_active"]');

        button.disabled = true;
        button.textContent = 'Speichert...';

        try {
            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');

                statusInput.value = result.newStatus;

                button.textContent = result.newStatus == 1
                    ? 'Deaktivieren'
                    : 'Aktivieren';
            } else {
                showToast(result.message, 'error');
                button.textContent = 'Fehler';
            }

        } catch (error) {
            showToast('Fehler beim Speichern.', 'error');
        }

        button.disabled = false;
    });

});
document.querySelectorAll('.ajax-delete-coupon-form').forEach(form => {

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (!confirm('Coupon wirklich löschen?')) {
            return;
        }

        const couponCard = form.closest('.coupon-card');

        const formData = new FormData(form);

        try {

            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {

                couponCard.style.opacity = '0';

                setTimeout(() => {
                    couponCard.remove();
                }, 300);

                showToast(result.message, 'success');

            } else {

                showToast(result.message, 'error');

            }

        } catch (error) {

            showToast('Fehler beim Löschen.', 'error');

        }

    });

});
document.querySelectorAll('.ajax-delete-product-form').forEach(form => {

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (!confirm('Produkt wirklich löschen?')) {
            return;
        }

        const productCard = form.closest('.product-card');
        const formData = new FormData(form);

        try {
            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                productCard.style.opacity = '0';
                productCard.style.transform = 'translateX(40px)';

                setTimeout(() => {
                    productCard.remove();
                }, 300);

                showToast(result.message, 'success');
            } else {
                showToast(result.message, 'error');
            }

        } catch (error) {
            showToast('Fehler beim Löschen.', 'error');
        }
    });

});
document.querySelectorAll('.ajax-delete-user-form').forEach(form => {

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (!confirm('Benutzer wirklich löschen?')) {
            return;
        }

        const userCard = form.closest('.user-card');

        const formData = new FormData(form);

        try {

            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {

                userCard.style.opacity = '0';
                userCard.style.transform = 'translateX(40px)';

                setTimeout(() => {
                    userCard.remove();
                }, 300);

                showToast(result.message, 'success');

            } else {

                showToast(result.message, 'error');

            }

        } catch (error) {

            showToast('Fehler beim Löschen.', 'error');

        }

    });

});
document.querySelectorAll('.ajax-change-role-form').forEach(form => {

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const button = form.querySelector('button');

        button.disabled = true;
        button.textContent = 'Speichert...';

        try {
            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');
            } else {
                showToast(result.message, 'error');
            }

        } catch (error) {
            showToast('Fehler beim Speichern.', 'error');
        }

        button.disabled = false;
        button.textContent = 'Rolle ändern';
    });

});
document.querySelectorAll('.ajax-update-stock-form').forEach(form => {

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const button = form.querySelector('button');
        const productCard = form.closest('.product-card');
        const stockValue = productCard.querySelector('.product-stock-value');

        button.disabled = true;
        button.textContent = 'Speichert...';

        try {
            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (stockValue) {
                    stockValue.textContent = result.newStock;
                }

                showToast(result.message, 'success');
            } else {
                showToast(result.message, 'error');
            }

        } catch (error) {
            showToast('Fehler beim Speichern.', 'error');
        }

        button.disabled = false;
        button.textContent = 'Bestand speichern';
    });

});
document.querySelectorAll('.drop-upload').forEach(dropArea => {
    const fileInput = dropArea.querySelector('input[type="file"]');

    dropArea.addEventListener('click', () => {
        fileInput.click();
    });

    dropArea.addEventListener('dragover', event => {
        event.preventDefault();
        dropArea.classList.add('dragover');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('dragover');
    });

    dropArea.addEventListener('drop', event => {
        event.preventDefault();
        dropArea.classList.remove('dragover');

        if (event.dataTransfer.files.length > 0) {
            fileInput.files = event.dataTransfer.files;
            dropArea.querySelector('span').textContent =
                event.dataTransfer.files[0].name;
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            dropArea.querySelector('span').textContent =
                fileInput.files[0].name;
        }
    });
});
function setupPagination(cardSelector, paginationId, itemsPerPage = 6) {
    const cards = Array.from(document.querySelectorAll(cardSelector));
    const pagination = document.getElementById(paginationId);

    if (!pagination || cards.length === 0) return;

    let currentPage = 1;
    const totalPages = Math.ceil(cards.length / itemsPerPage);

    function renderPage(page) {
        currentPage = page;

        cards.forEach((card, index) => {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            card.style.display = index >= start && index < end ? '' : 'none';
        });

        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = i === currentPage ? 'pagination-btn active' : 'pagination-btn';

            button.addEventListener('click', () => {
                renderPage(i);
            });

            pagination.appendChild(button);
        }
    }

    renderPage(1);
}

function setupSearchPagination(inputId, itemSelector, paginationId, itemsPerPage = 5) {

    const searchInput = document.getElementById(inputId);
    const items = Array.from(document.querySelectorAll(itemSelector));
    const pagination = document.getElementById(paginationId);

    if (!searchInput || !pagination || items.length === 0) return;

    let filteredItems = [...items];

    function renderPage(page = 1) {

        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

        items.forEach(item => {
            item.style.display = 'none';
        });

        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        filteredItems.slice(start, end).forEach(item => {
            item.style.display = '';
        });

        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {

            const button = document.createElement('button');

            button.textContent = i;

            button.className =
                i === page
                    ? 'pagination-btn active'
                    : 'pagination-btn';

            button.addEventListener('click', () => {
                renderPage(i);
            });

            pagination.appendChild(button);
        }

        document.querySelectorAll('.order-user-group').forEach(group => {

            group.open = true;

            const visibleOrders =
                group.querySelectorAll('.order-card:not([style*="display: none"])');

            group.style.display =
                visibleOrders.length > 0
                    ? ''
                    : 'none';
        });
    }

    searchInput.addEventListener('input', () => {

        const searchValue =
            searchInput.value.toLowerCase();

        filteredItems = items.filter(item => {

            return item.dataset.search
                .toLowerCase()
                .includes(searchValue);
        });

        renderPage(1);
    });

    renderPage(1);
}

setupSearchPagination(
    'orderSearch',
    '.order-card',
    'ordersPagination',
    5
);
window.addEventListener('load', () => {

    document.querySelectorAll('.order-user-group').forEach(group => {
        group.open = true;
    });

});
const loadMoreSocialButton = document.getElementById('loadMoreSocial');

if (loadMoreSocialButton) {
    loadMoreSocialButton.addEventListener('click', () => {
        const socialWall = document.querySelector('.home-social-wall');

        if (!socialWall) return;

        socialWall.classList.toggle('show-more');

        loadMoreSocialButton.textContent =
            socialWall.classList.contains('show-more')
                ? 'Show Less'
                : 'Load More';
    });
}
/* ANIMATED COUNTERS */

const counters = document.querySelectorAll('.counter');

if (counters.length > 0) {
    counters.forEach(counter => {
        const target = Number(counter.dataset.target);
        let current = 0;

        const increment = Math.max(1, Math.ceil(target / 60));

        const updateCounter = () => {
            current += increment;

            if (current >= target) {
                counter.textContent = target;
            } else {
                counter.textContent = current;
                requestAnimationFrame(updateCounter);
            }
        };

        updateCounter();
    });
}

/* SIMPLE HERO IMAGE SLIDER */

const premiumImages = document.querySelectorAll('.premium-campus-grid img');

if (premiumImages.length > 0) {
    const sliderImages = [
        '/uploads/rooms/1778077889_room.jpg',
        '/uploads/rooms/1778077889_room.jpg',
        '/uploads/rooms/1778077889_room.jpg'
    ];

    let imageIndex = 0;

    setInterval(() => {
        imageIndex = (imageIndex + 1) % sliderImages.length;

        premiumImages.forEach((img, index) => {
            img.style.opacity = '0.45';

            setTimeout(() => {
                img.src = sliderImages[(imageIndex + index) % sliderImages.length];
                img.style.opacity = '1';
            }, 250);
        });

    }, 5000);
}