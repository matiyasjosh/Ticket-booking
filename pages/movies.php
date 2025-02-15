<?php include 'header.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<main class="min-h-screen pt-24 pb-12 bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-white">Now Showing</h1>
            <p class="mt-2 text-white/60">Explore our current and upcoming movie selections</p>
        </div>
        
        <!-- Movies Grid -->
        <div id="movies-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Fallback content if JavaScript is disabled -->
            <?php foreach ($movies as $movie): ?>
                <a href="movie-details.php?id=<?php echo $movie['id']; ?>"><?php renderMovieCard($movie); ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="text-center text-white py-8" style="display: none;">
            Loading movies...
        </div>
        
        <!-- Error Message -->
        <div id="error-message" class="hidden text-center text-red-500 py-8">
            Failed to load movies. Please try again later.
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moviesGrid = document.getElementById('movies-grid');
    const loading = document.getElementById('loading');
    const errorMessage = document.getElementById('error-message');
    const storageKey = 'moviesData';
    const cacheDuration = 24 * 60 * 60 * 1000; // 24 hours

    // Hide PHP-rendered content initially
    moviesGrid.style.display = 'none';
    loading.style.display = 'block';

    // Try to load cached data first
    const cachedData = localStorage.getItem(storageKey);
    if (cachedData) {
        const { timestamp, data } = JSON.parse(cachedData);
        if (Date.now() - timestamp < cacheDuration) {
            renderMovies(data);
            loading.style.display = 'none';
            moviesGrid.style.display = 'grid';
            return;
        }
    }

    // Fetch fresh data from API
    fetch('/api/api.php?action=get_movies')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Cache the new data
            localStorage.setItem(storageKey, JSON.stringify({
                timestamp: Date.now(),
                data: data
            }));
            
            // Render movies and hide loading
            renderMovies(data);
            loading.style.display = 'none';
            moviesGrid.style.display = 'grid';
        })
        .catch(error => {
            console.error('Error:', error);
            loading.style.display = 'none';
            errorMessage.classList.remove('hidden');
            moviesGrid.style.display = 'grid'; // Show PHP fallback
            
            if (!cachedData) return;
            
            // Try to use expired cache as fallback
            const { data } = JSON.parse(cachedData);
            renderMovies(data);
        });

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

    function renderMovies(movies) {
        moviesGrid.innerHTML = '';
        
        movies.forEach(movie => {
            console.log(movie.title); // Debugging line
            const movieCard = document.createElement('a');
            movieCard.href = `movie-details.php?id=${movie.id}`;
            movieCard.innerHTML = `
                <div class="group relative overflow-hidden rounded-xl bg-white/5 backdrop-blur-sm transition-all duration-300 hover:scale-[1.02] hover:bg-white/10">
                    <div class="relative aspect-[2/3] overflow-hidden">
                        <img src="${movie.image}" 
                            alt="${movie.title}" 
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    </div>
                    <div class="absolute bottom-0 w-full p-6">
                        <h3 class="mb-2 text-xl text-white font-semibold">${movie.title}</h3>
                        <div class="flex items-center gap-4 text-sm text-white/80">
                            <div class="flex items-center gap-1">
                                <i class="ph ph-clock"></i>
                                <span>${formatDuration(movie.duration)}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="ph ph-calendar text-white"></i>
                                <span>${formatDate(movie.show_date)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            moviesGrid.appendChild(movieCard);
        });
    }
});
</script>
<?php include __DIR__ . '/../components/footer.php'; ?>