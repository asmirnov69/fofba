<?php

namespace FoFBA\ProtectedUploads;

use Flarum\Extend;

return [
     (new Extend\Routes('forum'))
        ->get('/protected-files/{path:.+}', 'protected.file', ProtectedFileRoute::class),
];
