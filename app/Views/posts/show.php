<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
</head>
<body>
<h1>Post</h1>

<?php if (!empty($post)): ?>
    <h2>
        <a href="/post/<?php echo $post->getId(); ?>">
            <?php echo htmlspecialchars($post->getTitle()); ?>
        </a>
    </h2>
    <p><?php echo nl2br(htmlspecialchars($post->getContent())); ?></p>
    <small>Posted on: <?php echo $post->getCreatedAt(); ?></small>
    <hr>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>

</body>
</html>
