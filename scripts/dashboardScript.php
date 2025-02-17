<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("ticketModal");
    const openModal = document.getElementById("openModal")
    const closeModal = document.getElementById("closeModal");
    const confirmButton = document.getElementById("confirmTicket");

    const movieTitleSpan = document.getElementById("movieTitle");
    const showTimeSpan = document.getElementById("showTime");
    const selectedSeatsSpan = document.getElementById("selectedSeats");
    const ticketPriceSpan = document.getElementById("ticketPrice");

    const movieId = '<?php echo $movieId; ?>';
    const userId = '<?php echo $_SESSION["user_id"]; ?>';

    function formatDateTime(datetimeStr) {
        const date = new Date(datetimeStr);
        const options = { hour: 'numeric', minute: 'numeric', hour12: true };
        return date.toLocaleString('en-US', options);
    }

    function getMovieFromStorage(movieId) {
        const cachedData = localStorage.getItem('moviesData');
        if (!cachedData) return null;
        const { data } = JSON.parse(cachedData);
        return data.find(movie => movie.id === movieId);
    }

    function openTicketModal(movieId, selectedSeats) {
        const movie = getMovieFromStorage(movieId);
        if (movie) {
            const totalCost = selectedSeats.length * Number(movie.price);
            movieTitleSpan.textContent = movie.title;
            showTimeSpan.textContent = formatDateTime(movie.show_time);
            selectedSeatsSpan.textContent = selectedSeats.join(", ");
            ticketPriceSpan.textContent = `${totalCost} Birr`;
            new QRious({
                element: document.getElementById("qrCodeCanvas"),
                value: `Seats: ${selectedSeats.join(", ")}, Price: ${totalCost} Birr`,
                size: 128,
            });
            modal.classList.remove("hidden");
        } else {
            console.error("Movie data not found in localStorage");
        }

        step1.classList.add("hidden");
        step2.classList.remove("hidden");
        modal.classList.remove("hidden");
        confirmButton.classList.add("hidden");
    }

    document.body.addEventListener('click', function(event) {
        if (event.target.id === 'openModal') {
            const movieId = event.target.dataset.movieId;
            const seats = event.target.dataset.seats 
            ? event.target.dataset.seats.split(',') 
            : [];
            openTicketModal(movieId, seats);
        }
    });
    closeModal.addEventListener("click", () => modal.classList.add("hidden"));
    confirmButton.addEventListener("click", () => modal.classList.add("hidden"));
});
</script>