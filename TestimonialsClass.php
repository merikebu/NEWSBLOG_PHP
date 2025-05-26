<?php
class Testimonials {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function submitTestimonial($name, $rating, $content, $company, $imagePath, $submittedBy) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO testimonials (name, rating, content, company, image_path, submitted_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $rating, $content, $company, $imagePath, $submittedBy]);
            
            return ['success' => true, 'message' => 'Testimonial submitted successfully and is pending approval'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error submitting testimonial: ' . $e->getMessage()];
        }
    }
    
    public function getTestimonials($statuses = ['approved'], $page = 1, $limit = 10) {
    if (is_string($statuses)) {
        $statuses = [$statuses];
    }

    $offset = ($page - 1) * $limit;

    // Prepare placeholders for statuses
    $inQuery = implode(',', array_fill(0, count($statuses), '?'));

    // Inject limit and offset directly (cast to int for safety)
    $limit = (int)$limit;
    $offset = (int)$offset;

    $sql = "SELECT t.*, u.full_name as submitter_name 
            FROM testimonials t 
            JOIN users u ON t.submitted_by = u.id 
            WHERE t.status IN ($inQuery) 
            ORDER BY t.created_at DESC 
            LIMIT $limit OFFSET $offset";

    $stmt = $this->pdo->prepare($sql);

    // Only bind the statuses (limit and offset are hard-coded in query)
    $stmt->execute($statuses);

    return $stmt->fetchAll();
}

    public function updateStatus($id, $status, $adminId) {
    try {
        $stmt = $this->pdo->prepare("UPDATE testimonials SET status = ?, approved_by = ?, approved_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $adminId, $id]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'No testimonial updated. Check if ID exists.'];
        }

        return ['success' => true, 'message' => 'Testimonial status updated'];
    } catch (PDOException $e) {
        // For debugging, output error message
        return ['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()];
    }
}

}
?>
