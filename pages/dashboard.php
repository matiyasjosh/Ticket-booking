<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php");  
    exit;
}

include 'header.php'; 
include __DIR__ . '/../components/movie-card.php';
include __DIR__ . '/../components/navbar.php'; 
?>

<main class="min-h-screen pt-36 pb-10 bg-black text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold">Hi <?php echo $_SESSION['username']?>,Welcome to Your Dashboard</h1>
            <p class="mt-2 text-white/60">Manage your movie tickets and bookings</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/10 rounded-lg">
                        <i class="ph ph-ticket text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-white/60">Active Tickets</p>
                        <p class="text-2xl font-bold" id="active-tickets-count"></p>
                    </div>
                </div>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/10 rounded-lg">
                        <i class="ph ph-clock-countdown text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-white/60">Upcoming Shows</p>
                        <p class="text-2xl font-bold" id="upcoming-movies-count"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6 mb-10">
            <h2 class="text-xl font-semibold mb-6">Recent Bookings</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-white/60">
                            <th class="pb-4">Movie</th>
                            <th class="pb-4">Date</th>
                            <th class="pb-4">Ticket</th>
                            <th class="pb-4">Unbook</th>
                        </tr>
                    </thead>
                    <tbody id="recent-bookings-body" class="divide-y divide-white/10">
                        <!-- data fetched here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6">
                <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                <div class="space-y-4">
                    <a href="/pages/movies.php" class="w-full flex items-center justify-between p-4 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                        <span class="flex items-center gap-3">
                            <i class="ph ph-ticket"></i>
                            Book New Ticket
                        </span>
                        <i class="ph ph-arrow-right"></i>
                    </a>
                    <button class="w-full flex items-center justify-between p-4 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                        <span class="flex items-center gap-3">
                            <i class="ph ph-user"></i>
                            Update Profile
                        </span>
                        <i class="ph ph-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6">
                <h2 class="text-xl font-semibold mb-4">Recommended Movies</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1925" 
                            alt="Poor Things" 
                            class="w-16 h-20 object-cover rounded-lg">
                        <div>
                            <h3 class="font-semibold">Poor Things</h3>
                            <p class="text-sm text-white/60">Drama, Comedy</p>
                            <button class="mt-2 text-sm text-white/80 hover:text-white">
                                Book Now →
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=2069" 
                            alt="Anyone But You" 
                            class="w-16 h-20 object-cover rounded-lg">
                        <div>
                            <h3 class="font-semibold">Anyone But You</h3>
                            <p class="text-sm text-white/60">Romance, Comedy</p>
                            <button class="mt-2 text-sm text-white/80 hover:text-white">
                                Book Now →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }
    document.addEventListener("DOMContentLoaded", function () {
        const userId = <?php echo $_SESSION['user_id']; ?>;    
        console.log("1 birr", userId);
        fetch(`/api/get_active_tickets.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.active_tickets !== undefined) {
                    document.getElementById("active-tickets-count").innerText = data.active_tickets;
                }
            })
            .catch(error => console.error("Error fetching active tickets:", error));


        // upcoming moive count
        fetch(`/api/get_upcoming_movies.php`)
            .then(response => response.json())
            .then(data => {
                if (data.upcoming_movies !== undefined) {
                    document.getElementById("upcoming-movies-count").innerText = data.upcoming_movies;
                }
            })
            .catch(error => console.error("Error fetching upcoming movies:", error));

            // the table part
            fetch(`/api/get_recent_bookings.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById("recent-bookings-body");
                tableBody.innerHTML = ""; // Clear previous content

                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="4" class="py-4 text-center">No recent bookings</td></tr>`;
                    return;
                }

                console.log("Wegentatna hzbtat", data);
                data.forEach(booking => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="py-4">${booking.movie_name}</td>
                        <td class="py-4">${formatDate(booking.show_date)}</td>
                        <td class="py-4">
                            <button id="openModal" class="bg-green-300 text-green-800 px-2 py-1 rounded-full hover:text-green-800 hover:bg-green-100" data-movie-id="${booking.movie_id}" data-seats="${booking.seat_numbers}">
                                Show Ticket
                            </button>
                        </td>
                        <td class="py-4">
                            <button class="bg-red-300 text-red-700 px-2 py-1 rounded-full hover:text-red-700 hover:bg-red-100" onclick="cancelBooking(${booking.booking_id  })">
                                Cancel
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error("Error fetching recent bookings:", error));
    });

    function cancelBooking(bookingId) {
        if (!confirm("Are you sure you want to cancel this booking?")) return;

        fetch(`/api/cancel_booking.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Booking canceled successfully!");
                location.reload();
            } else {
                alert("Failed to cancel booking.");
            }
        })
        .catch(error => console.error("Error canceling booking:", error));
    }
</script>

<?php include __DIR__ . '/../scripts/dashboardScript.php';?>
<?php include __DIR__ .'/../components/ticket-modal.php';?>
<?php include __DIR__ . '/../components/footer.php'; ?>