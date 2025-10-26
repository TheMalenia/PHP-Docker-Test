<?php

// app/routes/routes.php

return [
    // API routes (JSON)
    ['GET',  '/api/posts',           ['App\\Presentation\\Controllers\\Api\\PostApiController', 'index']],
    ['GET',  '/api/posts/{id}',      ['App\\Presentation\\Controllers\\Api\\PostApiController', 'show']],
    ['POST', '/api/posts',           ['App\\Presentation\\Controllers\\Api\\PostApiController', 'create']],

    ['POST', '/api/register',        ['App\\Presentation\\Controllers\\Api\\UserApiController', 'register']],
    ['POST', '/api/login',           ['App\\Presentation\\Controllers\\Api\\UserApiController', 'login']],
];
