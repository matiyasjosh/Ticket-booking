<?php include 'header.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<?php include __DIR__ . '/../components/movie-card.php'; ?>

<main class="min-h-screen pt-24 pb-12 bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-white">Now Showing</h1>
            <p class="mt-2 text-white/60">Explore our current and upcoming movie selections</p>
        </div>
        <!-- Movies Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $movies = [
                [
                    "id" => 1,
                    "title" => "Dune: Part Two",
                    "image" => "https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=80&w=2070",
                    "duration" => "2h 46m",
                    "date" => "Now Showing",
                    "genre" => "Sci-Fi, Adventure"
                ],
                [
                    "id" => 2,
                    "title" => "Napoleon",
                    "image" => "https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=2070",
                    "duration" => "2h 38m",
                    "date" => "March 15, 2024",
                    "genre" => "Drama, History"
                ],
                [
                    "id" => 3,
                    "title" => "Poor Things",
                    "image" => "https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1925",
                    "duration" => "2h 21m",
                    "date" => "March 18, 2024",
                    "genre" => "Comedy, Drama"
                ],
                [
                    "id" => 4,
                    "title" => "Anyone But You",
                    "image" => "https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=2069",
                    "duration" => "1h 43m",
                    "date" => "March 20, 2024",
                    "genre" => "Romance, Comedy"
                ],
                [
                    "id" => 5,
                    "title" => "Drive-Away Dolls",
                    "image" => "https://images.unsplash.com/photo-1616530940355-351fabd9524b?q=80&w=1935",
                    "duration" => "1h 24m",
                    "date" => "March 22, 2024",
                    "genre" => "Comedy, Action"
                ],
                [
                    "id" => 6,
                    "title" => "Bob Marley: One Love",
                    "image" => "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=2070",
                    "duration" => "1h 47m",
                    "date" => "Now Showing",
                    "genre" => "Biography, Music"
                ],
                [
                    "id" => 7,
                    "title" => "Madame Web",
                    "image" => "https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1974",
                    "duration" => "1h 56m",
                    "date" => "Now Showing",
                    "genre" => "Action, Fantasy"
                ],
                [
                    "id" => 8,
                    "title" => "Civil War",
                    "image" => "https://images.unsplash.com/photo-1542204165-65bf26472b9b?q=80&w=2048",
                    "duration" => "2h 10m",
                    "date" => "April 5, 2024",
                    "genre" => "Action, Drama"
                ]
            ];
            foreach ($movies as $movie): ?>
                <a href="movie-details.php?id=<?php echo $movie['id']; ?>"><?php renderMovieCard($movie); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../components/footer.php'; ?>