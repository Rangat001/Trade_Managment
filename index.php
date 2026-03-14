<?php
session_start();

// If a user session exists, send them to the dealer dashboard
if (isset($_SESSION['rgt_logedin_user_id']) && trim($_SESSION['rgt_logedin_user_id']) !== '') {
	header('Location: dealer/dashboard.php');
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Dealer Pro | Goods Trading Management System</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnW5DqZuxMxDPZWS9Vyhi3F7S3w7Dnk3a1JpN96CB2TF+qsSVqS+8CA0nVddOZXS6SmttuPAHyBs+K6TfGsULA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<style>
		:root {
			--primary: #4f46e5;
			--secondary: #6366f1;
			--accent: #0ea5e9;
			--text: #0f172a;
			--muted: #475569;
			--bg: #f8fafc;
		}
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: 'Inter', system-ui, -apple-system, sans-serif;
			background: var(--bg);
			color: var(--text);
			line-height: 1.6;
		}
		a { text-decoration: none; }
		.page {
			min-height: 100vh;
			display: flex;
			flex-direction: column;
		}
		header {
			padding: 20px 32px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			position: sticky;
			top: 0;
			background: rgba(248, 250, 252, 0.9);
			backdrop-filter: blur(12px);
			border-bottom: 1px solid #e2e8f0;
			z-index: 10;
		}
		.brand {
			display: flex;
			align-items: center;
			gap: 12px;
			font-weight: 700;
			font-size: 20px;
			color: var(--text);
		}
		.badge {
			background: linear-gradient(135deg, var(--primary), var(--secondary));
			color: #fff;
			border-radius: 999px;
			padding: 8px 14px;
			font-size: 13px;
			display: inline-flex;
			align-items: center;
			gap: 8px;
		}
		.cta {
			display: inline-flex;
			align-items: center;
			gap: 10px;
			padding: 12px 18px;
			border-radius: 12px;
			border: none;
			font-weight: 600;
			cursor: pointer;
			transition: transform 0.15s ease, box-shadow 0.15s ease;
		}
		.cta.primary {
			background: linear-gradient(135deg, var(--primary), var(--secondary));
			color: #fff;
			box-shadow: 0 10px 30px rgba(79, 70, 229, 0.25);
		}
		.cta.secondary {
			background: #e2e8f0;
			color: var(--text);
		}
		.cta:hover { transform: translateY(-2px); }
		.hero {
			padding: 64px 32px 32px;
			max-width: 1200px;
			margin: 0 auto;
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
			gap: 32px;
			align-items: center;
		}
		.hero-card {
			background: #fff;
			border: 1px solid #e2e8f0;
			border-radius: 24px;
			padding: 28px;
			box-shadow: 0 15px 45px rgba(15, 23, 42, 0.06);
		}
		h1 { font-size: 36px; margin: 12px 0 16px; }
		.lead { color: var(--muted); font-size: 17px; margin-bottom: 20px; }
		.pill-list { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0; }
		.pill { background: #eef2ff; color: #4338ca; padding: 8px 12px; border-radius: 10px; font-weight: 600; font-size: 13px; }
		.section { padding: 40px 32px; max-width: 1200px; margin: 0 auto; }
		.section h2 { margin-bottom: 12px; font-size: 26px; }
		.section p { color: var(--muted); margin-bottom: 20px; }
		.grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
		.card {
			background: #fff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 18px;
			box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
		}
		.card h3 { margin: 0 0 8px; font-size: 17px; }
		.card p { margin: 0; color: var(--muted); font-size: 14px; }
		.icon {
			width: 36px; height: 36px;
			border-radius: 10px;
			display: grid; place-items: center;
			color: #fff;
			margin-bottom: 10px;
			background: linear-gradient(135deg, var(--primary), var(--accent));
		}
		.cta-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 18px; }
		footer {
			padding: 28px 32px;
			text-align: center;
			color: var(--muted);
			font-size: 14px;
		}
		@media (max-width: 640px) {
			header { padding: 16px 20px; }
			.hero { padding: 48px 20px 24px; }
			.section { padding: 32px 20px; }
			h1 { font-size: 30px; }
		}
	</style>
