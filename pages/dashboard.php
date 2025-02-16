<?php 
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php?action=login");  
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
                        <p class="text-2xl font-bold"></p>
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
                        <p class="text-2xl font-bold">2</p>
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
                            <th class="pb-4">Time</th>
                            <th class="pb-4">Seats</th>
                            <th class="pb-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <tr>
                            <td class="py-4">Dune: Part Two</td>
                            <td class="py-4">Mar 15, 2024</td>
                            <td class="py-4">7:30 PM</td>
                            <td class="py-4">F12, F13</td>
                            <td class="py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-green-500/20 text-green-500">
                                    Confirmed
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4">Napoleon</td>
                            <td class="py-4">Mar 18, 2024</td>
                            <td class="py-4">6:45 PM</td>
                            <td class="py-4">D8</td>
                            <td class="py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-yellow-500/20 text-yellow-500">
                                    Pending
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6">
                <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                <div class="space-y-4">
                    <button class="w-full flex items-center justify-between p-4 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                        <span class="flex items-center gap-3">
                            <i class="ph ph-ticket"></i>
                            Book New Ticket
                        </span>
                        <i class="ph ph-arrow-right"></i>
                    </button>
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
document.addEventListener("DOMContentLoaded", function () {
    fetch('/api/get_active_tickets.php')
        .then(response => response.json())
        .then(data => {
            if (data.active_tickets !== undefined) {
                document.getElementById("active-tickets-count").innerText = data.active_tickets;
            }
        })
        .catch(error => console.error("Error fetching active tickets:", error));
});
</script>


<?php include __DIR__ . '/../components/footer.php'; ?>
