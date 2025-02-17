<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("ticketModal");
        const openModal = document.getElementById("openModal");
        const closeModal = document.getElementById("closeModal");
        const confirmButton = document.getElementById("confirmTicket");
        const continueButton = document.getElementById("continueBtn");

        const step1 = document.getElementById("step1");
        const step2 = document.getElementById("step2");

        const movieTitleSpan = document.getElementById("movieTitle");
        const showTimeSpan = document.getElementById("showTime");

        const seatGrid = document.getElementById("seatGrid");
        const ticketCountInput = document.getElementById("ticketCount");
        const selectedSeatsSpan = document.getElementById("selectedSeats");
        const ticketPriceSpan = document.getElementById("ticketPrice");

        let selectedSeats = [];
        const seatPrice = <?php echo 100?>; // Birr per ticket
        const movieId = '<?php echo $movieId; ?>'; // Dynamic movie ID from backend
        const userId = '<?php echo $_SESSION["user_id"]; ?>'; // Dynamic user ID from session
        let bookedSeats = [];

        // it gets the movie from the local storage
        function getMovieFromStorage() {
            const cachedData = localStorage.getItem('moviesData');
            if (!cachedData) return null;

            const { data } = JSON.parse(cachedData);
            return data.find(movie => movie.id === movieId);
        }

        // it formats the duration of the movie
        function formatDateTime(datetimeStr) {
            // Create a Date object from the input string
            const date = new Date(datetimeStr);

            // Options for formatting
            const options = { hour: 'numeric', minute: 'numeric', hour12: true };
            return date.toLocaleString('en-US', options);
        }

        // it fetches the booked seats from the database
        function fetchBookedSeats() {
            console.log("Fetching booked seats for movie ID:", movieId);
            fetch(`/api/get_booked_seats.php?movie_id=${movieId}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(data => {
                    console.log("API response data:", data);
                    bookedSeats = data.booked_seats || [];
                    console.log("Booked seats:", bookedSeats);
                    if (!Array.isArray(bookedSeats)) {
                        console.error("bookedSeats is not an array:", bookedSeats);
                        throw new Error('bookedSeats is not an array');
                    }
                    generateSeats();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }

        function generateSeats() {
            seatGrid.innerHTML = "";
            for (let i = 1; i <= 16; i++) {
                const seat = document.createElement("button");
                seat.textContent = `S${i}`;
                seat.classList = "p-2 border rounded text-center hover:bg-gray-200";

                if (bookedSeats.includes(`S${i}`)) {
                    seat.classList.add("bg-red-500", "text-white", "cursor-not-allowed");
                    seat.disabled = true;
                } else {
                    seat.addEventListener("click", () => toggleSeat(seat, `S${i}`));
                }
                seatGrid.appendChild(seat);
            }
        }

        function toggleSeat(seat, seatNumber) {
            if (selectedSeats.includes(seatNumber)) {
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                seat.classList.remove("bg-blue-500", "text-white");
            } else if (selectedSeats.length < ticketCountInput.value) {
                selectedSeats.push(seatNumber);
                seat.classList.add("bg-blue-500", "text-white");
            }
        }

        function openTicketModal() {
            selectedSeats = [];
            fetchBookedSeats(); // Get booked seats before opening

            
            step1.classList.remove("hidden");
            step2.classList.add("hidden");
            modal.classList.remove("hidden");
        }
        
        function proceedToStep2() {
            if (selectedSeats.length !== parseInt(ticketCountInput.value)) {
                alert("Please select the correct number of seats.");
                return;
            }
            const movie = getMovieFromStorage();
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
            } else {
                console.error(Error, "Movie data not found in localStorage, weird");
            }

            step1.classList.add("hidden");
            step2.classList.remove("hidden");
        }

        function bookTicket() {
            fetch("/api/book_ticket.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    user_id: userId,
                    movie_id: movieId,
                    seats: JSON.stringify(selectedSeats),
                    total: selectedSeats.length * seatPrice,
                })
            })
            .then(async (res) => {
                const text = await res.text();
                try {
                    return JSON.parse(text);
                } catch {
                    throw new Error(text || "Invalid JSON response");
                }
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || "Booking failed");
                }
                alert(data.message);
                modal.classList.add("hidden");
                fetchBookedSeats();
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Booking failed: " + error.message);
            });
        }

        document.body.addEventListener('click', function(event) {
            if (event.target.id === 'openModal') {
                openTicketModal();
            }
        });
        closeModal.addEventListener("click", () => modal.classList.add("hidden"));
        continueButton.addEventListener("click", proceedToStep2);
        confirmButton.addEventListener("click", bookTicket);
    });
</script>
