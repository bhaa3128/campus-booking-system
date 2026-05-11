const heroSearchButton = document.getElementById('heroSearchButton');

if (heroSearchButton) {

    heroSearchButton.addEventListener('click', () => {

        const startDate = document.getElementById('startDate').value;

        const endDate = document.getElementById('endDate').value;

        const guests = document.getElementById('guestCount').value;

        const promoCode = document.getElementById('promoCode').value;

        window.location.href =
            `rooms.php?guests=${guests}&start=${startDate}&end=${endDate}&promo=${promoCode}`;
    });
}