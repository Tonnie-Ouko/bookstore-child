<?php
get_header();

?>

<style>
.book-page-header {
  display: flex;
  justify-content: flex-end;
  padding: 15px 25px;
}
.book-page-header a {
  background: #ff9800;
  color: #fff !important;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
}
.book-page-header a:hover {
  background: #e68900;
}

.book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin: 40px auto;
  max-width: 1000px;
  padding: 0 20px;
}

.book-card {
  background: #fff;
  border-radius: 10px;
  border: 1px solid #ddd;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  padding: 25px;
  text-align: center;
  transition: transform 0.2s ease;
}
.book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.12);
}

.book-icon {
  font-size: 60px;
  color: #0073aa;
  margin-bottom: 15px;
}

.book-meta {
  font-size: 15px;
  margin: 6px 0;
}

.book-buttons {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
}

.book-buttons a,
.book-buttons button {
  background: #0073aa;
  color: #fff !important;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s ease;
}
.book-buttons a:hover,
.book-buttons button:hover {
  background: #005f8e;
}

@media (max-width: 600px) {
  .book-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="book-page-header">
  <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
    🛒 Cart (<?php echo WC()->cart->get_cart_contents_count(); ?>)
  </a>
</div>

<div class="book-grid">

<?php
while ( have_posts() ) : the_post();
    $author = get_field('author');
    $isbn   = get_field('isbn');
    $price  = get_field('price');
    $linked_product_id = get_post_meta(get_the_ID(), '_linked_product_id', true);
?>

  <div class="book-card">
      <div class="book-icon">📖</div>
      <h2 class="book-title"><?php the_title(); ?></h2>
      <p class="book-meta"><strong>Author:</strong> <?php echo esc_html($author); ?></p>
      <p class="book-meta"><strong>ISBN:</strong> <?php echo esc_html($isbn); ?></p>
      <p class="book-meta"><strong>Price:</strong> $<?php echo number_format((float)$price, 2); ?></p>

      <div class="book-buttons">
          <a href="<?php echo esc_url( get_post_type_archive_link('book') ); ?>">👁️ View All</a>

          <?php if ( $linked_product_id ) : ?>
              <a href="<?php echo esc_url( '?add-to-cart=' . $linked_product_id ); ?>">➕ Add to Cart</a>
          <?php else : ?>
              <span style="color:red;">⚠️ Product not linked (check ISBN)</span>
          <?php endif; ?>

          <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">💳 Checkout</a>
      </div>

      <!-- Keep Buy with Stripe intact -->
      <?php if ( $linked_product_id ) : ?>
          <form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" style="margin-top:1em;">
              <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($linked_product_id); ?>">
              <button type="submit" class="button alt">💠 Buy with Stripe</button>
          </form>
      <?php endif; ?>
  </div>

<?php endwhile; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const stripeButtons = document.querySelectorAll('form[action*="cart"] .button.alt');
  stripeButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Slight delay to allow WooCommerce to add to cart
      setTimeout(() => {
        window.location.href = "<?php echo esc_url( wc_get_cart_url() ); ?>";
      }, 800);
    });
  });
});
</script>


<?php get_footer(); ?>