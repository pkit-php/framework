<?php

namespace App\Utils;

class Paginator
{

  private int $page = 1;
  private int $limit;
  private int $total;

  public function __construct($total, $limit, $page)
  {
    $this->total = $total;
    $this->limit = $limit;
    $this->page = $page;
  }

  public function getFirstItem()
  {
    return $this->limit * ($this->page - 1);
  }

  public function getRemainingPages()
  {
    $remainingPages = $this->total / $this->limit - $this->page;
    if ((int)$remainingPages < $remainingPages) {
      $remainingPages = (int)$remainingPages + 1;
    }
    if ($remainingPages < 0) {
      $remainingPages = 0;
    }
    return $remainingPages;
  }
}
