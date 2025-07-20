<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObjects\NewsId;
use App\Domain\ValueObjects\NewsTitle;
use App\Domain\ValueObjects\NewsUrl;
use App\Domain\ValueObjects\NewsDate;

class News
{
    public function __construct(
        private readonly NewsId $_id,
        private readonly NewsUrl $_url,
        private readonly NewsTitle $_title,
        private readonly NewsDate $_date
    ) {
    }

    public NewsId $id {
        get => $this->_id;
    }

    public NewsUrl $url {
        get => $this->_url;
    }

    public NewsTitle $title {
        get => $this->_title;
    }

    public NewsDate $date {
        get => $this->_date;
    }
} 