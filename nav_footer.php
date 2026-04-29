<?php
declare(strict_types=1);

if (!function_exists('renderSiteHeader')) {
	function renderSiteHeader(string $pageTitle = 'UNI Events', string $activePage = 'home'): void
	{
		require_once __DIR__ . '/session_bootstrap.php';
		startAppSession();
		
		$isHome = $activePage === 'home' ? 'active' : '';
		$isEvents = $activePage === 'events' ? 'active' : '';
		$isAbout = $activePage === 'about' ? 'active' : '';
		$isContact = $activePage === 'contact' ? 'active' : '';
		
		// Check if admin is logged in
		$isAdminLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
		$authButtonLabel = $isAdminLoggedIn ? 'Dashboard' : 'Login';
		$authButtonHref = $isAdminLoggedIn ? 'admin/dashboard.php' : 'admin/login.php';
		$authButton = '<a id="navbarAuthButton" href="' . $authButtonHref . '" class="btn btn-sm btn-outline-info" data-login-label="Login" data-dashboard-label="Dashboard" data-login-href="admin/login.php" data-dashboard-href="admin/dashboard.php">' . $authButtonLabel . '</a>';

		echo <<<HTML
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{$pageTitle}</title>
	<link rel="icon" type="image/png" href="assets/ico.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top glass-nav">
	<div class="container">
		<a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
			<span class="brand-mark">UE</span>
			<span class="fw-semibold">UNI Events</span>
		</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="siteNav">
			<ul class="navbar-nav ms-auto me-lg-3 mb-2 mb-lg-0 gap-lg-1">
				<li class="nav-item"><a class="nav-link {$isHome}" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link {$isEvents}" href="events.php">Discover Events</a></li>
				<li class="nav-item"><a class="nav-link {$isAbout}" href="about.php">About Us</a></li>
				<li class="nav-item"><a class="nav-link {$isContact}" href="contact.php">Contact</a></li>
			</ul>
			{$authButton}
			<button class="btn btn-sm theme-switch ms-lg-2" id="themeToggle" type="button" aria-label="Toggle theme">Theme</button>
		</div>
	</div>
</nav>

<main class="site-main container py-4 py-lg-5">
HTML;
	}
}

if (!function_exists('renderSiteFooter')) {
	function renderSiteFooter(): void
	{
		$year = date('Y');

		echo <<<HTML
</main>

<footer class="site-footer py-4 py-lg-5 mt-5">
	<div class="container">
		<div class="row g-4">
			<div class="col-12 col-md-4">
				<p class="footer-title mb-2">UNI Events</p>
				<p class="footer-note mb-0">Find local moments worth sharing with your college community</p>
			</div>
			<div class="col-6 col-md-4">
				<p class="footer-title mb-2">Contact</p>
				<p class="footer-note mb-1">svu@svu.edu</p>
				<p class="footer-note mb-0">+963 456 456 456</p>
			</div>
			<div class="col-6 col-md-4">
				<p class="footer-title mb-2">Quick Links</p>
				<a class="footer-link d-block mb-1" href="events.php">Discover Events</a>
				<a class="footer-link d-block" href="contact.php">Contact</a>
			</div>
		</div>

		<hr class="my-4" style="border-color: var(--line);">

		<div class="d-flex flex-column flex-md-row justify-content-between gap-2">
			<small class="footer-note">Copyright {$year} City Events</small>
			<small class="footer-note">Student Project</small>
		</div>
	</div>
</footer>

<button id="scrollTopButton" class="scroll-top-btn" type="button" aria-label="Scroll to top">
	<i class="bi bi-arrow-up" aria-hidden="true"></i>
</button>

<script src="assets/js/main.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
HTML;
	}
}

