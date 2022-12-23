<?php

/**
 * @package byvex-woocommerce-starter
 */

get_header();

?>
<section class="error-404 not-found">
<div class="container">
    <div class="row">
      <div class="col-md-6 align-self-center">
		  <img src="/wp-content/uploads/2022/12/render-2.png" width="100%">
      </div>
      <div class="col-md-6 align-self-center">
        <h1>404</h1>
        <h2>UH OH! You're lost.</h2>
        <p>The page you are looking for does not exist.
          How you got here is a mystery. But you can click the button below
          to go back to the homepage.
        </p>
        <br>
		<a href="/" class="logout-btn-hd">Go to home</a>
      </div>
    </div>
  </div>	
</section>
<?php
get_footer();
