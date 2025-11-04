<?php
get_header();
?>

<style>
.book-archive-header {
  display: flex;
  justify-content: flex-end;
  padding: 15px 25px;
}
.book-archive-header a {
  background: #ff9800;
  color: #fff !important;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
}
.book-archive-header a:hover {
  background: #e68900;
}

.book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 25px;
  margin: 40px auto;
  max-width: 1100px;
  padding: 0 20px;
}

.book-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  padding: 20px;
  text-align: center;
  transition: transform 0.2s ease;
}
.book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.book-icon {
  font-size: 60px;
  color: #0073aa;
  margin-bottom: 10px;
}

.book-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
}

.book-meta {
  font-size: 15px;
  margin: 6px 0;
}

.book-buttons {
  margin-top: 15px;
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

<div class="book-archive-header">
  <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
    🛒 Cart (<?php echo WC()->cart->get_cart_contents_count(); ?>)
  </a>
</div>

<div class="book-grid">
<?php
if ( have_posts() ) :
  while ( have_posts() ) : the_post();
    $author = get_field('author');
    $isbn   = get_field('isbn');
    $price  = get_field('price');
?>
  <div class="book-card">
      <div class="book-icon">📚</div>
      <h3 class="book-title"><?php the_title(); ?></h3>
      <p class="book-meta"><strong>Author:</strong> <?php echo esc_html($author); ?></p>
      <p class="book-meta"><strong>Price:</strong> $<?php echo number_format((float)$price, 2); ?></p>

      <div class="book-buttons">
          <a href="<?php the_permalink(); ?>">👁️ View</a>
      </div>
  </div>
<?php
  endwhile;
else :
  echo '<p>No books available at the moment.</p>';
endif;
?>
</div>

<?php get_footer(); ?>