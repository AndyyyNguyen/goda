<?php require ABSPATH_SITE . 'layout/header.php' ?>
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-9">
                <ol class="breadcrumb">
                    <li><a href="/" target="_self">Trang chủ</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span>Tài khoản</span></li>
                </ol>
            </div>
            <div class="clearfix"></div>
            <?php require ABSPATH_SITE . 'view/customer/accountSidebar.php' ?>
            <div class="col-md-9 account">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="home-title">Thông tin tài khoản</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <form class="info-account" action="?c=customer&a=updateInfo" method="POST" role="form">
                            <div class="form-group">
                                <input type="text" value="<?= $customer->getName() ?>" class="form-control" name="fullname" placeholder="Họ và tên">
                            </div>
                            <div class="form-group">
                                <input type="tel" value="<?= $customer->getMobile() ?>" class="form-control" name="mobile" placeholder="Số điện thoại">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="current_password" placeholder="Mật khẩu hiện tại">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Mật khẩu mới">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Nhập lại mật khẩu mới">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php' ?>