</head>
<body>
	<div class="page">
		<header>
			<div class="brand">
				<span style="display:inline-grid;place-items:center;width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;font-weight:700;">DP</span>
				Dealer Pro
			</div>
			<div class="cta-row">
				<a class="cta secondary" href="auth/sign-in.php">Sign In</a>
				<a class="cta primary" href="auth/sign-up.php">Start Free</a>
			</div>
		</header>

		<section class="hero">
			<div class="hero-card">
				<span class="badge"><i class="fa-solid fa-cloud"></i> SaaS for Dealers & Traders</span>
				<h1>Goods Trading Management made simple.</h1>
				<p class="lead">Dealer Pro keeps your stock, payments, companies, and profits in one place. No accounting background needed.</p>
				<div class="pill-list">
					<span class="pill">Stock & Products</span>
					<span class="pill">Purchases & Orders</span>
					<span class="pill">Payments & Ledger</span>
					<span class="pill">Sales & Profit</span>
					<span class="pill">Staff Access</span>
					<span class="pill">Reports</span>
				</div>
				<div class="cta-row">
					<a class="cta primary" href="auth/sign-up.php">Get Started</a>
					<a class="cta secondary" href="auth/sign-in.php">I already have an account</a>
				</div>
				<p style="color:var(--muted);font-size:13px;margin-top:10px;">Access anywhere. No paperwork. No manual registers.</p>
			</div>
			<div class="hero-card" style="background:linear-gradient(135deg,#fff, #eef2ff);">
				<h3 style="margin:0 0 10px;">Perfect for:</h3>
				<div class="pill-list">
					<span class="pill">Grocery traders</span>
					<span class="pill">Wholesale distributors</span>
					<span class="pill">Electronics dealers</span>
					<span class="pill">Construction suppliers</span>
				</div>
				<p class="lead">Save time, avoid money loss, and understand your business better.</p>
				<div class="card" style="margin-top:12px;">
					<div class="icon"><i class="fa-solid fa-shield"></i></div>
					<h3>Staff & Security</h3>
					<p>Add staff with limited access. Each dealer sees only their own data. Your business stays private.</p>
				</div>
			</div>
		</section>

		<section class="section" id="features">
			<h2>What you get</h2>
			<p>Simple flows that cut paperwork and keep every rupee and item tracked automatically.</p>
			<div class="grid">
				<div class="card">
					<div class="icon"><i class="fa-solid fa-building"></i></div>
					<h3>Company (Supplier) Management</h3>
					<p>Add suppliers, see what you owe or receive, and keep separate ledgers per company.</p>
				</div>
				<div class="card">
					<div class="icon"><i class="fa-solid fa-cubes"></i></div>
					<h3>Stock & Product Management</h3>
					<p>Know stock anytime, track buy/sell prices, and auto-update stock on every purchase or sale.</p>
				</div>
				<div class="card">
					<div class="icon"><i class="fa-solid fa-cart-arrow-down"></i></div>
					<h3>Purchase & Order Tracking</h3>
					<p>Create purchase orders, see pending vs received, and avoid missing or duplicate orders.</p>
				</div>
				<div class="card">
					<div class="icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
					<h3>Payment & Ledger</h3>
					<p>Automatic credits/debits, clear history, and zero confusion in company payments.</p>
				</div>
				<div class="card">
					<div class="icon"><i class="fa-solid fa-chart-line"></i></div>
					<h3>Sales & Profit Tracking</h3>
					<p>Record daily sales easily, auto-calc profit, and see which products earn more.</p>
				</div>
				<div class="card">
					<div class="icon"><i class="fa-solid fa-chart-pie"></i></div>
					<h3>Reports & Analysis</h3>
					<p>Weekly/monthly profit views and custom date filters to understand growth clearly.</p>
				</div>
			</div>
		</section>

		<section class="section" id="security">
			<h2>Secure and role-based</h2>
			<p>Admins control everything. Staff get limited, view-first access. Each dealer’s data is isolated.</p>
			<div class="card" style="display:grid;gap:10px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));align-items:center;">
				<div>
					<div class="icon"><i class="fa-solid fa-user-shield"></i></div>
					<h3>Admin controls</h3>
					<p>Full access for admins: products, companies, purchases, sales, reports, and staff.</p>
				</div>
				<div>
					<div class="icon"><i class="fa-solid fa-user"></i></div>
					<h3>Staff controls</h3>
					<p>Limited actions: record sales/purchases; restricted edits and deletions as configured.</p>
				</div>
			</div>
		</section>

		<section class="section" id="cta">
			<div class="hero-card" style="text-align:center;">
				<h2>Start in minutes</h2>
				<p class="lead">No accounting jargon. Just the essentials to keep goods, money, and profit clear.</p>
				<div class="cta-row" style="justify-content:center;">
					<a class="cta primary" href="auth/sign-up.php">Create your account</a>
					<a class="cta secondary" href="auth/sign-in.php">Sign in</a>
				</div>
			</div>
		</section>

		<footer>
			Dealer Pro — Goods Trading Management System
		</footer>
	</div>
</body>
</html>
