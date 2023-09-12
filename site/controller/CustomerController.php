<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController
{
    // Hiển thị thông tin tài khoản
    public function show()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            //customer bị xóa, cho về trang chủ
            header('location: /');
            exit;
        }

        require ABSPATH_SITE . 'view/customer/show.php';
    }

    public function updateInfo()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            //customer bị xóa, cho về trang chủ
            header('location: /');
            exit;
        }

        // cập nhật dữ liệu vào object
        $customer->setName($_POST['fullname']);
        $customer->setMobile($_POST['mobile']);

        // cập nhật mật khẩu mới, nếu có
        $current_password = $_POST['current_password'];
        $password = $_POST['password'];
        if ($current_password && $password) {
            //check current password giống trong database không
            // password_verify có 2 tham số
            // tham số đầu là mật khẩu
            // tham số thứ 2 là mật khẩu đã mã hóa
            // trả về true nếu mật khẩu mã hóa chính là mật khẩu ban đầu
            if (!password_verify($current_password, $customer->getPassword())) {
                $_SESSION['error'] = 'Mật khẩu hiện tại nhập vào không đúng';
                header('location: /index?c=customer&a=show');
                exit;
            }

            // mã hóa mật khẩu  mới
            $encode_new_password = password_hash($password, PASSWORD_BCRYPT);

            $customer->setPassword($encode_new_password);
        }

        // cập nhật xuống database
        if (!$customerRepository->update($customer)) {
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /index?c=customer&a=show');
            exit;
        }
        $_SESSION['name'] = $customer->getName();
        $_SESSION['success'] = 'Đã cập nhật tài khoản thành công';
        header('location: /index?c=customer&a=show');
    }

    // Thông tin địa chỉ giao hàng mặc định
    public function shippingDefault()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            //customer bị xóa, cho về trang chủ
            header('location: /');
            exit;
        }
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/customer/shippingDefault.php';
    }

    public function updateShippingDefault()
    {

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        // Cập nhật giá trị mới vào object customer
        $customer->setShippingName($_POST['fullname']);
        $customer->setShippingMobile($_POST['mobile']);
        $customer->setWardId($_POST['ward']);
        $customer->setHousenumberStreet($_POST['address']);
        // Lưu xuống database
        if ($customerRepository->update($customer)) {
            // update session
            $_SESSION['success'] = 'Đã cập nhật địa chỉ giao hàng mặc định thành công';
            header('location: /index?c=customer&a=shippingDefault');
            exit;
        }
        $_SESSION['error'] = $customerRepository->getError();
        header('location: /index?c=customer&a=shippingDefault');
    }

    // Hiển thị danh sách đơn hàng
    public function orders()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $customer_id = $customer->getId();
        $orderRepository = new OrderRepository();
        // lấy đơn hàng của người đăng nhập
        $orders = $orderRepository->getByCustomerId($customer_id);
        require ABSPATH_SITE . 'view/customer/orders.php';
    }

    public function orderDetail()
    {
        $id = $_GET['id'];
        $orderRepository = new OrderRepository();
        $order = $orderRepository->find($id);
        require ABSPATH_SITE . 'view/customer/orderDetail.php';
    }

    // Tạo tài khoản người dùng
    public function register()
    {

        //check google recaptcha
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        $secret = GOOGLE_RECAPTCHA_SECRET;
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $resp = $recaptcha->setExpectedHostname('goda.com')
            ->verify($gRecaptchaResponse, '127.0.0.1');
        if (!$resp->isSuccess()) {
            // !Verified!
            $errors = $resp->getErrorCodes();
            // implode là nối các phần tử trong array lại thành chuỗi
            $error = implode('<br>', $errors);
            $_SESSION['error'] = 'Error: ' . $error;
            header('location:/');
            exit;
        }
        $data["name"] = $_POST['fullname'];
        $data["password"] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $data["mobile"] = $_POST['mobile'];
        $data["email"] = $_POST['email'];
        $data["login_by"] = 'form';
        $data["shipping_name"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["ward_id"] = null;
        $data["is_active"] = 0;
        $data["housenumber_street"] = '';

        $customerRepository = new CustomerRepository();
        $customerRepository->save($data);
        //Gởi mail active account đến người đăng ký tạo tài khoản
        $emailService = new EmailService();
        $to = $_POST['email'];
        $subject = 'Godashop - Verify your email';
        $payload = [
            'email' => $to,
        ];
        $token = JWT::encode($payload, JWT_KEY, 'HS256');

        $linkActive = get_domain() . '/index.php?c=customer&a=active&token=' . $token;
        $name = $data["name"];
        $website = get_domain();
        $content = "
        Dear $name,<br>
        Vui lòng click vào link bên dưới để active account<br>
        <a href='$linkActive'>Active Account</a><br>
        -----------<br>
        Được gởi từ $website
        ";

        $emailService->send($to, $subject, $content);

        $_SESSION['success'] = 'Đã đăng ký thành công. Vui lòng kích hoạt tài khoản';
        header('location:/');
    }

    public function test1()
    {
        // mã hóa
        $key = 'con vịt đang bơi';
        $payload = [
            'email' => 'nguyenvanteo@gmail.com',
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt;
    }

    public function test2()
    {
        // giải mã
        $key = 'con vịt đang bơi';
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im5ndXllbnZhbnRlb0BnbWFpbC5jb20ifQ.Itw1HDKDA2Zw4rc_bM_btIjKVacHNE1kUHk4aYIH9ac';
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'), $headers = new stdClass());
        print_r($decoded);
    }

    public function notExistingEmail()
    {
        // nếu email tồn tại trong hệ thống thì echo false;
        //ngược lại là echo true
        $email = $_GET['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (!empty($customer)) {
            echo 'false';
            return;
        }
        echo 'true';
    }

    public function active()
    {
        $token = $_GET['token'];
        $decoded = JWT::decode($token, new Key(JWT_KEY, 'HS256'));
        $email = $decoded->email;
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $customer->setIsActive(1);
        $customerRepository->update($customer);
        $_SESSION['success'] = 'Đã kích hoạt tài khoản thành công';
        header('location:/');
    }

    public function forgotPassword()
    {
        $email = $_POST['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            $_SESSION['error'] = "Email không tồn tại";
            header('location: /');
            exit;
        }

        $emailService = new EmailService();
        $name = $customer->getName();
        $to = $email;

        $payload = [
            'email' => $to,
        ];
        $token = JWT::encode($payload, JWT_KEY, 'HS256');

        $url_reset_password = get_domain() . '/index.php?c=customer&a=resetPassword&token=' . $token;

        $link_reset_password = "<a href='$url_reset_password'>Reset Password</a>";
        $subject = "Godashop: Reset password";
        $content = "
        Xin chào $name,<br>
        Vui lòng click vào link bên dưới để reset password <br>
        $link_reset_password
        ";
        if ($emailService->send($to, $subject, $content)) {
            $_SESSION['success'] = "Vui lòng check email để reset password";
            header('location: /');
            exit;
        }

        $_SESSION['error'] = $emailService->message;
        header('location: /');
    }

    public function resetPassword()
    {
        $token = $_GET['token'];
        require ABSPATH_SITE . 'view/customer/resetPassword.php';
    }

    public function updatePassword()
    {
        $token = $_POST['token'];
        $password = $_POST['password'];

        $decoded = JWT::decode($token, new Key(JWT_KEY, 'HS256'));
        $email = $decoded->email;
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $encodePassword = password_hash($password, PASSWORD_BCRYPT);
        $customer->setPassword($encodePassword);

        if ($customerRepository->update($customer)) {
            // update session
            $_SESSION['success'] = 'Đã reset password thành công';
            header('location: /');
            exit;
        }
        $_SESSION['error'] = $customerRepository->getError();
        header('location: /');
    }
}
