<?php
class ProductController
{
    public function index($category_id = null)
    {
        // Hiển thị danh sách sản phẩm
        $page = $_GET['page'] ?? 1;
        $item_per_page = 10;
        $conds = [];
        $sorts = [];
        // tìm kiếm theo danh mục
        if ($category_id) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category_id,
                ],
            ];
            // SELECT * FROM view_product WHERE category_id =3 LIMIT 0, 10
        }
        // tìm kiếm theo khoảng giá
        // price-range=100000-200000
        $price_range = $_GET['price-range'] ?? null;
        if ($price_range) {
            $temp = explode('-', $price_range);
            $start_price = $temp[0];
            $end_price = $temp[1];
            $conds = [
                'sale_price' => [
                    'type' => 'BETWEEN',
                    'val' => "$start_price AND $end_price",
                ],
            ];
            // SELECT * FROM view_product WHERE sale_price 100000 AND 200000 LIMIT 0, 10

            // price-range=1000000-greater
            if ($end_price == 'greater') {
                $conds = [
                    'sale_price' => [
                        'type' => '>=',
                        'val' => $start_price,
                    ],
                ];
            }
            // SELECT * FROM view_product WHERE sale_price >= 1000000 LIMIT 0, 10
        }

        // sort
        // sort=price-asc
        $sort = $_GET['sort'] ?? null;
        if ($sort) {
            $temp = explode('-', $sort);
            $mapCol = [
                'alpha' => 'name',
                'price' => 'sale_price',
                'created' => 'created_date',
            ];
            $dummy = $temp[0];
            $col = $mapCol[$dummy];
            $order = $temp[1]; //asc hay desc
            $sorts = [$col => $order];
        }

        // search=kem
        $search = $_GET['search'] ?? null;
        if ($search) {
            $conds = [
                'name' => [
                    'type' => 'LIKE',
                    'val' => "'%$search%'"
                ]
            ];
            //SELECT * FROM view_product WHERE name LIKE '%kem%'
        }

        $productRepository = new ProductRepository();
        $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        $totalProducts = $productRepository->getBy($conds, $sorts);

        $totalPage = ceil(count($totalProducts) / $item_per_page);

        // Lấy tất cả các danh mục
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        require ABSPATH_SITE . 'view/product/index.php';
    }

    function detail($id)
    {
        // Lấy tất cả các danh mục
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        $productRepository = new ProductRepository();
        $product = $productRepository->find($id);
        $category_id = $product->getCategoryId();
        $conds = [
            'category_id' => [
                'type' => '=',
                'val' => $category_id,
            ],
            'id' => [
                'type' => '!=',
                'val' => $product->getId(),
            ]
        ];
        // SELECT * FROM view_product WHERE category_id =3 AND id !=2
        $sorts = []; //không sort để mặc định
        $relatedProducts = $productRepository->getBy($conds, $sorts);
        require ABSPATH_SITE . 'view/product/detail.php';
    }

    function storeComment()
    {
        $data = [
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email'],
            'star' => $_POST['rating'],
            'description' => $_POST['description'],
            'product_id' => $_POST['product_id'],
            'created_date' => date('Y-m-d H:i:s'),
        ];
        $commentRepository = new CommentRepository();
        $commentRepository->save($data);

        $productRepository = new ProductRepository();
        $product = $productRepository->find($_POST['product_id']);

        // đỗ sanh sách comment về giao diện
        require ABSPATH_SITE . 'view/product/comments.php';
    }
}
