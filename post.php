<?php
require_once 'config.php';
require_once 'posts.php';

$postId = $_GET['id'] ?? 0;
$post = $posts->getPostById($postId);

if (!$post || $post['status'] !== 'approved') {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $authorName = sanitize($_POST['author_name']);
    $authorEmail = sanitize($_POST['author_email']);
    $comment = sanitize($_POST['comment']);
    
    if (!empty($authorName) && !empty($comment)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, author_name, author_email, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$postId, $authorName, $authorEmail, $comment]);
            $commentMessage = "Comment added successfully!";
        } catch (PDOException $e) {
            $commentError = "Failed to add comment.";
        }
    } else {
        $commentError = "Name and comment are required.";
    }
}

// Get comments
try {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    $comments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - ModernBlog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-in': 'slideIn 0.6s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-slate-900 dark:to-indigo-950 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg shadow-xl border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            ModernBlog
                        </h1>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (isLoggedIn()): ?>
                        <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <?= htmlspecialchars($_SESSION['full_name']) ?>
                            </span>
                        </div>
                        <a href="create-post.php" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            Create Post
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Post Content -->
        <article class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden mb-8 animate-fade-in">
            <?php if ($post['image_path']): ?>
                <div class="relative h-96 overflow-hidden">
                    <img src="<?= UPLOAD_PATH . $post['image_path'] ?>" 
                         alt="<?= htmlspecialchars($post['title']) ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                </div>
            <?php endif; ?>
            
            <div class="p-8 lg:p-12">
                <header class="mb-8">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
                        <?= htmlspecialchars($post['title']) ?>
                    </h1>
                    
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">
                                    <?= strtoupper(substr($post['full_name'], 0, 1)) ?>
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($post['full_name']) ?>
                                </p>
                                <p class="text-sm">Author</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium">
                                <?= date('F j, Y \a\t g:i A', strtotime($post['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </header>
                
                <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden animate-slide-in">
            
            <!-- Comments Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Comments (<?= count($comments) ?>)
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Share your thoughts about this story</p>
                    </div>
                </div>
            </div>
            
            <!-- Add Comment Form -->
            <div class="p-8 border-b border-gray-200/50 dark:border-gray-700/50">
                <?php if (isset($commentMessage)): ?>
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl dark:bg-emerald-900/20 dark:border-emerald-500/30 dark:text-emerald-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?= htmlspecialchars($commentMessage) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($commentError)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl dark:bg-red-900/20 dark:border-red-500/30 dark:text-red-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?= htmlspecialchars($commentError) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="author_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Your Name *
                            </label>
                            <input type="text" id="author_name" name="author_name" required 
                                   class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="Enter your name">
                        </div>
                        
                        <div>
                            <label for="author_email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Email (optional)
                            </label>
                            <input type="email" id="author_email" name="author_email" 
                                   class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="your@email.com">
                        </div>
                    </div>
                    
                    <div>
                        <label for="comment" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Your Comment *
                        </label>
                        <textarea id="comment" name="comment" rows="4" required 
                                  class="w-full px-4 py-3 bg-white/50 dark:bg-gray-700/50 border border-gray-300/50 dark:border-gray-600/50 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                  placeholder="Share your thoughts about this story..."></textarea>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Your comment will be reviewed before publishing.
                        </p>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Post Comment
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Comments List -->
            <div class="p-8">
                <?php if (empty($comments)): ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Start the Conversation</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Be the first to share your thoughts about this story!
                        </p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($comments as $index => $comment): ?>
                            <div class="flex items-start space-x-4 p-6 bg-gradient-to-r from-gray-50/50 to-blue-50/50 dark:from-gray-800/50 dark:to-blue-900/20 rounded-2xl border border-gray-200/30 dark:border-gray-700/30" style="animation-delay: <?= $index * 0.1 ?>s">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg">
                                        <span class="text-white font-bold text-lg">
                                            <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($comment['author_name']) ?>
                                        </h4>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            <?= date('M j, Y \a\t g:i A', strtotime($comment['created_at'])) ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Add staggered animation to comments
        document.addEventListener('DOMContentLoaded', function() {
            const comments = document.querySelectorAll('[style*="animation-delay"]');
            comments.forEach((comment, index) => {
                comment.style.opacity = '0';
                comment.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    comment.style.opacity = '1';
                    comment.style.transform = 'translateY(0)';
                    comment.style.transition = 'all 0.5s ease-out';
                }, index * 100);
            });
        });

        // Auto-resize comment textarea
        const textarea = document.getElementById('comment');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
</body>
</html>
