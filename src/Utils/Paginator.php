<?php

namespace Pkit\Utils;

class Paginator
{

  public readonly int
    $total,
    $limit,
    $page,
    $remainingPages,
    $firstItem;

  public function __construct(int $total, int $limit, int $page)
  {
    $this->total = $total;
    $this->limit = $limit;
    $this->page = $page;
    $this->setFirstItem();
    $this->setRemainingPages();
  }

  private function setFirstItem()
  {
    $this->firstItem = $this->limit * ($this->page - 1);
  }

  private function setRemainingPages()
  {
    $remainingPages = $this->total / $this->limit - $this->page;
    if ((int)$remainingPages < $remainingPages)
      $remainingPages = (int)$remainingPages + 1;
    if ($remainingPages < 0)
      $remainingPages = 0;
    $this->remainingPages = $remainingPages;
  }
}
