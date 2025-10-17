<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
</head>
<body>
<h1>Post</h1>

<?php if (!empty($post)): ?>
    <h2>
        <a href="index.php?page=post&id=<?php echo $post['id']; ?>">
            <?php echo htmlspecialchars($post['title']); ?>
        </a>
    </h2>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    <small>Posted on: <?php echo $post['created_at']; ?></small>
    <hr>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>

</body>
</html>
