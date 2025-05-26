
<?php
require_once 'config.php';
requireLogin();
require_once 'posts.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $imagePath = null;
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (in_array($fileType, $allowedTypes) && $_FILES['image']['size'] <= MAX_FILE_SIZE) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
            $uploadPath = UPLOAD_PATH . $fileName;
            
            if (!file_exists(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = $fileName;
            }
        }
    }
    
    $result = $posts->createPost($title, $content, $_SESSION['user_id'], $imagePath);
    $resultMessage = $result['message'];
    $resultType = $result['success'] ? 'success' : 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Story - ModernBlog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-in': 'slideIn 0.8s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-purple-950 dark:to-pink-950">
    
    <!-- Navigation -->
    <nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg shadow-xl border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            ModernBlog
                        </h1>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 rounded-xl">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">
                                <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
                            </span>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?= htmlspecialchars($_SESSION['full_name']) ?>
                        </span>
                    </div>
                    
                    <a href="index.php" class="px-6 py-2 bg-gradient-to-r from-gray-500 to-slate-600 text-white font-medium rounded-xl hover:from-gray-600 hover:to-slate-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Header Section -->
        <div class="text-center mb-12 animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-red-500 bg-clip-text text-transparent mb-4">
                Create Your Story
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Share your thoughts, experiences, and insights with our community of passionate readers.
            </p>
        </div>

        <!-- Form Container -->
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden animate-slide-in">
            
            <!-- Form Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-purple-500/10 via-pink-500/10 to-red-500/10 border-b border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">New Post</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Your story will be reviewed before publishing</p>
                    </div>
                </div>
            </div>
            
            <!-- Form Content -->
            <div class="p-8">
                <?php if (isset($resultMessage)): ?>
                    <div class="mb-8 p-6 rounded-2xl border-l-4 <?= $resultType === 'success' ? 'bg-emerald-50 border-emerald-500 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-400 dark:text-emerald-200' : 'bg-red-50 border-red-500 text-red-800 dark:bg-red-900/20 dark:border-red-400 dark:text-red-200' ?>">
                        <div class="flex items-center">
                            <?php if ($resultType === 'success'): ?>
                                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            <?php else: ?>
                                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            <?php endif; ?>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($resultMessage) ?></p>
                                <?php if ($resultType === 'success'): ?>
                                    <p class="text-sm mt-1">Your post is pending approval by an admin and will be visible once approved.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    
                    <!-- Title Input -->
                    <div class="space-y-3">
                        <label for="title" class="block text-lg font-semibold text-gray-900 dark:text-white">
                            Post Title *
                        </label>
                        <input type="text" id="title" name="title" required 
                               class="w-full px-4 py-4 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-lg"
                               placeholder="Write an engaging title that captures your story...">
                    </div>
                    
                    <!-- Image Upload -->
                    <div class="space-y-3">
                        <label for="image" class="block text-lg font-semibold text-gray-900 dark:text-white">
                            Featured Image (optional)
                        </label>
                        <div class="border-2 border-dashed border-purple-300/50 dark:border-purple-600/50 rounded-2xl p-8 text-center hover:border-purple-400 dark:hover:border-purple-500 transition-colors duration-200 bg-gradient-to-br from-purple-50/50 to-pink-50/50 dark:from-purple-900/20 dark:to-pink-900/20">
                            <input type="file" id="image" name="image" accept="image/*" 
                                   class="hidden" onchange="previewImage(this)">
                            <label for="image" class="cursor-pointer">
                                <div id="imagePreview" class="mb-4 hidden">
                                    <img id="previewImg" src="" alt="Preview" class="max-w-full h-64 object-cover rounded-xl mx-auto shadow-lg">
                                </div>
                                <div id="uploadPrompt">
                                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <span class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Content Textarea -->
                    <div class="space-y-3">
                        <label for="content" class="block text-lg font-semibold text-gray-900 dark:text-white">
                            Post Content *
                        </label>
                        <textarea id="content" name="content" rows="16" required 
                                  class="w-full px-4 py-4 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                  placeholder="Share your story, thoughts, or experiences here. You can use HTML tags for formatting like <strong>, <em>, <p>, etc."></textarea>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>You can use basic HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt; for formatting.</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-200/50 dark:border-gray-700/50 space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            <span>Required fields. Your post will be reviewed before publishing.</span>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="index.php" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 text-white font-semibold rounded-xl hover:from-purple-600 hover:via-pink-600 hover:to-red-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Publish Story
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('uploadPrompt').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // Auto-resize textarea
        const textarea = document.getElementById('content');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Character counter
        textarea.addEventListener('input', function() {
            const current = this.value.length;
            const counter = document.getElementById('charCounter') || (() => {
                const counter = document.createElement('div');
                counter.id = 'charCounter';
                counter.className = 'text-sm text-gray-500 dark:text-gray-400 text-right mt-2';
                this.parentElement.appendChild(counter);
                return counter;
            })();
            
            counter.textContent = `${current} characters`;
            
            if (current < 100) {
                counter.className = 'text-sm text-red-500 text-right mt-2';
            } else if (current < 500) {
                counter.className = 'text-sm text-yellow-500 text-right mt-2';
            } else {
                counter.className = 'text-sm text-green-500 text-right mt-2';
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();
            
            if (title.length < 5) {
                e.preventDefault();
                alert('Title must be at least 5 characters long.');
                return;
            }
            
            if (content.length < 50) {
                e.preventDefault();
                alert('Content must be at least 50 characters long.');
                return;
            }
        });
    </script>
</body>
</html>
