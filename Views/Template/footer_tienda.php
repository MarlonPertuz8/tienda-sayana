	<!-- Footer -->
	<footer class="bg3 p-t-75 p-b-32">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-lg-4 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Categorías
					</h4>

					<ul>
						<?php
						if (!empty($data['categorias_footer'])) {
							// Usamos array_slice para mostrar solo las primeras 4 y mantener el diseño limpio
							$cates = array_slice($data['categorias_footer'], 0, 4);
							foreach ($cates as $cat) {
						?>
								<li class="p-b-10">
									<a href="<?= base_url(); ?>/tienda/categoria/<?= $cat['idcategoria'] . '/' . $cat['ruta']; ?>" class="stext-107 cl7 hov-cl1 trans-04">
										<?= $cat['nombre']; ?>
									</a>
								</li>
							<?php
							}
						} else { ?>
							<li class="p-b-10"><span class="stext-107 cl7">Próximamente</span></li>
						<?php } ?>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-4 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Contacto
					</h4>

					<p class="stext-107 cl7 size-201">
						Cartaena 123, Bolivar, Colombia
						Tel: 123 456 789
					</p>

					<div class="p-t-27">
						<a href="https://instagram.com/sayana.col" target="_blank" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa-brands fa fa-instagram"></i>
						</a>
						<a href="https://wa.me/3023075957" target="_blank" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa-brands fa fa-whatsapp"></i>
						</a>
					</div>



				</div>

				<div class="col-sm-6 col-lg-4 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Suscríbete
					</h4>

					<form>
						<div class="wrap-input1 w-full p-b-4">
							<input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email" placeholder="email@example.com">
							<div class="focus-input1 trans-04"></div>
						</div>

						<div class="p-t-18">
							<button class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
								Subscribe
							</button>
						</div>
					</form>
				</div>
			</div>

			<div class="p-t-40">
				<p class="stext-107 cl6 txt-center">
					<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
					<?= NOMBRE_EMPRESA; ?> | <?= WEB_EMPRESA; ?> | <a href="https://colorlib.com" target="_blank">Colorlib</a>
					<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->

				</p>
			</div>
		</div>
	</footer>


	<!-- Back to top -->
	<div class="btn-back-to-top" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="zmdi zmdi-chevron-up"></i>
		</span>
	</div>

	<!--===============================================================================================-->
	<script src="<?= media() ?>/tienda/vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media() ?>/tienda/vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media() ?>/tienda/vendor/bootstrap/js/popper.js"></script>
	<script src="<?= media() ?>/tienda/vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media() ?>/tienda/vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/daterangepicker/moment.min.js"></script>
	<script src="<?= media(); ?>/tienda/vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/slick/slick.min.js"></script>
	<script src="<?= media(); ?>/tienda/js/slick-custom.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/parallax100/parallax100.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/isotope/isotope.pkgd.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/sweetalert/sweetalert.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<!--===============================================================================================-->
	<script src="<?= media(); ?>/tienda/js/main.js"></script>
	<script src="<?= media(); ?>/tienda/js/functions.js"></script>
	<script src="<?= media(); ?>/tienda/js/functions_tienda.js"></script>
	<script src="<?= media(); ?>/tienda/js/functions_carrito.js"></script>

	<?php if (!empty($data['page_functions_js'])) { ?>
		<script src="<?= media(); ?>/tienda/js/<?= $data['page_functions_js']; ?>"></script>
	<?php } ?>

	<nav class="sayana-mobile-nav d-lg-none">
        <a href="<?= base_url(); ?>">
            <i class="zmdi zmdi-home"></i>
            <span>Inicio</span>
        </a>
        <a href="<?= base_url(); ?>/tienda">
            <i class="zmdi zmdi-store"></i>
            <span>Tienda</span>
        </a>
        <a href="<?= base_url(); ?>/blogtienda/blog">
            <i class="zmdi zmdi-collection-text"></i>
            <span>Blog</span>
        </a>
        <a href="<?= base_url(); ?>/nosotros/nosotros">
            <i class="zmdi zmdi-accounts-list"></i>
            <span>Nosotros</span>
        </a>
        <a href="<?= base_url(); ?>/contacto">
            <i class="zmdi zmdi-email"></i>
            <span>Contacto</span>
        </a>
    </nav>

	<a href="https://wa.me/3023075957?text=Hola,%20me%20gustaría%20obtener%20más%20información."
		class="float-whatsapp"
		target="_blank"
		rel="noopener noreferrer">
		<i class="fa fa-whatsapp"></i>
	</a>
	</body>

	</html>