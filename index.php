<?php include 'pages/header.php'; ?>
<?php include 'components/navbar.php'; ?>
<?php include 'components/movie-card.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Ticket Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        marck: ['Marck Script', 'cursive'],
                        gloria: ['Gloria Hallelujah', 'cursive'],
                        julee: ['Julee', 'cursive'],
                        hand: ['Just Another Hand', 'cursive'],
                        vinyl: ['Rubik Vinyl', 'cursive']
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-black text-white">
    <section class="relative min-h-screen">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=80&w=2070" 
                alt="Dune Part Two" 
                class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
            <div class="max-w-2xl">
                <span class="inline-block rounded-full bg-white/10 px-4 py-1 text-sm backdrop-blur-sm">
                    Now Showing
                </span>
                <h1 class="mt-4 text-5xl font-bold tracking-tight sm:text-6xl">
                    Kuret, Book Movies Online
                </h1>
                <p class="mt-6 text-xl leading-8 text-white/80">
                    Kuret is a platform where you can book movie tickets online. We have a wide range of movies to choose from. 
                    You can book tickets for your favorite movies and enjoy the show with your friends and family.
                </p>
                <div class="mt-10">
                    <a href="/pages/movies.php" class="rounded-lg bg-white px-8 py-3 text-lg font-semibold text-black transition-all hover:scale-105 hover:bg-white/90">
                        Book Tickets
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- featured movies section -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block rounded-full bg-white/5 px-4 py-1 text-sm text-white/80 backdrop-blur-sm mb-4">
                    Coming Soon
                </span>
                <h2 class="text-4xl font-bold">Featured Movies</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $movies = [
                    [
                        "title" => "Napoleon",
                        "image" => "https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=2070",
                        "duration" => "2h 38m",
                        "date" => "March 15, 2024"
                    ],
                    [
                        "title" => "Poor Things",
                        "image" => "https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1925",
                        "duration" => "2h 21m",
                        "date" => "March 18, 2024"
                    ],
                    [
                        "title" => "Anyone But You",
                        "image" => "https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=2069",
                        "duration" => "1h 43m",
                        "date" => "March 20, 2024"
                    ],
                    [
                        "title" => "Drive-Away Dolls",
                        "image" => "https://images.unsplash.com/photo-1616530940355-351fabd9524b?q=80&w=1935",
                        "duration" => "1h 24m",
                        "date" => "March 22, 2024"
                    ]
                ];
                foreach ($movies as $movie):
                    renderMovieCard($movie);
                ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
</body>
</html>
