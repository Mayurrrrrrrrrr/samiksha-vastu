/**
 * Vastu Samiksha - Core JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // ===== Navbar Scroll Effect =====
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
        // Trigger on load
        if (window.scrollY > 50) navbar.classList.add('scrolled');
    }

    // ===== Mobile Menu =====
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navOverlay = document.getElementById('navOverlay');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('open');
            navOverlay?.classList.toggle('open');
        });
        navOverlay?.addEventListener('click', () => {
            navMenu.classList.remove('open');
            navOverlay.classList.remove('open');
        });
    }

    // ===== Dark Mode Toggle =====
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    if (themeToggle) {
        themeToggle.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
        themeToggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            themeToggle.textContent = next === 'dark' ? '☀️' : '🌙';
        });
    }

    // ===== Scroll Reveal Animations =====
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length > 0) {
        const revealOnScroll = () => {
            reveals.forEach(el => {
                const top = el.getBoundingClientRect().top;
                if (top < window.innerHeight - 100) {
                    el.classList.add('active');
                }
            });
        };
        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll(); // Initial check
    }

    // ===== Flash Message Auto-hide =====
    const flashMsg = document.querySelector('.flash-message');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.opacity = '0';
            flashMsg.style.transform = 'translateX(-50%) translateY(-20px)';
            setTimeout(() => flashMsg.remove(), 300);
        }, 4000);
    }

    // ===== Newsletter Form =====
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            const btn = this.querySelector('button');
            btn.textContent = '...';
            try {
                const res = await fetch(BASE_URL + 'api/newsletter', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                btn.textContent = data.success ? '✓' : '✗';
                if (data.success) this.querySelector('input').value = '';
            } catch {
                btn.textContent = '✗';
            }
            setTimeout(() => btn.textContent = document.documentElement.lang === 'hi' ? 'सब्सक्राइब' : 'Subscribe', 2000);
        });
    }

    // ===== Smooth Scroll for anchor links =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ===== Share Buttons =====
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = encodeURIComponent(this.dataset.url || window.location.href);
            const title = encodeURIComponent(this.dataset.title || document.title);
            const platform = this.dataset.platform;

            const urls = {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
                twitter: `https://twitter.com/intent/tweet?url=${url}&text=${title}`,
                whatsapp: `https://wa.me/?text=${title}%20${url}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${url}`,
                telegram: `https://t.me/share/url?url=${url}&text=${title}`,
            };

            if (urls[platform]) {
                window.open(urls[platform], '_blank', 'width=600,height=400');
            }
        });
    });

    // ===== Chat Widget =====
    // Chat widget is now handled inline by public_footer.php
    // No redirect needed — the footer has full chat panel with messaging

    // ===== Testimonials Slider =====
    const slider = document.querySelector('.testimonials-track');
    if (slider) {
        let currentSlide = 0;
        const cards = slider.querySelectorAll('.testimonial-card');
        const totalSlides = cards.length;
        let slidesPerView = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;

        const updateSlider = () => {
            const percent = (currentSlide * 100) / slidesPerView;
            slider.style.transform = `translateX(-${percent}%)`;
        };

        window.addEventListener('resize', () => {
            slidesPerView = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
            currentSlide = 0;
            updateSlider();
        });

        // Auto-slide
        setInterval(() => {
            currentSlide = (currentSlide + 1) % (totalSlides - slidesPerView + 1);
            updateSlider();
        }, 4000);
    }
});

// Base URL for API calls - derive from current page origin
const BASE_URL = (() => {
    const canonical = document.querySelector('link[rel="canonical"]');
    if (canonical) {
        // Extract base from canonical: https://samikshavastu.yuktaa.com/some-page → https://samikshavastu.yuktaa.com/
        const url = new URL(canonical.href);
        return url.origin + '/';
    }
    return window.location.origin + '/';
})();

// Utility: Format date
function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString(document.documentElement.lang === 'hi' ? 'hi-IN' : 'en-IN', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
}

// Utility: Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `flash-message flash-${type}`;
    toast.style.cssText = 'position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:999;max-width:500px;width:90%;';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(-20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Utility: Debounce
function debounce(fn, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}
