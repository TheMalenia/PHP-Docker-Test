<?php ?>
<!DOCTYPE html>
    <?php // session started in public/index.php
    ?>
<head>
    <title>My Blog</title>
</head>
<body>
<div style="text-align: right;">
    <?php if (isset($_SESSION['user'])): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?></span> |
        <a href="/logout">Logout</a>
    <?php else: ?>
        <a href="/login">Login</a> |
        <a href="/register">Register</a>
    <?php endif; ?>
</div>

<h1>All Posts</h1>

<?php if (!empty($posts)): ?>
        <?php foreach($posts as $post): ?>
        <h2>
            <a href="/post/<?php echo $post->getId(); ?>">
                <?php echo htmlspecialchars($post->getTitle()); ?>
            </a>
        </h2>
        <p><?php echo nl2br(htmlspecialchars($post->getContent())); ?></p>
        <small>Posted on: <?php echo $post->getCreatedAt(); ?></small>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>

<a href="/create_post">Create New Post</a>
</body>
</html>
