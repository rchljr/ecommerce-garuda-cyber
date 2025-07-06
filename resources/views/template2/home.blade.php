@extends('template2.layouts.template2')

@section('content')
	{{-- Letakkan semua konten spesifik halaman (semua <section>) di sini --}}
		{{-- Contoh: Section "hero" --}}
		<section id="home-section" class="hero">
			<div class="home-slider owl-carousel">
				<div class="slider-item" style="background-image: url(images/bg_1.jpg);">
					<div class="overlay"></div>
					<div class="container">
						<div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">

							<div class="col-md-12 ftco-animate text-center">
								<h1 class="mb-2">We serve Fresh Vegestables &amp; Fruits</h1>
								<h2 class="subheading mb-4">We deliver organic vegetables &amp; fruits</h2>
								<p><a href="#" class="btn btn-primary">View Details</a></p>
							</div>

						</div>
					</div>
				</div>
			</div>
		</section>

		{{-- ... dan section lainnya ... --}}
@endsection