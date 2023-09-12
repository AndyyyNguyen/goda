<?php
class AuthController
{
    public function login()
    {
        $email = $_POST['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (!$customer) {
            $_SESSION['error'] = 'Không tồn tại email, vui lòng nhập lại';
            //trở về trang chủ
            header('location: /');
            exit;
        }

        $password = $_POST['password'];
        // password_verify kiểm tra xem password có giống trong database không
        // nếu đúng trả về true, ngược lại trả về false
        if (!password_verify($password, $customer->getPassword())) {
            $_SESSION['error'] = 'Mật khẩu không đúng, vui lòng nhập lại';
            //trở về trang chủ
            header('location: /');
            exit;
        }

        if ($customer->getIsActive() == 0) {
            $_SESSION['error'] = 'Tài khoản chưa được kích hoạt, vui lòng vào check email để active';
            //trở về trang chủ
            header('location: /');
            exit;
        }
        // login thành công
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $customer->getName();

        // điều hướng về trang thông tin account người dùng
        header('location: /index?c=customer&a=show');
    }

    public function logout()
    {
        // hủy session
        session_destroy();
        // điều hường về trang chủ
        header('location: /');
    }

    public function loginGoogle()
    {
        try {
            $clientID = GOOGLE_CLIENT_ID;
            $clientSecret = GOOGLE_CLIENT_SECRET;
            $redirectUri = get_domain() . "/index.php?c=auth&a=loginGoogle";

            // create Client Request to access Google API
            $client = new Google_Client();
            $client->setClientId($clientID);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
            $client->addScope("email");
            $client->addScope("profile");

            if (isset($_GET['code'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

                $client->setAccessToken($token['access_token']);

                // get profile info
                $google_oauth = new Google_Service_Oauth2($client);
                $google_account_info = $google_oauth->userinfo->get();
                $email = $google_account_info->email;
                $name = $google_account_info->name;
                $this->createCustomerBySocial($email, $name, "google");
                $this->setupLoginEnv($email, $name);
                header("location: index.php");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function createCustomerBySocial($email, $name, $type)
    {
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        if (empty($customer)) {
            //create new customer
            $data = array(
                "name" => $name,
                "mobile" => "",
                "password" => "",
                "email" => $email,
                "shipping_name" => $name,
                "shipping_mobile" => "",
                "ward_id" => null,
                "housenumber_street" => null,
                "login_by" => $type,
                "is_active" => 1,
            );
            $customerRepository->save($data);
        }
    }

    public function setupLoginEnv($email, $name, $remember_me = null)
    {
        $_SESSION["email"] = $email;
        $_SESSION["name"] = $name;
    }

    // https://godashop.com/site/index.php?c=auth&a=loginFacebook
    public function loginFacebook()
    {

        $fb = new Facebook\Facebook([
            'app_id' => FACEBOOK_CLIENT_ID, // Replace {app-id} with your app id
            'app_secret' => FACEBOOK_CLIENT_SECRET,
            'default_graph_version' => 'v17.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // Logged in

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(FACEBOOK_CLIENT_ID); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
            var_dump($accessToken->getValue());
        }

        // $_SESSION['fb_access_token'] = (string) $accessToken;

        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //header('Location: https://example.com/members.php');
        try {
            // Returns a `Facebook\FacebookResponse` object
            $fields = array('id', 'name', 'email');
            $response = $fb->get('/me?fields=' . implode(',', $fields) . '', $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();
        if (empty($user["email"])) {
            $user["email"] = $user["id"] . "@gmail.com";
        }
        $email = $user['email'];
        $name = $user['name'];
        $this->createCustomerBySocial($email, $name, "facebook");
        $this->setupLoginEnv($email, $name);
        header("location: index.php");
    }
}
