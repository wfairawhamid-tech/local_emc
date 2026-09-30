<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
$pageCount = $pager->getPageCount();
if ($pageCount <= 1) {
    return;
}
$current      = $pager->getCurrentPageNumber();
$links        = $pager->links();
$firstLinkNum = !empty($links) ? (int) $links[0]['title'] : 1;
$lastLinkItem = !empty($links) ? end($links) : null;
$lastLinkNum  = $lastLinkItem ? (int) $lastLinkItem['title'] : $pageCount;
?>

<nav aria-label="Page navigation" class="emc-pagination-wrapper">
  <ul class="pagination">

    <?php /* ── [Previous] ────────────────────────── */ ?>
    <?php if ($pager->hasPreviousPage()): ?>
      <li class="page-item">
        <a href="<?= $pager->getPreviousPage() ?>" class="page-link" aria-label="Previous">
          <span>&laquo; Previous</span>
        </a>
      </li>
    <?php else: ?>
      <li class="page-item disabled">
        <span class="page-link">&laquo; Previous</span>
      </li>
    <?php endif; ?>

    <?php /* ── First Page + Ellipsis ─────────────── */ ?>
    <?php if ($firstLinkNum > 1): ?>
      <li class="page-item <?= ($current === 1) ? 'active' : '' ?>">
        <a href="<?= $pager->getFirst() ?>" class="page-link">1</a>
      </li>
      <?php if ($firstLinkNum > 2): ?>
        <li class="page-item disabled ellipsis">
          <span class="page-link">&hellip;</span>
        </li>
      <?php endif; ?>
    <?php endif; ?>

    <?php /* ── Numbered Pages ───────────────────── */ ?>
    <?php foreach ($links as $link): ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <?php if ($link['active']): ?>
          <span class="page-link"><?= $link['title'] ?></span>
        <?php else: ?>
          <a href="<?= $link['uri'] ?>" class="page-link"><?= $link['title'] ?></a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>

    <?php /* ── Last Page + Ellipsis ──────────────── */ ?>
    <?php if ($lastLinkNum < $pageCount): ?>
      <?php if ($lastLinkNum < $pageCount - 1): ?>
        <li class="page-item disabled ellipsis">
          <span class="page-link">&hellip;</span>
        </li>
      <?php endif; ?>
      <li class="page-item <?= ($current === $pageCount) ? 'active' : '' ?>">
        <a href="<?= $pager->getLast() ?>" class="page-link"><?= $pageCount ?></a>
      </li>
    <?php endif; ?>

    <?php /* ── [Next] ────────────────────────────── */ ?>
    <?php if ($pager->hasNextPage()): ?>
      <li class="page-item">
        <a href="<?= $pager->getNextPage() ?>" class="page-link" aria-label="Next">
          <span>Next &raquo;</span>
        </a>
      </li>
    <?php else: ?>
      <li class="page-item disabled">
        <span class="page-link">Next &raquo;</span>
      </li>
    <?php endif; ?>

  </ul>
</nav>
