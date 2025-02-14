<?php
session_start();
?>

<nav class="fixed top-0 w-full z-50 bg-black/50 backdrop-blur-lg">
    <div class="max-w-7xl mx-auto px-1">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="text-3xl font-bold text-white font-julee";>MovieTime</a>
            <input type="text" class="bg-transparent text-white/80 px-14 py-2 rounded-lg border border-white/20 focus:outline-none" placeholder="Search Movies">
            <div class="hidden md:flex items-center space-x-8 text-white/50">
                <a href="/pages/movies.php" class="hover:text-white/80">Movies</a>
                <a href="/pages/dashboard.php" class="hover:text-white/80">dashboard</a>
                <a href="#" class="hover:text-white/80">About</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Show "Book Now" button and user profile dropdown when logged in -->
                    <a href="#" class="px-4 py-2 rounded-lg bg-white text-black hover:bg-white/90">
                        Book Now
                    </a>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 px-4 py-2 rounded-lg text-white">
                            <i class="ph ph-user-circle text-3xl"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                        </button>
                        <div class="absolute right-0 mt-2 w-40 bg-black text-white rounded-lg shadow-lg hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 hover:bg-white/10">Profile</a>
                            <a href="logout.php" class="block px-4 py-2 hover:bg-white/10">Logout</a>
                        </div>
                    </div>
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
