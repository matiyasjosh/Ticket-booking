<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: /pages/login.php");
    exit();
}
// Get movie ID from URL parameter
$movieId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$movieId) {
    header("Location: movies.php");
    exit();
}

include 'header.php';
include __DIR__ .'/../components/navbar.php';
include __DIR__ .'/../scripts/script.php';
?>


<main class="min-h-screen pt-20 pb-12 bg-black text-white">
    <div id="movie-details" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading state -->
        <div id="loading" class="text-center py-12">
            Loading movie details...
        </div>
        
        <!-- Error state -->
        <div id="error" class="hidden text-center py-12 text-red-500">
            Failed to load movie details. Please try again later.
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const movieId = '<?php echo $movieId; ?>';
    console.log('Movie ID:', movieId);
    const movieDetails = document.getElementById('movie-details');
    const loading = document.getElementById('loading');
    const error = document.getElementById('error');

    function getMovieFromStorage() {
        const cachedData = localStorage.getItem('moviesData');
        if (!cachedData) return null;

        const { data } = JSON.parse(cachedData);
        return data.find(movie => movie.id === movieId);
    }

    function formatDuration(minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours}h ${mins}m`;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function formatDateTime(datetimeStr) {
        // Create a Date object from the input string
        const date = new Date(datetimeStr);

        // Options for formatting
        const options = { hour: 'numeric', minute: 'numeric', hour12: true };
        return date.toLocaleString('en-US', options);
    }

    function renderMovieDetails(movie) {
        if (!movie) {
            error.classList.remove('hidden');
            loading.style.display = 'none';
            return;
        }

        const showDate = movie.show_date === 'now_showing' ? 'Now Showing' : formatDate(movie.show_date);
        
        movieDetails.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Movie Image -->
                <div class="relative aspect-[2/3] overflow-hidden rounded-xl">
                    <img src="${movie.image}" 
                         alt="${movie.title}" 
                         class="absolute h-[200px] inset-0 h-full w-full object-cover">
                </div>
                <!-- Movie Details -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-4xl font-bold mb-4">${movie.title}</h1>
                        <div class="flex flex-wrap gap-4 text-sm text-white/60">
                            <div class="flex items-center gap-1">
                                <i class="ph ph-clock"></i>
                                <span>${formatDuration(movie.duration)}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="ph ph-film-slate"></i>
                                <span>${movie.genre}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="ph ph-calendar"></i>
                                <span>${showDate}</span>
                            </div>
                        </div>
                    </div>
                    <div class="py-6 border-t border-white/10">
                        <h2 class="text-xl font-semibold mb-4">Synopsis</h2>
                        <p class="text-white/80 leading-relaxed">
                            ${movie.description}
                        </p>
                    </div>
                    
                    <div class="py-6 border-t border-white/10">
                        <h2 class="text-xl font-semibold mb-4">Show Time</h2>
                        <div class="mb-6">
                            <button class="px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 transition-colors">
                                ${formatDateTime(movie.show_time)}
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-8">
                            <div>
                                <p class="text-sm text-white/60">Ticket Price</p>
                                <p class="text-3xl font-bold">${movie.price} Birr</p>
                            </div>
                            <button id="openModal"  
                               class="inline-flex items-center gap-2 px-8 py-4 rounded-lg bg-white text-black font-semibold hover:bg-white/90 transition-colors">
                                <i class="ph ph-ticket"></i>
                                Book Tickets
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Try to get movie from localStorage first
    const movie = getMovieFromStorage();
    console.log("movie",movie)
    
    if (movie) {
        renderMovieDetails(movie);
        loading.style.display = 'none';
    } else {
        // If not in localStorage, fetch from API
        fetch(`/api.php?action=get_movie&id=${movieId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(movie => {
                renderMovieDetails(movie);
                loading.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                error.classList.remove('hidden');
                loading.style.display = 'none';
            });
    }
});
</script>

<?php include __DIR__ . '/../components/ticket-modal.php'; ?>
<?php include __DIR__ . '/../components/footer.php'; ?>