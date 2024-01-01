<?php

namespace App\Controller;

use Twig\Environment;

abstract class AbstractController
{
    public function __construct(
        protected Environment $twig
    ) {
    }

    public function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
