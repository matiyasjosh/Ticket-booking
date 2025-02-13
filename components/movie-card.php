<?php function renderMovieCard($movie) { ?>
    <div class="group relative overflow-hidden rounded-xl bg-white/5 backdrop-blur-sm transition-all duration-300 hover:scale-[1.02] hover:bg-white/10">
        <div class="relative aspect-[2/3] overflow-hidden">
            <img src="<?php echo $movie['image']; ?>" 
                alt="<?php echo $movie['title']; ?>" 
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
        </div>
        <div class="absolute bottom-0 w-full p-6">
            <h3 class="mb-2 text-xl font-semibold"><?php echo $movie['title']; ?></h3>
            <div class="flex items-center gap-4 text-sm text-white/80">
                <div class="flex items-center gap-1">
                    <i class="ph ph-clock"></i>
                    <span><?php echo $movie['duration']; ?></span>
                </div>
                <div class="flex items-center gap-1">
                    <i class="ph ph-calendar"></i>
                    <span><?php echo $movie['date']; ?></span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
