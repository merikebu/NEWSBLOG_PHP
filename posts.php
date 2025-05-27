<?php
require_once 'config.php';

class Posts {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function createPost($title, $content, $authorId, $imagePath = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO posts (title, content, author_id, image_path, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$title, $content, $authorId, $imagePath]);
            
            return ['success' => true, 'message' => 'Post created successfully and is pending approval'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create post: ' . $e->getMessage()];
        }
    }
    
    public function getPosts($status = 'approved', $page = 1, $limit = 10, $search = '') {
    try {
        $offset = ($page - 1) * $limit;
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $params = [$status];
        $whereClause = "WHERE p.status = ?";

        if (!empty($search)) {
            $whereClause .= " AND p.title LIKE ?";
            $params[] = '%' . $search . '%';
        }

        $sql = "SELECT p.*, u.username, u.full_name 
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                $whereClause 
                ORDER BY p.created_at DESC 
                LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Optionally log error
        error_log("Error in getPosts: " . $e->getMessage());
        return [];
    }
}

    
    public function getPostById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, u.username, u.full_name 
                                        FROM posts p 
                                        JOIN users u ON p.author_id = u.id 
                                        WHERE p.id = ?");
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    public function updatePost($id, $title, $content, $imagePath = null) {
        try {
            if ($imagePath) {
                $stmt = $this->pdo->prepare("UPDATE posts SET title = ?, content = ?, image_path = ? WHERE id = ?");
                $stmt->execute([$title, $content, $imagePath, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
                $stmt->execute([$title, $content, $id]);
            }
            
            return ['success' => true, 'message' => 'Post updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update post: ' . $e->getMessage()];
        }
    }
    
    public function deletePost($id, $adminId = null) {
        try {
            // Get post details before deletion for logging
            $post = $this->getPostById($id);
            if (!$post) {
                return ['success' => false, 'message' => 'Post not found'];
            }
            
            // Delete associated image if exists
            if ($post['image_path'] && file_exists('uploads/' . $post['image_path'])) {
                unlink('uploads/' . $post['image_path']);
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            
            // Log admin action if admin is provided
            if ($adminId) {
                $this->logAdminAction($adminId, 'delete_post', 'post', $id);
            }
            
            return ['success' => true, 'message' => 'Post deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete post: ' . $e->getMessage()];
        }
    }
    
    public function approvePost($postId, $adminId) {
    try {
        $sql = "UPDATE posts SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$adminId, $postId]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'No post found or updated with ID ' . $postId];
        }

        return ['success' => true, 'message' => 'Post approved successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'General error: ' . $e->getMessage()];
    }
}

    public function rejectPost($id, $adminId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE posts SET status = 'rejected', approved_by = ?, approved_at = NOW() WHERE id = ?");
            $stmt->execute([$adminId, $id]);
            
            // Log admin action
            $this->logAdminAction($adminId, 'reject_post', 'post', $id);
            
            return ['success' => true, 'message' => 'Post rejected successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to reject post: ' . $e->getMessage()];
        }
    }
    
    public function getTimeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2629746) return floor($time/86400) . ' days ago';
        if ($time < 31556952) return floor($time/2629746) . ' months ago';
        return floor($time/31556952) . ' years ago';
    }
    
    private function logAdminAction($adminId, $action, $targetType, $targetId) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$adminId, $action, $targetType, $targetId]);
        } catch (PDOException $e) {
            error_log("Error logging admin action: " . $e->getMessage());
        }
    }
}

$posts = new Posts($pdo)
?>
