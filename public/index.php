<?php
/**
 * EduPay Africa - Fintech Landing Page
 * Version: 3.0 (Production Optimized)
 * Author: Francis Kienji
 * Stack: PHP, HTML5, CSS3, Vanilla JS
 */

// Simple logic for dynamic CTA based on session (if applicable)
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$primary_cta_link = $is_logged_in ? 'dashboard.php' : 'register.php';
$primary_cta_text = $is_logged_in ? 'Go to Dashboard' : 'Register Your School';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPay Africa | Digitizing the Financial Pulse of African Education</title>
    
    <meta name="description" content="Secure, transparent, and automated school fee collection for Kenyan institutions. Integrated with M-Pesa STK Push and real-time analytics.">
    <meta name="keywords" content="Fintech Kenya, Edutech Africa, School Fees Payment, M-Pesa Schools, EduPay Africa">
    <meta name="author" content="EduPay Africa">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://edupayafrica.com/">
    <meta property="og:title" content="EduPay Africa | Smart School Payments">
    <meta property="og:description" content="Digitizing school fee collections with seamless M-Pesa integration and real-time reporting.">
    <meta property="og:image" content="assets/og-image.jpg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #003d82;
            --primary-dark: #002952;
            --primary-light: #0052b3;
            --accent: #f39c12;
            --accent-dark: #d68910;
            --success: #27ae60;
            --text-main: #1a202c;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --white: #ffffff;
            --bg-light: #f8fafb;
            --bg-lightest: #fcfdfe;
            --border: #e2e8f0;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.09);
            --shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --max-width: 1280px;
        }

        /* Base Reset & Typography */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { 
            font-family: 'Inter', sans-serif; 
            color: var(--text-main); 
            background: var(--white); 
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Accessibility: Skip Link */
        .skip-link {
            position: absolute;
            top: -100px;
            left: 0;
            background: var(--primary);
            color: white;
            padding: 1rem;
            z-index: 2000;
            transition: 0.3s;
        }
        .skip-link:focus { top: 0; }

        /* Reusable Components */
        .container { max-width: var(--max-width); margin: 0 auto; padding: 0 5%; }
        section { padding: 100px 0; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 1rem;
            letter-spacing: 0.3px;
            gap: 8px;
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white); 
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.2);
        }
        .btn-primary:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 20px rgba(0, 61, 130, 0.35); 
        }
        .btn-accent { 
            background: linear-gradient(135deg, var(--accent) 0%, #e67e22 100%);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        .btn-accent:hover { 
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(243, 156, 18, 0.4);
        }
        .btn-outline { 
            background: transparent; 
            border: 2px solid var(--primary); 
            color: var(--primary);
        }
        .btn-outline:hover { 
            background: var(--primary); 
            color: white;
            transform: translateY(-2px);
        }
        .btn-outline.white {
            border-color: white;
            color: white;
        }
        .btn-outline.white:hover {
            background: white;
            color: var(--primary);
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            height: 80px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }
        .nav-wrapper { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            width: 100%;
            padding: 0 20px;
        }
        .logo { 
            display: flex; 
            align-items: center; 
            text-decoration: none;
            gap: 12px;
            transition: var(--transition);
        }
        .logo:hover {
            opacity: 0.8;
        }
        .logo-box { 
            width: 44px; 
            height: 44px; 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.2);
        }
        .logo span { 
            font-size: 1.2rem; 
            font-weight: 800; 
            color: var(--primary); 
            letter-spacing: -0.5px;
        }

        .nav-links { 
            display: flex; 
            align-items: center; 
            gap: 45px;
        }
        .nav-links a { 
            text-decoration: none; 
            color: var(--text-secondary); 
            font-weight: 500; 
            font-size: 0.95rem; 
            transition: var(--transition);
            position: relative;
        }
        .nav-links a:after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }
        .nav-links a:hover { 
            color: var(--primary);
        }
        .nav-links a:hover:after {
            width: 100%;
        }

        .nav-auth { 
            display: flex; 
            align-items: center; 
            gap: 16px;
        }

        /* Hamburger Menu */
        .hamburger { 
            display: none; 
            cursor: pointer; 
            background: none; 
            border: none; 
            font-size: 1.5rem; 
            color: var(--primary); 
            padding: 8px; 
            transition: var(--transition);
        }
        .hamburger:hover {
            transform: scale(1.1);
        }

        .mobile-nav {
            display: none;
        }

        .nav-overlay {
            display: none;
        }

        /* Hero Section */
        .hero { 
            padding: 140px 0 100px; 
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(248, 250, 251, 1) 50%,
                rgba(243, 156, 18, 0.03) 100%);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 80% 20%, rgba(243, 156, 18, 0.08), transparent);
            pointer-events: none;
        }
        .hero-content { 
            max-width: 900px; 
            margin: 0 auto; 
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .trust-badge { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            gap: 10px; 
            background: rgba(0, 61, 130, 0.06); 
            color: var(--primary); 
            padding: 12px 24px; 
            border-radius: 50px; 
            font-size: 0.9rem; 
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 61, 130, 0.1);
            transition: var(--transition);
        }
        .trust-badge:hover {
            background: rgba(0, 61, 130, 0.1);
            border-color: rgba(0, 61, 130, 0.2);
        }
        .hero h1 { 
            font-size: clamp(2.8rem, 6vw, 4.5rem); 
            line-height: 1.15; 
            font-weight: 800; 
            color: var(--primary); 
            margin-bottom: 30px; 
            letter-spacing: -1px;
        }
        .hero p { 
            font-size: 1.35rem; 
            color: var(--text-secondary); 
            margin-bottom: 50px; 
            max-width: 750px; 
            margin-inline: auto;
            line-height: 1.7;
            font-weight: 500;
        }
        .hero-actions { 
            display: flex; 
            gap: 20px; 
            justify-content: center; 
            flex-wrap: wrap;
        }
        .btn-lg {
            padding: 18px 48px;
            font-size: 1.1rem;
        }

        /* Stats Strip */
        .stats-strip { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 70px 0; 
            color: white;
            position: relative;
            overflow: hidden;
        }
        .stats-strip::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(243, 156, 18, 0.1), transparent);
            pointer-events: none;
        }
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 40px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .stat-item {
            padding: 20px;
        }
        .stat-item h2 { 
            font-size: 3rem; 
            color: var(--accent); 
            margin-bottom: 8px;
            font-weight: 800;
        }
        .stat-item p { 
            opacity: 0.9; 
            font-size: 0.95rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1.5px;
        }

        /* Features */
        .features {
            background: var(--bg-lightest);
        }
        .section-header { 
            text-align: center; 
            margin-bottom: 80px;
        }
        .section-header h2 { 
            font-size: 2.8rem; 
            color: var(--primary); 
            margin-bottom: 18px;
            font-weight: 800;
            letter-spacing: -0.8px;
        }
        .section-header p { 
            color: var(--text-secondary); 
            max-width: 700px; 
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        
        .features-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 35px;
        }
        .feature-card { 
            background: var(--white); 
            padding: 50px; 
            border-radius: 18px; 
            box-shadow: var(--shadow-sm); 
            transition: var(--transition);
            border: 1.5px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--primary) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
            transform-origin: left;
        }
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        .feature-card:hover { 
            transform: translateY(-12px); 
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }
        .feature-icon { 
            width: 70px; 
            height: 70px; 
            background: linear-gradient(135deg, rgba(0, 61, 130, 0.08), rgba(243, 156, 18, 0.08)); 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 30px; 
            font-size: 2rem; 
            color: var(--primary);
            transition: var(--transition);
        }
        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }
        .feature-card h3 { 
            margin-bottom: 16px; 
            font-size: 1.4rem; 
            color: var(--primary);
            font-weight: 700;
        }
        .feature-card p { 
            color: var(--text-secondary); 
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* How it Works */
        .how-works { 
            background: white;
        }
        .steps-container { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 50px; 
            position: relative;
        }
        .steps-container::before {
            content: '';
            position: absolute;
            top: 80px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: linear-gradient(90deg, 
                rgba(0, 61, 130, 0.3) 0%,
                rgba(243, 156, 18, 0.3) 50%,
                rgba(0, 61, 130, 0.3) 100%);
            display: none;
        }
        .step-item { 
            text-align: center; 
            position: relative;
            padding: 20px;
        }
        .step-number { 
            width: 60px; 
            height: 60px; 
            background: linear-gradient(135deg, var(--accent) 0%, #e67e22 100%);
            color: white; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 30px; 
            font-weight: 800; 
            font-size: 1.4rem;
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.35);
            transition: var(--transition);
        }
        .step-item:hover .step-number {
            transform: scale(1.15) rotate(-5deg);
            box-shadow: 0 10px 30px rgba(243, 156, 18, 0.45);
        }
        .step-item h4 { 
            margin-bottom: 15px; 
            font-size: 1.4rem;
            color: var(--primary);
            font-weight: 700;
        }
        .step-item p {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.7;
        }

        /* Testimonials */
        .testimonials { 
            background: linear-gradient(135deg, 
                rgba(0, 61, 130, 0.02) 0%,
                rgba(243, 156, 18, 0.02) 100%);
        }
        .testimonial-box { 
            max-width: 900px; 
            margin: 0 auto; 
            background: white; 
            padding: 70px; 
            border-radius: 24px; 
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            text-align: center;
        }
        .testi-text { 
            font-size: 1.5rem; 
            font-weight: 600;
            font-style: italic; 
            color: var(--primary); 
            margin-bottom: 40px;
            line-height: 1.8;
            position: relative;
            padding: 0 50px;
        }
        .testi-text::before,
        .testi-text::after {
            content: '"';
            font-size: 3rem;
            color: var(--accent);
            position: absolute;
            opacity: 0.3;
        }
        .testi-text::before {
            top: -20px;
            left: 0;
        }
        .testi-text::after {
            bottom: -40px;
            right: 0;
        }
        .testi-author { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 18px;
        }
        .author-img { 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, var(--primary), var(--accent));
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.2);
        }
        .author-info h5 { 
            font-size: 1.1rem;
            color: var(--primary);
            font-weight: 700;
        }
        .author-info p { 
            font-size: 0.9rem; 
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* CTA Band */
        .cta-band { 
            background: linear-gradient(135deg, 
                var(--primary) 0%, 
                var(--primary-dark) 100%),
                url('https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80');
            background-size: cover; 
            background-position: center;
            background-attachment: fixed;
            color: white; 
            text-align: center; 
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }
        .cta-band::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(0, 61, 130, 0.85) 0%,
                rgba(0, 41, 82, 0.85) 100%);
            pointer-events: none;
        }
        .cta-band > .container {
            position: relative;
            z-index: 1;
        }
        .cta-band h2 { 
            font-size: 3rem; 
            margin-bottom: 25px;
            font-weight: 800;
            letter-spacing: -1px;
        }
        .cta-band p { 
            margin-bottom: 45px; 
            opacity: 0.95; 
            max-width: 700px; 
            margin-inline: auto;
            font-size: 1.2rem;
            line-height: 1.8;
        }

        /* Footer */
        footer { 
            background: linear-gradient(135deg, #0a0e12 0%, #12151c 100%);
            color: #a4b0be; 
            padding: 100px 0 40px;
            position: relative;
            overflow: hidden;
        }
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            right: -30%;
            width: 60%;
            height: 100%;
            background: radial-gradient(circle at 30% 50%, rgba(243, 156, 18, 0.08), transparent);
            pointer-events: none;
        }
        .footer-grid { 
            display: grid; 
            grid-template-columns: 2fr 1fr 1fr 1fr; 
            gap: 60px; 
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }
        .footer-col h4 { 
            color: white; 
            margin-bottom: 25px; 
            font-size: 1.1rem;
            font-weight: 700;
        }
        .footer-col h3 {
            color: white;
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 800;
        }
        .footer-col p {
            line-height: 1.8;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 14px; }
        .footer-col ul a { 
            color: inherit; 
            text-decoration: none; 
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-col ul a:hover { 
            color: var(--accent);
            transform: translateX(4px);
        }
        .contact-info li { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            margin-bottom: 16px;
        }
        .contact-info i {
            color: var(--accent);
            font-size: 1.1rem;
        }
        .footer-bottom { 
            border-top: 1px solid rgba(255,255,255,0.08); 
            padding-top: 40px;
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }
        .footer-socials {
            display: flex;
            gap: 20px;
        }
        .footer-socials a {
            color: #a4b0be;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            font-size: 1.1rem;
        }
        .footer-socials a:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .nav-links, .nav-auth { display: none; }
            .hamburger { display: block; }

            .mobile-nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 85%;
                height: 100vh;
                background: white;
                z-index: 1100;
                padding: 100px 30px 40px;
                transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: -10px 0 40px rgba(0,0,0,0.15);
                display: flex;
                flex-direction: column;
                gap: 20px;
                overflow-y: auto;
            }

            .mobile-nav.active { right: 0; }

            .mobile-nav a {
                font-size: 1.1rem;
                font-weight: 600;
                color: var(--primary);
                text-decoration: none;
                padding: 12px 0;
                transition: var(--transition);
            }

            .mobile-nav a:hover {
                color: var(--accent);
                padding-left: 8px;
            }

            .nav-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1050;
                opacity: 0;
                visibility: hidden;
                transition: 0.3s;
                display: block;
            }

            .nav-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .btn-lg {
                padding: 16px 40px;
                font-size: 1rem;
            }
            section { padding: 80px 0; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            section { padding: 60px 0; }
            .hero { padding: 100px 0 60px; }
            .hero h1 { font-size: 2.5rem; }
            .hero p { font-size: 1.1rem; }
            .hero-actions {
                gap: 12px;
            }
            .btn, .btn-lg {
                padding: 14px 28px;
                font-size: 0.95rem;
            }
            .section-header h2 { font-size: 2rem; }
            .section-header p { font-size: 1rem; }
            .cta-band { padding: 80px 0; }
            .cta-band h2 { font-size: 2.2rem; }
            .testimonial-box { 
                padding: 50px; 
            }
            .testi-text {
                font-size: 1.2rem;
                padding: 0 30px;
            }
            .footer-grid { grid-template-columns: 1fr; gap: 40px; }
            .footer-bottom { flex-direction: column; gap: 20px; text-align: center; }
            
            /* Mobile Nav Active States */
            .mobile-nav {
                position: fixed; 
                top: 0; 
                right: -100%; 
                width: 85%; 
                height: 100vh;
                background: white; 
                z-index: 1100; 
                padding: 100px 30px 40px;
                transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: -10px 0 40px rgba(0,0,0,0.15);
                display: flex; 
                flex-direction: column; 
                gap: 20px;
                overflow-y: auto;
            }
            .mobile-nav.active { right: 0; }
            .mobile-nav a { 
                font-size: 1.1rem; 
                font-weight: 600; 
                color: var(--primary); 
                text-decoration: none;
                padding: 12px 0;
                transition: var(--transition);
            }
            .mobile-nav a:hover {
                color: var(--accent);
                padding-left: 8px;
            }
            .nav-overlay {
                position: fixed; 
                top: 0; 
                left: 0; 
                width: 100%; 
                height: 100%;
                background: rgba(0,0,0,0.5); 
                z-index: 1050; 
                opacity: 0; 
                visibility: hidden;
                transition: 0.3s;
            }
            .nav-overlay.active { 
                opacity: 1; 
                visibility: visible; 
            }
        }

        @media (max-width: 640px) {
            .logo span { font-size: 1rem; }
            .hero h1 { font-size: 1.9rem; }
            .hero p { font-size: 1rem; }
            .stats-grid { gap: 30px; }
            .stat-item h2 { font-size: 2.2rem; }
            .stat-item p { font-size: 0.85rem; }
            .feature-card { padding: 35px; }
            .testimonial-box { padding: 35px 25px; }
            .testi-text {
                font-size: 1rem;
                padding: 0 15px;
            }
            .cta-band h2 { font-size: 1.8rem; }
            .cta-band p { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <a href="#main" class="skip-link">Skip to main content</a>

    <header role="banner">
        <div class="container nav-wrapper">
            <a href="index.php" class="logo" aria-label="EduPay Africa Home">
                <div class="logo-box"></div>
                <span>EDUPAY AFRICA</span>
            </a>

            <nav class="nav-links" aria-label="Desktop Navigation">
                <a href="#features">Why EduPay</a>
                <a href="#payment-options">Payment Options</a>
                <a href="#how-works">How It Works</a>
                <a href="#contact">Contact</a>
            </nav>

            <div class="nav-auth">
                <a href="login.php" class="btn btn-outline" style="padding: 11px 24px; font-size: 0.9rem; border-width: 1.5px;">Login</a>
                <a href="register.php" class="btn btn-primary" style="padding: 11px 28px; font-size: 0.9rem;">Get Started</a>
            </div>

            <button class="hamburger" id="menuToggle" aria-label="Toggle Navigation" aria-expanded="false">
                <i class="fas fa-bars-staggered"></i>
            </button>
        </div>
    </header>

    <div class="nav-overlay" id="overlay"></div>
    <nav class="mobile-nav" id="mobileNav">
        <a href="#features" class="mob-link">Why EduPay</a>
        <a href="#payment-options" class="mob-link">Payment Options</a>
        <a href="#how-works" class="mob-link">How It Works</a>
        <a href="#contact" class="mob-link">Contact</a>
        <hr style="opacity: 0.1; margin: 10px 0;">
        <a href="login.php" class="btn btn-outline" style="margin-top: 15px;">Login</a>
        <a href="register.php" class="btn btn-primary">Get Started</a>
    </nav>

    <main id="main">
        <section class="hero">
            <div class="container hero-content">
                <div class="trust-badge">
                    <i class="fas fa-shield-halved"></i>
                    Kenya-First Platform • East Africa Expansion Ready
                </div>
                <h1>A Parent-Centric Way to Collect School Fees</h1>
                <p>EduPay Africa helps schools reduce defaults, simplify parent payments, and reconcile faster with M-Pesa, bank, and gateway-ready collections.</p>
                <div class="hero-actions">
                    <a href="<?= $primary_cta_link ?>" class="btn btn-accent btn-lg">
                        <i class="fas fa-rocket"></i>
                        <?= $primary_cta_text ?>
                    </a>
                    <a href="#how-works" class="btn btn-outline btn-lg">
                        <i class="fas fa-circle-info"></i>
                        See How It Works
                    </a>
                </div>
            </div>
        </section>

        <div class="stats-strip">
            <div class="container stats-grid">
                <div class="stat-item">
                    <h2>20%+</h2>
                    <p>Fee Default Challenge Solved</p>
                </div>
                <div class="stat-item">
                    <h2>50,000+</h2>
                    <p>Schools in Addressable Market</p>
                </div>
                <div class="stat-item">
                    <h2>50M+</h2>
                    <p>Students Across East Africa</p>
                </div>
                <div class="stat-item">
                    <h2>24/7</h2>
                    <p>Institution Support</p>
                </div>
            </div>
        </div>

        <section id="features" class="features">
            <div class="container">
                <div class="section-header">
                    <h2>Built from Research on Real School Pain Points</h2>
                    <p>Designed to replace paper-ledgers, improve transparency, and make parent payments simple from day one.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-phone"></i></div>
                        <h3>Parent-First Payment UX</h3>
                        <p>No confusing paybill memorization. Parents receive clear payment prompts, guided flows, and instant confirmations.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-layer-group"></i></div>
                        <h3>Lightweight, Payment-Focused Platform</h3>
                        <p>Unlike heavy ERP suites, EduPay focuses on fees, collections, and receipts so schools deploy faster and train staff quickly.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-people-roof"></i></div>
                        <h3>Multi-Child / Multi-School Visibility</h3>
                        <p>Parents can manage multiple learners from one account and view balances, due dates, and complete payment history in one place.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-receipt"></i></div>
                        <h3>Instant Receipts on SMS/Email</h3>
                        <p>Schools and parents receive real-time payment confirmations and digital receipts immediately after successful transactions.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-mobile-screen-button"></i></div>
                        <h3>M-Pesa Daraja Ready</h3>
                        <p>Built around Kenya's dominant payment rail with STK Push capabilities and clear transaction status tracking.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
                        <h3>Security & Data Protection</h3>
                        <p>Strong access controls, secure session handling, and institution-level data isolation for safe financial operations.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="payment-options" class="how-works" aria-labelledby="payment-options-title">
            <div class="container">
                <div class="section-header">
                    <h2 id="payment-options-title">Payment Options Schools Can Roll Out in Phases</h2>
                    <p>Start with M-Pesa in Kenya, then expand to gateway and bank channels as your institution scales.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <h3>M-Pesa (Primary)</h3>
                        <p>Industry-standard mobile payments with high parent adoption and proven operational reliability.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-credit-card"></i></div>
                        <h3>Gateway Expansion (Pesapal/Cards)</h3>
                        <p>Enable additional channels for schools that want card and alternative digital payment methods.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-building-columns"></i></div>
                        <h3>Bank Reconciliation Flows</h3>
                        <p>Track transfers and keep finance teams aligned with clean, auditable records and reports.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-works" class="how-works">
            <div class="container">
                <div class="section-header">
                    <h2>Get Live Quickly with a Focused Implementation</h2>
                    <p>Launch a working collections flow fast, then add more payment channels and automation in sprints.</p>
                </div>
                <div class="steps-container">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <h4>Onboard Institution</h4>
                        <p>Register your school, upload learners in bulk, and set term-based fee structures in one admin workspace.</p>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <h4>Activate Parent Access</h4>
                        <p>Parents receive credentials, view balances instantly, and get clear instructions for each supported payment method.</p>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <h4>Collect, Reconcile, Report</h4>
                        <p>Receive payments, issue instant receipts, and monitor collections from one dashboard with institution-level visibility.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="testimonials">
            <div class="container">
                <div class="section-header" style="margin-bottom: 60px;">
                    <h2>Validated by School Finance Teams</h2>
                    <p>Built for practical deployment in Kenyan schools, not just demo environments</p>
                </div>
                <div class="testimonial-box">
                    <p class="testi-text">EduPay gave us a simpler alternative to full ERP rollouts. Parents found it easier to use, collections improved, and our team finally had transparent real-time records.</p>
                    <div class="testi-author">
                        <div class="author-img"></div>
                        <div class="author-info">
                            <h5>Jane Wambui</h5>
                            <p>Bursar, Greenfields School | Kiambu</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="container">
                <h2>Launch a Kenya-Ready Fee Collection System</h2>
                <p>Book a demo, onboard your institution, and roll out parent-friendly digital payments in days.</p>
                <a href="register.php" class="btn btn-accent btn-lg" style="padding: 18px 48px;">
                    <i class="fas fa-play"></i>
                    Request School Demo
                </a>
            </div>
        </section>
    </main>

    <footer id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo" style="margin-bottom: 20px;">
                        <div class="logo-box" style="background: white;"></div>
                        <span style="color: white;">EDUPAY AFRICA</span>
                    </div>
                    <p>A focused school payments platform built for Kenya and designed to scale across East Africa.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#features">Why EduPay</a></li>
                        <li><a href="#payment-options">Payment Options</a></li>
                        <li><a href="#how-works">How It Works</a></li>
                        <li><a href="login.php">Administrator Login</a></li>
                        <li><a href="register.php">Request Demo</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Data Protection</a></li>
                        <li><a href="#">Security Standards</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Get In Touch</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-envelope"></i> support@edupay.africa</li>
                        <li><i class="fas fa-phone"></i> +254 700 000 000</li>
                        <li><i class="fas fa-location-dot"></i> Westlands, Nairobi, Kenya</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> EduPay Africa. All Rights Reserved.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="LinkedIn" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="#" aria-label="Twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Facebook" title="Facebook"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');
        const overlay = document.getElementById('overlay');
        const mobLinks = document.querySelectorAll('.mob-link');

        function toggleMenu() {
            const isActive = mobileNav.classList.toggle('active');
            overlay.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', isActive);
            // Toggle Icon
            const icon = menuToggle.querySelector('i');
            icon.classList.toggle('fa-bars-staggered');
            icon.classList.toggle('fa-xmark');
        }

        menuToggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
        
        // Close on link click
        mobLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });

        // Close on Escape Key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                toggleMenu();
            }
        });

        // Intersection Observer for fade-in animations
        const observerOptions = { threshold: 0.1 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .step-item').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = '0.8s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>