<?php
function renderTestimonialCard($testimonial, $index) {
?>
<div class="group bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl shadow-xl hover:shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden transform hover:scale-105 transition-all duration-300 animate-slide-up" style="animation-delay: <?= $index * 0.1 ?>s">
    <div class="p-6">
        <!-- Rating -->
        <div class="flex justify-center mb-4">
            <div class="flex space-x-1">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="w-5 h-5 <?= $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                <?php endfor; ?>
            </div>
        </div>
        
        <!-- Content -->
        <p class="text-gray-600 dark:text-gray-300 mb-6 italic text-center leading-relaxed">
            "<?= htmlspecialchars($testimonial['content']) ?>"
        </p>
        
        <!-- Author -->
        <div class="flex items-center justify-center space-x-4">
            <?php if ($testimonial['image_path']): ?>
                <img src="<?= UPLOAD_PATH . $testimonial['image_path'] ?>" 
                     alt="<?= htmlspecialchars($testimonial['name']) ?>" 
                     class="w-12 h-12 rounded-full object-cover border-2 border-gradient-to-r from-emerald-500 to-teal-500">
            <?php else: ?>
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-lg">
                        <?= strtoupper(substr($testimonial['name'], 0, 1)) ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <div class="text-center">
                <p class="font-bold text-gray-900 dark:text-white">
                    <?= htmlspecialchars($testimonial['name']) ?>
                </p>
                <?php if ($testimonial['company']): ?>
                    <p class="text-sm text-emerald-600 dark:text-emerald-400">
                        <?= htmlspecialchars($testimonial['company']) ?>
                    </p>
                <?php endif; ?>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <?= date('M j, Y', strtotime($testimonial['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
