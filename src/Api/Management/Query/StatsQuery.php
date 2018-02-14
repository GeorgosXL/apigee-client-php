<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Query;

use League\Period\Period;

/**
 * Class StatsQuery.
 */
class StatsQuery implements StatsQueryInterface
{
    const SORT_ASC = 'ASC';

    const SORT_DESC = 'DESC';

    /** @var string[] */
    private $metrics = [];

    /** @var \League\Period\Period */
    private $timeRange;

    /** @var null|string */
    private $filter;

    /** @var null|string */
    private $timeUnit;

    /** @var null|string */
    private $sortBy;

    /** @var null|string */
    private $sort;

    /** @var null|int */
    private $topK;

    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $offset;

    /** @var null|bool */
    private $realtime;

    /** @var null|int */
    private $accuracy;

    /** @var bool */
    private $tsAscending = false;

    /**
     * StatsQuery constructor.
     *
     * @param string[] $metrics
     *   Metrics to be aggregated for the report.
     * @param \League\Period\Period $timeRange
     *   The start and end time for the desired interval.
     */
    public function __construct(array $metrics, Period $timeRange)
    {
        $this->metrics = $metrics;
        $this->timeRange = $timeRange;
    }

    /**
     * @inheritdoc
     */
    public function getFilter(): ?string
    {
        return $this->filter;
    }

    /**
     * @inheritdoc
     */
    public function setFilter(?string $filter): StatsQueryInterface
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * @inheritdoc
     */
    public function setMetrics(array $metrics): StatsQueryInterface
    {
        $this->metrics = $metrics;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTimeRange(): Period
    {
        return $this->timeRange;
    }

    /**
     * @inheritdoc
     */
    public function setTimeRange(Period $timeRange): StatsQueryInterface
    {
        $this->timeRange = $timeRange;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTimeUnit(): ?string
    {
        return $this->timeUnit;
    }

    /**
     * @inheritdoc
     */
    public function setTimeUnit(?string $timeUnit): StatsQueryInterface
    {
        $this->timeUnit = $timeUnit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    /**
     * @inheritdoc
     */
    public function setSortBy(?string $sortBy): StatsQueryInterface
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @inheritdoc
     */
    public function setSort(?string $sort): StatsQueryInterface
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTopK(): ?int
    {
        return $this->topK;
    }

    /**
     * @inheritdoc
     */
    public function setTopK(?int $topK): StatsQueryInterface
    {
        $this->topK = $topK;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @inheritdoc
     */
    public function setLimit(?int $limit): StatsQueryInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @inheritdoc
     */
    public function setOffset(?int $offset): StatsQueryInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRealtime(): ?bool
    {
        return $this->realtime;
    }

    /**
     * @inheritdoc
     */
    public function setRealtime(?bool $realtime): StatsQueryInterface
    {
        $this->realtime = $realtime;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAccuracy(): ?int
    {
        return $this->accuracy;
    }

    /**
     * @inheritdoc
     */
    public function setAccuracy(?int $accuracy): StatsQueryInterface
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTsAscending(): bool
    {
        return $this->tsAscending;
    }

    /**
     * @inheritdoc
     */
    public function setTsAscending(bool $tsAscending): StatsQueryInterface
    {
        $this->tsAscending = $tsAscending;

        return $this;
    }
}
