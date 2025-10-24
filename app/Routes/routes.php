<?php
// app/routes/routes.php

return [
    // API routes (JSON)
    ['GET',  '/api/posts',           ['App\\Controllers\\Api\\PostApiController', 'index']],
    ['GET',  '/api/posts/{id}',      ['App\\Controllers\\Api\\PostApiController', 'show']],
    ['POST', '/api/posts',           ['App\\Controllers\\Api\\PostApiController', 'create']],

    ['POST', '/api/register',        ['App\\Controllers\\Api\\UserApiController', 'register']],
    ['POST', '/api/login',           ['App\\Controllers\\Api\\UserApiController', 'login']],
];
