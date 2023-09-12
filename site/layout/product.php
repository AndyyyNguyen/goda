<div class="product-container">
    <div class="image">
        <img class="img-responsive" src="../upload/<?= $product->getFeaturedImage() ?>" alt="">
    </div>
    <div class="product-meta">
        <?php
        $slug = $slugify->slugify($product->getName());
        $linkProductDetail = $router->generate('productDetail', ['slug' => $slug, 'id' => $product->getId()])
        ?>
        <h5 class="name">
            <a class="product-name" href="<?= $linkProductDetail ?>" title="<?= $product->getName() ?>"><?= $product->getName() ?></a>
        </h5>
        <div class="product-item-price">
            <?php if ($product->getPrice() != $product->getSalePrice()) : ?>
                <span class="product-item-regular"><?= number_format($product->getPrice()) ?>₫</span>
            <?php endif ?>
            <span class="product-item-discount"><?= number_format($product->getSalePrice()) ?>₫</span>
        </div>
    </div>
    <div class="button-product-action clearfix">
        <?php if ($product->getInventoryQty() > 0) : ?>
            <div class="cart icon">
                <a class="btn btn-outline-inverse buy" product-id="<?= $product->getId() ?>" href="javascript:void(0)" title="Thêm vào giỏ">
                    Thêm vào giỏ <i class="fa fa-shopping-cart"></i>
                </a>
            </div>
        <?php endif ?>
        <div class="quickview icon">
            <a class="btn btn-outline-inverse" href="<?= $linkProductDetail ?>" title="Xem nhanh">
                Xem chi tiết <i class="fa fa-eye"></i>
            </a>
        </div>
    </div>
</div>