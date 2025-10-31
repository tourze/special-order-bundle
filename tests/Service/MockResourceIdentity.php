<?php

declare(strict_types=1);

namespace Tourze\SpecialOrderBundle\Tests\Service;

use Tourze\ResourceManageBundle\Model\ResourceIdentity;

/**
 * Mock ResourceIdentity for non-Spu objects
 * @internal
 */
class MockResourceIdentity implements ResourceIdentity
{
    public function __construct(private string $id, private string $label)
    {
    }

    public function getResourceId(): string
    {
        return $this->id;
    }

    public function getResourceLabel(): string
    {
        return $this->label;
    }
}
