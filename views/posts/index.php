<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
</head>
<body>
<div style="text-align: right;">
    <?php if (isset($_SESSION['user'])): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?></span> |
        <a href="index.php?page=logout">Logout</a>
    <?php else: ?>
        <a href="index.php?page=login">Login</a> |
        <a href="index.php?page=register">Register</a>
    <?php endif; ?>
</div>

<h1>All Posts</h1>

<?php if (!empty($posts)): ?>
    <?php foreach($posts as $post): ?>
        <h2>
            <a href="index.php?page=post&id=<?php echo $post['id']; ?>">
                <?php echo htmlspecialchars($post['title']); ?>
            </a>
        </h2>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <small>Posted on: <?php echo $post['created_at']; ?></small>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>

<a href="index.php?page=create_post">Create New Post</a>
</body>
</html>
