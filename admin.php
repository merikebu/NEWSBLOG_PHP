
<?php
require_once 'config.php';
requireAdmin();
require_once 'posts.php';
require_once 'testimonials.php';

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    
    switch ($action) {
        case 'approve_post':
            $result = $posts->approvePost($id, $_SESSION['user_id']);
            echo json_encode($result);
            exit();
            
        case 'reject_post':
            $result = $posts->rejectPost($id, $_SESSION['user_id']);
            echo json_encode($result);
            exit();
            
        case 'delete_post':
            $result = $posts->deletePost($id, $_SESSION['user_id']);
            echo json_encode($result);
            exit();
            
        case 'approve_testimonial':
            $result = $testimonials->updateStatus($id, 'approved', $_SESSION['user_id']);
            echo json_encode($result);
            exit();
            
        case 'reject_testimonial':
            $result = $testimonials->updateStatus($id, 'rejected', $_SESSION['user_id']);
            echo json_encode($result);
            exit();
    }
}

// Get pending content
$pendingPosts = $posts->getPosts('pending', 1, 50);
$pendingTestimonials = $testimonials->getTestimonials('pending', 1, 50);
$approvedPosts = $posts->getPosts('approved', 1, 50);
$totalPosts = count($posts->getPosts('approved', 1, 1000));
$totalUsers = 0;

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalUsers = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ModernBlog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-in': 'slideIn 0.8s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-indigo-950 dark:to-purple-950 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-lg shadow-xl border-b border-gray-200/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                Admin Dashboard
                            </h1>
                            <p class="text-sm text-gray-500">Content Management System</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200/50">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700">
                            Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>
                        </span>
                    </div>
                    
                    <a href="index.php" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Site
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 animate-fade-in">
            
            <!-- Pending Posts Card -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-orange-200/50 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-600 uppercase tracking-wide">Pending Posts</p>
                        <p class="text-3xl font-bold text-gray-900"><?= count($pendingPosts) ?></p>
                        <p class="text-sm text-gray-500 mt-1">Awaiting approval</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Pending Testimonials Card -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-purple-200/50 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600 uppercase tracking-wide">Pending Testimonials</p>
                        <p class="text-3xl font-bold text-gray-900"><?= count($pendingTestimonials) ?></p>
                        <p class="text-sm text-gray-500 mt-1">Awaiting review</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v8a2 2 0 002 2h6a2 2 0 002-2V8"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Total Posts Card -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-green-200/50 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Total Posts</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalPosts ?></p>
                        <p class="text-sm text-gray-500 mt-1">Published stories</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Total Users Card -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-blue-200/50 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalUsers ?></p>
                        <p class="text-sm text-gray-500 mt-1">Registered members</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-8">
            <div class="border-b border-gray-200/50">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('posts')" id="posts-tab" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                        Pending Posts (<?= count($pendingPosts) ?>)
                    </button>
                    <button onclick="showTab('testimonials')" id="testimonials-tab" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Pending Testimonials (<?= count($pendingTestimonials) ?>)
                    </button>
                    <button onclick="showTab('approved')" id="approved-tab" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Approved Posts (<?= count($approvedPosts) ?>)
                    </button>
                </nav>
            </div>
        </div>

        <!-- Pending Posts Section -->
        <div id="posts-content" class="tab-content">
            <!-- ... keep existing code (pending posts display) -->
            <?php if (!empty($pendingPosts)): ?>
                <div class="space-y-6">
                    <?php foreach ($pendingPosts as $index => $post): ?>
                        <div class="border border-gray-200/50 rounded-2xl p-6 hover:shadow-lg transition-all duration-200 bg-gradient-to-r from-white/50 to-gray-50/50">
                            <div class="flex flex-col lg:flex-row lg:items-start space-y-4 lg:space-y-0 lg:space-x-6">
                                
                                <!-- Post Content -->
                                <div class="flex-1">
                                    <div class="flex items-start space-x-4">
                                        <?php if ($post['image_path']): ?>
                                            <img src="<?= UPLOAD_PATH . $post['image_path'] ?>" 
                                                 alt="Post image" 
                                                 class="w-24 h-20 object-cover rounded-xl shadow-md flex-shrink-0">
                                        <?php endif; ?>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </h3>
                                            
                                            <p class="text-gray-600 mb-4 line-clamp-3">
                                                <?= substr(strip_tags($post['content']), 0, 200) ?>...
                                            </p>
                                            
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-medium text-xs">
                                                            <?= strtoupper(substr($post['full_name'], 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <span class="font-medium">By: <?= htmlspecialchars($post['full_name']) ?></span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Submitted: <?= $posts->getTimeAgo($post['created_at']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-row lg:flex-col space-x-3 lg:space-x-0 lg:space-y-3 flex-shrink-0">
                                    <button onclick="moderateContent(<?= $post['id'] ?>, 'approve_post')" 
                                            class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium rounded-xl hover:from-green-600 hover:to-emerald-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        <span class="flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Approve
                                        </span>
                                    </button>
                                    
                                    <button onclick="moderateContent(<?= $post['id'] ?>, 'reject_post')" 
                                            class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white font-medium rounded-xl hover:from-red-600 hover:to-rose-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        <span class="flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">All Posts Reviewed!</h3>
                    <p class="text-gray-600 max-w-md mx-auto">No pending posts to review.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Testimonials Section -->
        <div id="testimonials-content" class="tab-content hidden">
            <?php if (!empty($pendingTestimonials)): ?>
                <div class="space-y-6">
                    <?php foreach ($pendingTestimonials as $testimonial): ?>
                        <div class="border border-gray-200/50 rounded-2xl p-6 hover:shadow-lg transition-all duration-200 bg-gradient-to-r from-white/50 to-gray-50/50">
                            <div class="flex flex-col lg:flex-row lg:items-start space-y-4 lg:space-y-0 lg:space-x-6">
                                
                                <div class="flex-1">
                                    <div class="flex items-start space-x-4">
                                        <?php if ($testimonial['image_path']): ?>
                                            <img src="<?= UPLOAD_PATH . $testimonial['image_path'] ?>" 
                                                 alt="User image" 
                                                 class="w-16 h-16 object-cover rounded-full shadow-md flex-shrink-0">
                                        <?php endif; ?>
                                        
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                <?= htmlspecialchars($testimonial['name']) ?>
                                            </h3>
                                            <p class="text-sm text-purple-600 mb-3"><?= htmlspecialchars($testimonial['company']) ?></p>
                                            
                                            <p class="text-gray-600 mb-4 italic">
                                                "<?= htmlspecialchars($testimonial['content']) ?>"
                                            </p>
                                            
                                            <div class="text-sm text-gray-500">
                                                Submitted: <?= $posts->getTimeAgo($testimonial['created_at']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-3 flex-shrink-0">
                                    <button onclick="moderateContent(<?= $testimonial['id'] ?>, 'approve_testimonial')" 
                                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium rounded-xl hover:from-green-600 hover:to-emerald-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        Approve
                                    </button>
                                    
                                    <button onclick="moderateContent(<?= $testimonial['id'] ?>, 'reject_testimonial')" 
                                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white font-medium rounded-xl hover:from-red-600 hover:to-rose-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Pending Testimonials</h3>
                    <p class="text-gray-600">All testimonials have been reviewed.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Approved Posts Section -->
        <div id="approved-content" class="tab-content hidden">
            <?php if (!empty($approvedPosts)): ?>
                <div class="space-y-6">
                    <?php foreach ($approvedPosts as $post): ?>
                        <div class="border border-gray-200/50 rounded-2xl p-6 hover:shadow-lg transition-all duration-200 bg-gradient-to-r from-white/50 to-gray-50/50">
                            <div class="flex flex-col lg:flex-row lg:items-start space-y-4 lg:space-y-0 lg:space-x-6">
                                
                                <div class="flex-1">
                                    <div class="flex items-start space-x-4">
                                        <?php if ($post['image_path']): ?>
                                            <img src="<?= UPLOAD_PATH . $post['image_path'] ?>" 
                                                 alt="Post image" 
                                                 class="w-24 h-20 object-cover rounded-xl shadow-md flex-shrink-0">
                                        <?php endif; ?>
                                        
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </h3>
                                            
                                            <p class="text-gray-600 mb-4">
                                                <?= substr(strip_tags($post['content']), 0, 200) ?>...
                                            </p>
                                            
                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span>By: <?= htmlspecialchars($post['full_name']) ?></span>
                                                <span>Published: <?= $posts->getTimeAgo($post['created_at']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-3 flex-shrink-0">
                                    <a href="post.php?id=<?= $post['id'] ?>" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        View
                                    </a>
                                    
                                    <button onclick="moderateContent(<?= $post['id'] ?>, 'delete_post')" 
                                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white font-medium rounded-xl hover:from-red-600 hover:to-rose-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Approved Posts</h3>
                    <p class="text-gray-600">No posts have been approved yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active classes from all tabs
            document.querySelectorAll('[id$="-tab"]').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');
        }

        function moderateContent(id, action) {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            
            fetch('admin.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=${action}&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success animation
                    button.innerHTML = `
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    `;
                    
                    // Remove the content card with animation
                    const contentCard = button.closest('.border');
                    contentCard.style.transform = 'translateX(100%)';
                    contentCard.style.opacity = '0';
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                } else {
                    alert('Error: ' + data.message);
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }
            })
            .catch(error => {
                alert('Error processing request');
                button.disabled = false;
                button.innerHTML = originalContent;
                console.error(error);
            });
        }
    </script>
</body>
</html>
