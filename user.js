document.addEventListener('DOMContentLoaded', function () {
    const modals = {
        payment: document.getElementById('paymentModal'),
        amount: document.getElementById('amountModal'),
        theater: document.getElementById('theaterModal'),
        seat: document.getElementById('seatModal')
    };

    const closeButtons = {
        payment: document.getElementById('closeModal'),
        amount: document.getElementById('closeAmountModal'),
        theater: document.getElementById('closeTheaterModal'),
        seat: document.getElementById('closeSeatModal')
    };

    const buttons = {
        payOnline: document.getElementById('payOnlineBtn'),
        payCash: document.getElementById('payCashBtn'),
        confirmAmount: document.getElementById('confirmAmountBtn'),
        confirmSeat: document.getElementById('confirmSeatBtn')
    };

    const buyTicketButtons = document.querySelectorAll('.buy-ticket-btn');
    const theaterListItems = document.querySelectorAll('#theaterList li');
    const seatElements = document.querySelectorAll('.seat');

    let selectedSeat = null;
    let selectedTheater = null;
    let selectedMovieId = null;

    window.addEventListener("click", (e) => {
        Object.values(modals).forEach(modal => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    });

    buyTicketButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            selectedMovieId = this.getAttribute('data-movie-id');
            selectedSeat = null;
            selectedTheater = null;

            seatElements.forEach(s => s.classList.remove('selected'));
            theaterListItems.forEach(t => t.classList.remove('selected'));

            modals.payment.style.display = 'flex';
        });
    });

    closeButtons.payment.onclick = () => modals.payment.style.display = 'none';
    closeButtons.amount.onclick = () => modals.amount.style.display = 'none';
    closeButtons.theater.onclick = () => modals.theater.style.display = 'none';
    closeButtons.seat.onclick = () => modals.seat.style.display = 'none';

    buttons.payOnline.onclick = () => {
        modals.payment.style.display = 'none';
        modals.amount.style.display = 'flex';
    };

    buttons.payCash.onclick = () => {
        modals.payment.style.display = 'none';
        modals.theater.style.display = 'flex';
    };

    buttons.confirmAmount.onclick = () => {
        const amount = parseFloat(document.getElementById('amountInput').value);
        if (!isNaN(amount) && amount > 0) {
            modals.amount.style.display = 'none';
            modals.theater.style.display = 'flex';
        } else {
            alert("Please enter a valid amount.");
        }
    };

    theaterListItems.forEach(item => {
        item.addEventListener('click', () => {
            theaterListItems.forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
            selectedTheater = item.textContent;
            modals.theater.style.display = 'none';
            modals.seat.style.display = 'flex';
        });
    });

    seatElements.forEach(seat => {
        seat.addEventListener('click', () => {
            seatElements.forEach(s => s.classList.remove('selected'));
            seat.classList.add('selected');
            selectedSeat = seat.textContent;
        });
    });

    buttons.confirmSeat.onclick = () => {
        if (selectedSeat && selectedMovieId && selectedTheater) {
            modals.seat.style.display = 'none';

            fetch('book_movie.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    movie_id: selectedMovieId,
                    theater: selectedTheater,
                    seat: selectedSeat,
                    payment_method: 'Cash'
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("🎟 Ticket booked successfully!");
                    } else {
                        alert("Booking failed: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while booking.");
                });

        } else {
            alert("Please select a seat.");
        }
    };
});
