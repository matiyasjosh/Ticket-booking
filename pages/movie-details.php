<?php
// Get movie ID from URL parameter
$movieId = isset($_GET['id']) ? $_GET['id'] : null;
// Movie data (in a real application, this would come from a database)
$movies = [
    "1" => [
        "title" => "Dune: Part Two",
        "image" => "https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=10&w=1000",
        "duration" => "2h 46m",
        "date" => "Now Showing",
        "genre" => "Sci-Fi, Adventure",
        "description" => "Continue the journey beyond imagination as Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.",
        "price" => "$15.99",
        "showtimes" => ["10:30 AM", "2:15 PM", "6:00 PM", "9:45 PM"]
    ],
    "2" => [
        "title" => "Napoleon",
        "image" => "https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=2070",
        "duration" => "2h 38m",
        "date" => "March 15, 2024",
        "genre" => "Drama, History",
        "description" => "An epic historical drama that chronicles the rise and fall of Napoleon Bonaparte, from his humble beginnings to becoming Emperor of France.",
        "price" => "$14.99",
        "showtimes" => ["11:00 AM", "3:30 PM", "7:15 PM", "10:30 PM"]
    ]
];
$movie = isset($movies[$movieId]) ? $movies[$movieId] : null;
if (!$movie) {
    header("Location: movies.php");
    exit();
}
include 'header.php';
include __DIR__ .'/../components/navbar.php';
?>
<main class="min-h-screen pt-20 pb-12 bg-black text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Movie Image -->
            <div class="relative aspect-[2/3] overflow-hidden rounded-xl">
                <img src="<?php echo $movie['image']; ?>" 
                     alt="<?php echo $movie['title']; ?>" 
                     class="absolute inset-0 h-full w-full object-cover">
            </div>
            <!-- Movie Details -->
            <div class="space-y-6">
                <div>
                    <h1 class="text-4xl font-bold mb-4"><?php echo $movie['title']; ?></h1>
                    <div class="flex flex-wrap gap-4 text-sm text-white/60">
                        <div class="flex items-center gap-1">
                            <i class="ph ph-clock"></i>
                            <span><?php echo $movie['duration']; ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="ph ph-film-slate"></i>
                            <span><?php echo $movie['genre']; ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="ph ph-calendar"></i>
                            <span><?php echo $movie['date']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="py-6 border-t border-white/10">
                    <h2 class="text-xl font-semibold mb-4">Synopsis</h2>
                    <p class="text-white/80 leading-relaxed">
                        <?php echo $movie['description']; ?>
                    </p>
                </div>
                <div class="py-6 border-t border-white/10">
                    <h2 class="text-xl font-semibold mb-4">Showtimes</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <?php foreach ($movie['showtimes'] as $time): ?>
                            <button class="px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 transition-colors">
                                <?php echo $time; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="py-6 border-t border-white/10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-sm text-white/60">Ticket Price</p>
                            <p class="text-3xl font-bold"><?php echo $movie['price']; ?></p>
                        </div>
                        <a href="dashboard.php" 
                           class="inline-flex items-center gap-2 px-8 py-4 rounded-lg bg-white text-black font-semibold hover:bg-white/90 transition-colors">
                            <i class="ph ph-ticket"></i>
                            Book Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../components/footer.php'; ?>