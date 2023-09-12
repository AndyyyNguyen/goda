<!-- Paging -->
<ul class="pagination pull-right">
    <?php if ($page > 1): ?>
    <li><a href="javascript:void(0)" onclick="goToPage(event,<?=$page - 1?>)">Trước</a></li>
    <?php endif ?>
    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
    <li class="<?=$i == $page ? 'active': ''?>"><a href="javascript:void(0)"
            onclick="goToPage(event,<?=$i?>)"><?=$i?></a></li>
    <?php endfor ?>
    <?php if ($page < $totalPage) :?>
    <li><a href="javascript:void(0)" onclick="goToPage(event,<?=$page + 1?>)">Sau</a></li>
    <?php endif ?>
</ul>
<!-- End paging -->