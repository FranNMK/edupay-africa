<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPay Africa | The Future of School Collections</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo-container">
        <div class="logo-placeholder">
            <i class="fas fa-leaf"></i>
        </div>
        <h2>EDUPAY AFRICA</h2>
    </div>
    
    <div class="menu-toggle" id="mobile-menu">
        <i class="fas fa-bars"></i>
    </div>

    <nav id="nav-menu">
        <div class="nav-links">
            <a href="#features">Solutions</a>
            <a href="#how-it-works">How it Works</a>
            <a href="#pricing">Pricing</a>
            <a href="#contact">Support</a>
        </div>
        <div class="nav-auth">
            <a href="login.php" class="login-link">Login</a>
            <a href="register.php" class="btn-nav">Get Started</a>
        </div>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <span class="badge">Trusted by 50+ Schools in Kenya</span>
        <h1>Digitizing the Financial Pulse of African Education</h1>
        <p>A comprehensive ecosystem designed to bridge the gap between institutions, parents, and students through secure, transparent payment processing.</p>
        <div class="hero-btns">
            <a href="register.php" class="btn-gold-lg">Register Your School</a>
            <a href="#how-it-works" class="btn-outline">Watch Video <i class="fas fa-play-circle"></i></a>
        </div>
    </div>
</section>

<section class="stats-bar">
    <div class="stat-item"><h3>KES 12M+</h3><p>Processed Weekly</p></div>
    <div class="stat-item"><h3>15k+</h3><p>Active Students</p></div>
    <div class="stat-item"><h3>99.9%</h3><p>System Uptime</p></div>
    <div class="stat-item"><h3>24/7</h3><p>Support Access</p></div>
</section>

<section id="features" class="features-grid">
    <div class="section-title">
        <h2>Tailored Features for Modern Institutions</h2>
        <p>Move away from manual reconciliations and embrace automated finance.</p>
    </div>
    <div class="features-container">
        <div class="card">
            <div class="icon-circle"><i class="fas fa-mobile-alt"></i></div>
            <h3>M-Pesa Integration</h3>
            <p>Direct STK Push technology for instant fee payments. No more manual receipting.</p>
        </div>
        <div class="card">
            <div class="icon-circle"><i class="fas fa-chart-line"></i></div>
            <h3>Revenue Analytics</h3>
            <p>Predictive cashflow reporting. See which terms have the highest collection rates.</p>
        </div>
        <div class="card">
            <div class="icon-circle"><i class="fas fa-user-graduate"></i></div>
            <h3>Student Portals</h3>
            <p>Each student gets a digital ID and a live ledger of their fee history.</p>
        </div>
        <div class="card">
            <div class="icon-circle"><i class="fas fa-file-invoice-dollar"></i></div>
            <h3>Automated Reminders</h3>
            <p>Send Bulk SMS reminders to parents automatically when balances are due.</p>
        </div>
    </div>
</section>

<section id="how-it-works" class="how-it-works">
    <h2>Three Steps to Efficiency</h2>
    <div class="steps-container">
        <div class="step">
            <div class="step-num">1</div>
            <h4>Onboard School</h4>
            <p>Upload your student list and define your fee structure categories.</p>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <h4>Invite Parents</h4>
            <p>Parents receive unique login credentials to track their children's progress.</p>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <h4>Collect & Reconcile</h4>
            <p>Payments reflect instantly on the school dashboard with zero manual entry.</p>
        </div>
    </div>
</section>

<footer>
    <div class="footer-grid">
        <div class="footer-col">
            <h3>EDUPAY AFRICA</h3>
            <p>Empowering the next generation of African scholars through financial technology.</p>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">API Documentation</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact Us</h4>
            <p>Nairobi, Kenya</p>
            <p>Email: support@edupay.africa</p>
            <p>Tel: +254 700 000 000</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2026 EduPay Africa. All Rights Reserved.
    </div>
</footer>

<script>
    const menuToggle = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');

    // 1. Toggle menu when clicking the hamburger icon
    menuToggle.addEventListener('click', (e) => {
        navMenu.classList.toggle('active');
        e.stopPropagation(); // Prevents the "click outside" listener from firing immediately
    });

    // 2. Close menu when clicking anywhere else on the page
    document.addEventListener('click', (e) => {
        // If the menu is open and the click was NOT inside the menu or the toggle button
        if (navMenu.classList.contains('active') && 
            !navMenu.contains(e.target) && 
            !menuToggle.contains(e.target)) {
            navMenu.classList.remove('active');
        }
    });

    // 3. Optional: Close menu when a link inside is clicked
    const links = document.querySelectorAll('nav a');
    links.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
</script>
</body>
</html>