<?php

namespace App\Services;

use App\Repositories\PortalRepository;

class PortalService
{
    public function __construct(
        protected PortalRepository $repository
    ) {
    }

    public function homepage(): array
    {
        return $this->repository->getHomepage();
    }
}