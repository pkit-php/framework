<?php

namespace Pkit\Utils;

class Paginator
{

  public readonly int
    $total,
    $limit,
    $page,
    $totalPages,
    $remainingPages,
    $firstItem;

  public function __construct(int $total, int $limit, int $page)
  {
    $this->total = $total;
    $this->limit = $limit;
    $this->page = $page;
    $this->setFirstItem();
    $this->setTotalPages();
    $this->setRemainingPages();
  }


  private function setFirstItem()
  {
    $firstItem = $this->limit * ($this->page - 1);
    if ($firstItem > $this->total)
      $firstItem = $this->total;
    $this->firstItem = $firstItem;
  }

  private function setTotalPages()
  {
    $totalPages = $this->total / $this->limit;
    $totalPages = ceil($totalPages);
    $this->totalPages = $totalPages;
  }

  private function setRemainingPages()
  {
    $remainingPages = $this->totalPages - $this->page;
    if ($remainingPages < 0)
      $remainingPages = 0;
    $this->remainingPages = $remainingPages;
  }
}
