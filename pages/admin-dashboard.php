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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Theatre System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    .sidebar { transition: transform 0.3s ease; }
  </style>
</head>
<body class="bg-gray-900 text-white">
  <!-- Sidebar -->
  <div class="sidebar fixed h-screen w-64 bg-gray-800 text-white p-6 transform -translate-x-full md:translate-x-0 transition-all">
    <div class="text-3xl font-bold mb-8">Admin Panel</div>
    <nav class="space-y-4">
      <a href="#movies" class="block p-3 hover:bg-gray-700 rounded transition-colors">
        <i class="fas fa-film mr-2"></i>Movies
      </a>
      <a href="#analytics" class="block p-3 hover:bg-gray-700 rounded transition-colors">
        <i class="fas fa-chart-bar mr-2"></i>Analytics
      </a>
      <a href="#users" class="block p-3 hover:bg-gray-700 rounded transition-colors">
        <i class="fas fa-users mr-2"></i>Users
      </a>
      <a href="#bookings" class="block p-3 hover:bg-gray-700 rounded transition-colors">
        <i class="fas fa-ticket-alt mr-2"></i>Bookings
      </a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="ml-0 md:ml-64 p-6 min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 shadow-lg rounded-lg p-6 mb-8 animate-fade-in">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <div class="flex items-center space-x-4">
          <span class="text-gray-300">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="/api/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Logout</a>
        </div>
      </div>
    </header>

    <!-- Movies Section -->
    <section id="movies" class="mb-8 animate-fade-in">
      <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Movie Management</h2>
          <button onclick="openMovieModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Add Movie
          </button>
        </div>
        <div id="moviesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Movies will be loaded here via AJAX -->
        </div>
      </div>
    </section>

    <!-- Analytics Section -->
    <section id="analytics" class="mb-8 animate-fade-in">
      <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Theatre Analytics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="p-4 bg-gray-700 rounded-lg shadow-md">
            <canvas id="bookingsChart"></canvas>
          </div>
          <div class="p-4 bg-gray-700 rounded-lg shadow-md">
            <canvas id="revenueChart"></canvas>
          </div>
          <div class="p-4 bg-gray-700 rounded-lg shadow-md">
            <canvas id="monthlyRevenueChart"></canvas>
          </div>
          <div class="p-4 bg-gray-700 rounded-lg shadow-md">
            <canvas id="attendanceChart"></canvas>
          </div>
        </div>
      </div>
    </section>

    <!-- Users Section -->
    <section id="users" class="mb-8 animate-fade-in">
      <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">User Management</h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-700">
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
      <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Booking Management</h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-700">
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
  <div id="movieModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center">
    <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md animate-fade-in">
      <h3 class="text-2xl font-bold mb-4">Add New Movie</h3>
      <form id="movieForm" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_movie">
        <input type="hidden" name="movie_id" id="movieId" value="">
        <div>
          <label class="block text-gray-300 mb-2">Title</label>
          <input type="text" name="title" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
        </div>
        <div>
          <label class="block text-gray-300 mb-2">Description</label>
          <textarea name="description" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white"></textarea>
        </div>
        <div>
          <label class="block text-gray-300 mb-2">Duration (minutes)</label>
          <input type="number" name="duration" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
        </div>
        <div>
          <label class="block text-gray-300 mb-2">Price</label>
          <input type="number" step="0.01" name="price" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
        </div>
        <div>
          <label class="block text-gray-300 mb-2">Movie Image</label>
          <input type="file" name="image" accept="image/*" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
        </div>
        <!-- New Show Times Section -->
        <div>
          <label class="block text-gray-300 mb-2">Show Time</label>
          <div id="showTimesContainer" class="mt-4">
            <!-- Show times will be dynamically inserted here -->
          </div>
          <input type="datetime-local" name="show_time" id="showTimeInput" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeMovieModal()" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition-colors">Cancel</button>
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Global variable to store current show times (array)
    let currentShowTimes = [];

    // Tab Navigation
    document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href').substring(1);
        showSection(targetId);
      });
    });

    function showSection(sectionId) {
      document.querySelectorAll('section').forEach(section => section.classList.add('hidden'));
      const targetSection = document.getElementById(sectionId);
      if (targetSection) targetSection.classList.remove('hidden');
      document.querySelectorAll('.sidebar a').forEach(link => {
        link.classList.toggle('bg-gray-700', link.getAttribute('href') === `#${sectionId}`);
      });
      if (sectionId === 'analytics') loadAnalytics();
    }

    document.addEventListener('DOMContentLoaded', () => {
      showSection('movies');
      fetchData('movies');
      fetchData('users');
      fetchData('bookings');
    });

    // Analytics Charts with Dark Theme Options
    let bookingsChart, revenueChart, monthlyRevenueChart, attendanceChart;
    async function loadAnalytics() {
      try {
        const response = await fetch('/api/api.php?action=movie_analytics');
        const analytics = await response.json();
        if (bookingsChart) bookingsChart.destroy();
        if (revenueChart) revenueChart.destroy();
        if (monthlyRevenueChart) monthlyRevenueChart.destroy();
        if (attendanceChart) attendanceChart.destroy();

        const commonOptions = {
          plugins: {
            legend: { labels: { color: "#fff" } },
            tooltip: {
              backgroundColor: "rgba(0, 0, 0, 0.7)",
              titleColor: "#fff",
              bodyColor: "#fff"
            }
          },
          scales: {
            x: {
              ticks: { color: "#fff" },
              grid: { color: "rgba(255,255,255,0.1)" }
            },
            y: {
              ticks: { color: "#fff" },
              grid: { color: "rgba(255,255,255,0.1)" }
            }
          },
          animation: { duration: 1500, easing: "easeInOutQuart" }
        };

        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        bookingsChart = new Chart(bookingsCtx, {
          type: 'bar',
          data: {
            labels: analytics.bookings_per_movie.map(m => m.title),
            datasets: [{
              label: 'Bookings per Movie',
              data: analytics.bookings_per_movie.map(m => m.bookings),
              backgroundColor: 'rgba(59, 130, 246, 0.7)'
            }]
          },
          options: commonOptions
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(revenueCtx, {
          type: 'line',
          data: {
            labels: analytics.revenue_by_movie.map(m => m.title),
            datasets: [{
              label: 'Revenue by Movie',
              data: analytics.revenue_by_movie.map(m => m.revenue),
              borderColor: 'rgba(34, 197, 94, 0.8)',
              backgroundColor: 'rgba(34, 197, 94, 0.2)',
              fill: true,
              tension: 0.4
            }]
          },
          options: commonOptions
        });

        // Monthly Revenue Chart
        if (analytics.monthly_revenue) {
          const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
          monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
            type: 'line',
            data: {
              labels: analytics.monthly_revenue.map(item => item.month),
              datasets: [{
                label: 'Monthly Revenue',
                data: analytics.monthly_revenue.map(item => item.revenue),
                borderColor: 'rgba(255, 99, 132, 0.8)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.4
              }]
            },
            options: commonOptions
          });
        }

        // Theatre Attendance Chart
        if (analytics.attendance) {
          const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
          attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
              labels: analytics.attendance.map(item => item.month),
              datasets: [{
                label: 'Theatre Attendance',
                data: analytics.attendance.map(item => item.attendance),
                borderColor: 'rgba(54, 162, 235, 0.8)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.4
              }]
            },
            options: commonOptions
          });
        }
      } catch (error) {
        console.error('Error loading analytics:', error);
      }
    }

    // User Management Functions
    async function updateUserRole(userId, newRole) {
      try {
        const response = await fetch('/api/api.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'update_user', id: userId, role: newRole })
        });
        const result = await response.json();
        if (!result.success) throw new Error('Update failed');
        fetchData('users');
      } catch (error) {
        console.error('Error updating user:', error);
        alert('Failed to update user role');
      }
    }

    async function deleteUser(userId) {
      if (!confirm("Are you sure you want to delete this user?")) return;
      try {
        const response = await fetch('/api/api.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'delete_user', id: userId })
        });
        const result = await response.json();
        if (result.success) fetchData('users');
        alert(result.message);
      } catch (error) {
        console.error('Error deleting user:', error);
        alert('Failed to delete user');
      }
    }

    async function banUser(userId) {
      if (!confirm("Are you sure you want to ban this user?")) return;
      try {
        const response = await fetch('/api/api.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'ban_user', id: userId })
        });
        const result = await response.json();
        if (result.success) fetchData('users');
        alert(result.message);
      } catch (error) {
        console.error('Error banning user:', error);
        alert('Failed to ban user');
      }
    }

    function renderUsers(users) {
      const container = document.getElementById('usersList');
      container.innerHTML = users.map(user => `
        <tr>
          <td class="px-4 py-2">${user.username}</td>
          <td class="px-4 py-2">${user.email}</td>
          <td class="px-4 py-2">${user.role}</td>
          <td class="px-4 py-2 flex items-center space-x-2">
            <select onchange="updateUserRole(${user.id}, this.value)" class="bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white">
              <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
              <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
              <option value="banned" ${user.role === 'banned' ? 'selected' : ''}>Banned</option>
            </select>
            <button onclick="deleteUser(${user.id})" class="text-red-400 hover:text-red-600">
              <i class="fas fa-trash"></i>
            </button>
            <button onclick="banUser(${user.id})" class="text-yellow-400 hover:text-yellow-600">
              <i class="fas fa-ban"></i>
            </button>
          </td>
        </tr>
      `).join('');
    }

    function renderMovies(movies) {
      const container = document.getElementById('moviesList');
      container.innerHTML = movies.map(movie => `
        <div class="bg-gray-700 rounded-lg shadow-md p-4 hover:shadow-xl transition-shadow">
          <h3 class="text-lg font-semibold">${movie.title}</h3>
          <p class="text-gray-300 text-sm mt-2">${movie.description}</p>
          <div class="mt-4 flex justify-between items-center">
            <span class="text-blue-400">${movie.duration} mins</span>
            <div class="space-x-2">
              <button onclick='openMovieModal(${JSON.stringify(movie)})' class="text-yellow-400 hover:text-yellow-500">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="deleteMovie(${movie.id})" class="text-red-400 hover:text-red-500">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `).join('');
    }

    function renderBookings(bookings) {
      const container = document.getElementById('bookingsList');
      container.innerHTML = bookings.map(booking => `
        <tr>
          <td class="px-4 py-2">${booking.username}</td>
          <td class="px-4 py-2">${booking.title}</td>
          <td class="px-4 py-2">${booking.seats}</td>
          <td class="px-4 py-2">$${booking.total}</td>
          <td class="px-4 py-2">${booking.status}</td>
          <td class="px-4 py-2">
            <select onchange="updateBookingStatus(${booking.id}, this.value)" class="bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white">
              <option ${booking.status === 'pending' ? 'selected' : ''}>pending</option>
              <option ${booking.status === 'confirmed' ? 'selected' : ''}>confirmed</option>
              <option ${booking.status === 'cancelled' ? 'selected' : ''}>cancelled</option>
            </select>
          </td>
        </tr>
      `).join('');
    }

    async function updateBookingStatus(bookingId, newStatus) {
      try {
        const response = await fetch('/api/api.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'update_booking', id: bookingId, status: newStatus })
        });
        const result = await response.json();
        if (!result.success) throw new Error('Update failed');
        fetchData('bookings');
      } catch (error) {
        console.error('Error updating booking:', error);
        alert('Failed to update booking status');
      }
    }

    async function handleMovieSubmit(e) {
      e.preventDefault();
      const form = e.target;
      const submitButton = form.querySelector('button[type="submit"]');
      const formData = new FormData(form);
      const movieId = document.getElementById('movieId').value;
      if (movieId) formData.append('movie_id', movieId);
      submitButton.disabled = true;
      submitButton.innerHTML = 'Saving...';
      try {
        const response = await fetch('/api/api.php', { method: 'POST', body: formData });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          throw new Error('Server did not return JSON response');
        }
        const result = await response.json();
        if (result.success) {
          alert(result.message || 'Movie added successfully');
          closeMovieModal();
          fetchData('movies');
        } else {
          alert(result.message || 'Failed to add movie');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Failed to add movie: ' + error.message);
      } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Save';
      }
    }

    document.getElementById('movieForm').addEventListener('submit', handleMovieSubmit);

    async function deleteMovie(movieId) {
      if (!confirm('Are you sure you want to delete this movie?')) return;
      
      try {
          const response = await fetch('/api/api.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'delete_movie', id: movieId })
          });

          // Log full response
          const textResponse = await response.text();
          console.log('Server Response:', textResponse);

          let result;
          try {
              result = JSON.parse(textResponse); // Convert text to JSON
          } catch (jsonError) {
              console.error('Invalid JSON response:', textResponse);
              alert('Failed to delete movie: Invalid server response');
              return;
          }

          if (result.success) {
              fetchData('movies'); // Refresh movie list
              alert(result.message);
          } else {
              alert('Failed to delete movie: ' + result.message);
          }
      } catch (error) {
          console.error('Error:', error);
          alert('Failed to delete movie');
      }
    }



    async function fetchData(type) {
      try {
        const response = await fetch(`/api/api.php?action=get_${type}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        if (type === 'movies') renderMovies(data);
        if (type === 'users') renderUsers(data);
        if (type === 'bookings') renderBookings(data);
      } catch (error) {
        console.error('Error:', error);
      }
    }

    // Movie Modal & Show Times Functions
    function openMovieModal(movie = null) {
      const modal = document.getElementById('movieModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      const form = document.getElementById('movieForm');
      form.reset();
      // Reset show times
      currentShowTimes = [];
      updateShowTimesUI();
      if (movie) {
        form.querySelector('[name="action"]').value = 'update_movie';
        document.getElementById('movieId').value = movie.id;
        document.querySelector('[name="title"]').value = movie.title;
        document.querySelector('[name="description"]').value = movie.description;
        document.querySelector('[name="duration"]').value = movie.duration;
        document.querySelector('[name="price"]').value = movie.price;
        form.querySelector('[name="image"]').required = false;
        // Parse and render show times (assumes movie.show_time is stored as a JSON array)
        try {
          const times = JSON.parse(movie.show_time);
          currentShowTimes = Array.isArray(times) ? times : [];
        } catch (e) {
          console.error('Error parsing show_time:', e);
        }
        updateShowTimesUI();
      } else {
        form.querySelector('[name="action"]').value = 'add_movie';
        form.querySelector('[name="image"]').required = true;
      }
    }

    function closeMovieModal() {
      const modal = document.getElementById('movieModal');
      if (modal) {
          modal.classList.add('hidden'); // Hide the modal
      }

      // Call updateShowTimesUI only if necessary
      updateShowTimesUI();
    }

    function addShowTime() {
      const newTimeInput = document.getElementById('newShowTime');
      const timeValue = newTimeInput.value;
      if (timeValue) {
        currentShowTimes.push(timeValue);
        updateShowTimesUI();
        newTimeInput.value = '';
      }
    }

    function removeShowTime(index) {
      currentShowTimes.splice(index, 1);
      updateShowTimesUI();
    }

    function updateShowTimesUI() {
      const showTimesContainer = document.getElementById('showTimesContainer');
      if (!showTimesContainer) {
          console.error('Element with ID "showTimesContainer" not found in the DOM.');
          return;
      }
      console.log('Updating show times UI:', showTimesContainer);
      showTimesContainer.innerHTML = ''; // Clear existing content

      // Example: Dynamically add new content
      const newContent = document.createElement('div');
      newContent.textContent = 'Updated show times will appear here.';
      showTimesContainer.appendChild(newContent);
    }
  </script>
</body>
</html>
