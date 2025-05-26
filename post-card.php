<?php
function renderPostCard($post, $index, $posts) {
?>
<article class="group bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl shadow-xl hover:shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden transform hover:scale-105 transition-all duration-300 animate-slide-up" style="animation-delay: <?= $index * 0.1 ?>s">
    <?php if ($post['image_path']): ?>
        <div class="relative overflow-hidden h-48">
            <img src="<?= UPLOAD_PATH . $post['image_path'] ?>" 
                 alt="<?= htmlspecialchars($post['title']) ?>" 
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        </div>
    <?php endif; ?>
    
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
            <a href="post.php?id=<?= $post['id'] ?>" class="hover:underline">
                <?= htmlspecialchars($post['title']) ?>
            </a>
        </h2>
        
        <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
            <?= substr(strip_tags($post['content']), 0, 150) ?>...
        </p>
        
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm">
                        <?= strtoupper(substr($post['full_name'], 0, 1)) ?>
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        <?= htmlspecialchars($post['full_name']) ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <?= $posts->getTimeAgo($post['created_at']) ?>
                    </p>
                </div>
            </div>
            
            <a href="post.php?id=<?= $post['id'] ?>" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-200">
                Read More
            </a>
        </div>
    </div>
</article>
<?php
}
?>
