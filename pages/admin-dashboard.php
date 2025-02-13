<?php
session_start();
require '../api/db.php';

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Theatre System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .sidebar { transition: transform 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar fixed h-screen w-64 bg-gray-800 text-white p-4 transform -translate-x-64 md:translate-x-0">
        <div class="text-2xl font-bold mb-8">Admin Panel</div>
        <nav class="space-y-2">
            <a href="#movies" class="block p-2 hover:bg-gray-700 rounded transition-all"><i class="fas fa-film mr-2"></i>Movies</a>
            <a href="#analytics" class="block p-2 hover:bg-gray-700 rounded transition-all"><i class="fas fa-chart-bar mr-2"></i>Analytics</a>
            <a href="#users" class="block p-2 hover:bg-gray-700 rounded transition-all"><i class="fas fa-users mr-2"></i>Users</a>
            <a href="#bookings" class="block p-2 hover:bg-gray-700 rounded transition-all"><i class="fas fa-ticket-alt mr-2"></i>Bookings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-0 md:ml-64 p-6 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm rounded-lg p-4 mb-6 animate-fade-in">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all">Logout</a>
                </div>
            </div>
        </header>

        <!-- Movies Section -->
        <section id="movies" class="mb-8 animate-fade-in">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Movie Management</h2>
                    <button onclick="openMovieModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all">
                        <i class="fas fa-plus mr-2"></i>Add Movie
                    </button>
                </div>
                <div id="moviesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Movies will be loaded here via AJAX -->
                </div>
            </div>
        </section>

        <!-- Analytics Section -->
        <section id="analytics" class="mb-8 animate-fade-in">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">System Analytics</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Users Section -->
        <section id="users" class="mb-8 animate-fade-in">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">User Management</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">Username</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Role</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersList">
                            <!-- Users loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Bookings Section -->
        <section id="bookings" class="animate-fade-in">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Booking Management</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">User</th>
                                <th class="px-4 py-2">Movie</th>
                                <th class="px-4 py-2">Seats</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsList">
                            <!-- Bookings loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Movie Modal -->
    <div id="movieModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md animate-fade-in">
            <h3 class="text-xl font-bold mb-4">Add New Movie</h3>
            <form id="movieForm" class="space-y-4">
                <input type="hidden" id="movieId">
                <div>
                    <label class="block text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" required class="w-full p-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Description</label>
                    <textarea id="description" required class="w-full p-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Duration (minutes)</label>
                    <input type="number" id="duration" required class="w-full p-2 border rounded-lg">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeMovieModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Movie Management
        function openMovieModal(movie = null) {
            if(movie) {
                document.getElementById('movieId').value = movie.id;
                document.getElementById('title').value = movie.title;
                document.getElementById('description').value = movie.description;
                document.getElementById('duration').value = movie.duration;
            }
            document.getElementById('movieModal').classList.remove('hidden');
        }

        function closeMovieModal() {
            document.getElementById('movieModal').classList.add('hidden');
            document.getElementById('movieForm').reset();
        }

        // Load initial data
        window.addEventListener('DOMContentLoaded', () => {
            fetchData('movies');
            fetchData('users');
            fetchData('bookings');
            initializeCharts();
        });

        async function fetchData(type) {
            try {
                const response = await fetch(`/api/api.php?action=get_${type}`);
                const data = await response.json();
                
                if(type === 'movies') renderMovies(data);
                if(type === 'users') renderUsers(data);
                if(type === 'bookings') renderBookings(data);
            } catch(error) {
                console.error('Error:', error);
            }
        }

        function renderMovies(movies) {
            const container = document.getElementById('moviesList');
            container.innerHTML = movies.map(movie => `
                <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-semibold">${movie.title}</h3>
                    <p class="text-gray-600 text-sm mt-2">${movie.description}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-blue-500">${movie.duration} mins</span>
                        <div class="space-x-2">
                            <button onclick="openMovieModal(${JSON.stringify(movie)})" class="text-yellow-500 hover:text-yellow-600">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteMovie(${movie.id})" class="text-red-500 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Add similar render functions for users and bookings
    </script>
</body>
</html>