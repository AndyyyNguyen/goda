<?php

class ContactController
{
    // hiển thị form liên hệ
    function form()
    {
        require ABSPATH_SITE . 'view/contact/form.php';
    }

    // Gởi mail đến chủ cửa hàng
    function sendEmail()
    {
        $emailService = new EmailService();
        $to = SHOP_OWNER;
        $subject = APP_NAME . ' - Khách hàng liên hệ';
        $email = $_POST['email'];
        $fullname = $_POST['fullname'];
        $mobile = $_POST['mobile'];
        $content = $_POST['content'];
        $website = get_domain();
        $message = "
        Xin chào chủ shop,<br>
        Dưới đây là thông tin khách hàng liên hệ: <br>
        Tên: $fullname <br>
        Email: $email <br>
        Mobile: $mobile <br>
        Nội dung: $content <br>
        =============<br>
        Được gởi từ website: $website
        ";
        $emailService->send($to, $subject, $message);
        echo 'Đã gởi mail thành công';
    }
}
