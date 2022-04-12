<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoXTemplatesGraphQl\Model;

class RequestedFilterArgsStorage
{
    protected $filters;

    protected $disabled = false;

    public function disable(): void
    {
        $this->disabled = true;
    }

    public function enable(): void
    {
        $this->disabled = false;
    }

    public function set(array $filters)
    {
        $this->filters = $filters;
    }

    public function get(): ?array
    {
        if ($this->disabled === false) {
            return $this->filters;
        }

        return [];
    }
}
