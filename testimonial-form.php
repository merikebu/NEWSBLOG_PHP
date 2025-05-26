<?php
function renderTestimonialForm() {
    if (!isLoggedIn()) return;
?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-8">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Share Your Experience</h3>
        
        <form id="testimonialForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="submit_testimonial">
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Your Name</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                           placeholder="Enter your full name">
                </div>
                
                <div class="space-y-2">
                    <label for="company" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Company (Optional)</label>
                    <input type="text" id="company" name="company" 
                           class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                           placeholder="Your company name">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Rating</label>
                <div class="flex space-x-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="<?= $i ?>" class="hidden" required>
                            <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition-colors star-input" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Your Review</label>
                <textarea id="content" name="content" rows="5" required 
                          class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                          placeholder="Share your experience with our platform..."></textarea>
            </div>
            
            <div class="space-y-2">
                <label for="image" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Your Photo (Optional)</label>
                <input type="file" id="image" name="image" accept="image/*" 
                       class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-400 transition-all duration-200 text-gray-900 dark:text-white">
            </div>
            
            <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-teal-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                Submit Review
            </button>
        </form>
    </div>
</div>
<?php
}
?>
