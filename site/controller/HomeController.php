<?php
class HomeController
{
    function index()
    {
        $page = 1;
        $item_per_page = 4;
        $conds = [];
        $productRepository = new ProductRepository();
        // Lấy 4 sản phẩm nổi bật
        $sorts = ['featured' => 'DESC'];
        $featuredProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        // SELECT * FROM view_product ORDER BY featured DESC LIMIT 0, 4;

        // Lấy 4 sản phẩm mới nhất
        $sorts = ['created_date' => 'DESC'];
        $latestProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        // SELECT * FROM view_product ORDER BY created_date DESC LIMIT 0, 4;

        // Lấy sản phẩm theo danh mục
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();
        //biến này dùng lưu sản phẩm theo danh mục
        $categoriedProducts = [];
        // lấy sản phẩm theo từng danh mục
        foreach ($categories as $category) {
            // sản phẩm cùng category_id
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category->getId()
                ]
            ];
            // SELECT * FROM view_product WHERE category_id =3 ORDER BY created_date DESC LIMIT 0, 4 
            $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
            $categoriedProducts[] = [
                'category_name' => $category->getName(),
                'products' => $products
            ];
        }

        require ABSPATH_SITE . 'view/home/index.php';
    }
}
