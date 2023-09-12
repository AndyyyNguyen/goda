<?php
$message = "";
$controlClass = '';
if (!empty($_SESSION['success'])) {
    $message = $_SESSION['success'];
    // xóa phần tử trong array có key là success
    unset($_SESSION['success']);
    $controlClass = 'alert-success';
} else if (!empty($_SESSION['error'])) {
    $message = $_SESSION['error'];
    // xóa phần tử trong array có key là error
    unset($_SESSION['error']);
    $controlClass = 'alert-danger';
}
?>
<?php if ($message): ?>
<!-- .alert.alert-success -->
<div class="alert <?=$controlClass?> mt-3 text-center"><?=$message?></div>
<?php endif?>