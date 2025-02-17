<?php
session_start();
?>

<nav class="fixed top-0 w-full z-50 bg-black/50 backdrop-blur-lg">
    <div class="max-w-7xl mx-auto px-1">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="text-5xl font-bold text-white font-julee";>Kuret</a>
            
            <!-- search section -->
            <div class="relative w-[400px]">
                <input type="text" id="searchInput" class="w-full bg-transparent text-white/80 px-14 py-2 rounded-lg border border-white/20 focus:outline-none" placeholder="Search Movies">

                <!-- Container for search results -->
                <div id="searchResults" class="absolute top-full left-0 right-0 w-full bg-black/80 text-white mt-2 rounded-lg shadow-lg hidden max-h-[300px] overflow-y-auto">
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-8 text-white/50">
                <a href="/pages/movies.php" class="hover:text-white/80">Movies</a>
                <a href="/pages/dashboard.php" class="hover:text-white/80">dashboard</a>
                <!-- <a href="#" class="hover:text-white/80">About</a> -->

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Show "Book Now" button and user profile dropdown when logged in -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 px-4 py-2 rounded-lg text-white">
                            <i class="ph ph-user-circle text-3xl"></i>
                            <span>
                                <?php 
                                    echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; 
                                ?>
                            </span>

                        </button>
                    </div>
                    <a href="/api/logout.php" class="px-4 py-2 rounded-lg bg-red-500 text-black hover:bg-red-500/90 text-white">
                        Log Out
                    </a>
                <?php else: ?>
                    <!-- Show "Login" and "Signup" buttons when not logged in -->
                    <a href="/pages/login.php" class="px-4 py-2 rounded-lg border border-white text-white hover:bg-white hover:text-black">
                        Login
                    </a>
                    <a href="/pages/signup.php" class="px-4 py-2 rounded-lg bg-white text-black hover:bg-white/90">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");

    searchInput.addEventListener("input", function () {
        let query = searchInput.value.trim();

        if (query.length > 0) {
            fetch(`/api/search.php?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = ""; // Clear previous results

                    if (data.length > 0) {
                        searchResults.classList.remove("hidden");
                        
                        data.forEach(movie => {
                            let movieItem = document.createElement("div");
                            movieItem.classList.add("flex", "items-center", "p-2", "hover:bg-gray-700", "cursor-pointer");

                            movieItem.innerHTML = `
                                <img src="${movie.image}" alt="${movie.title}" class="w-16 h-16 rounded-lg mr-3">
                                <span class="text-white">${movie.title}</span>
                            `;

                            movieItem.addEventListener("click", function () {
                                window.location.href = `/pages/movie-details.php?id=${movie.id}`;
                            });

                            searchResults.appendChild(movieItem);
                        });
                    } else {
                        searchResults.innerHTML = "<p class='text-center p-2'>No results found</p>";
                    }
                })
                .catch(error => console.error("Error fetching movies:", error));
        } else {
            searchResults.classList.add("hidden");
        }
    });

    // Hide search results when clicking outside
    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.classList.add("hidden");
        }
    });
});
</script>
