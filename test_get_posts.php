<?php
require_once 'config.php';
require_once 'Posts.php'; // or wherever your class is

$posts = new Posts($pdo);

// Change status here to test different values: 'pending', 'approved', 'rejected'
$status = 'pending';
$page = 1;
$limit = 5;
$search = ''; // Or add a keyword like 'news'

// ---- Begin Debugging Output ----
echo "<h2>TEST: Posts with status = '$status'</h2>";

$results = $posts->getPosts($status, $page, $limit, $search);

echo "<h3>Returned Posts:</h3><pre>";
print_r($results);
echo "</pre>";
?>
