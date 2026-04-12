</main>

<!-- WhatsApp Float -->
<div class="whatsapp-float">
    <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" rel="noopener" title="Chat on WhatsApp">
        💬
    </a>
</div>

<!-- Native Chat Widget (visible on all public pages) -->
<div class="chat-widget-float" id="chatWidgetContainer">
    <button class="chat-widget-btn" id="chatWidgetBtn" title="<?= t('chat_title') ?>">
        💭
        <span class="unread-badge" id="chatUnreadBadge" style="display:none;">0</span>
    </button>

    <!-- Chat Panel -->
    <div class="chat-widget-panel" id="chatWidgetPanel" style="display:none;">
        <div class="chat-widget-header">
            <div class="chat-widget-header-info">
                <div class="avatar-placeholder" style="width:36px;height:36px;font-size:14px;">स</div>
                <div>
                    <strong><?= CONSULTANT_NAME ?></strong>
                    <span class="chat-widget-status"><?= t('chat_online') ?></span>
                </div>
            </div>
            <button class="chat-widget-close" id="chatWidgetClose">✕</button>
        </div>

        <?php if (isLoggedIn()): ?>
            <div class="chat-widget-messages" id="widgetMessages">
                <div class="chat-widget-welcome">
                    <div style="font-size:2rem;margin-bottom:8px;">🏠</div>
                    <p><?= $lang === 'hi' ? 'नमस्ते! वास्तु या अंक ज्योतिष के बारे में कोई सवाल?' : 'Hello! Any questions about Vastu or Numerology?' ?>
                    </p>
                </div>
            </div>
            <div class="chat-widget-input">
                <input type="text" id="widgetChatInput" placeholder="<?= t('chat_placeholder') ?>" autocomplete="off">
                <button id="widgetSendBtn"><?= t('send') ?></button>
            </div>
        <?php else: ?>
            <div class="chat-widget-messages" style="display:flex;align-items:center;justify-content:center;">
                <div class="text-center" style="padding:24px;">
                    <div style="font-size:3rem;margin-bottom:16px;">🔐</div>
                    <h4 style="margin-bottom:8px;"><?= $lang === 'hi' ? 'चैट करने के लिए लॉगिन करें' : 'Login to Chat' ?>
                    </h4>
                    <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">
                        <?= $lang === 'hi' ? 'समीक्षा जी से सीधे बात करने के लिए अपना अकाउंट बनाएं' : 'Create your account to chat directly with Samiksha ji' ?>
                    </p>
                    <a href="<?= BASE_URL ?>login" class="btn btn-primary btn-lg"
                        style="width:100%;"><?= t('nav_login') ?></a>
                    <div style="margin-top:12px;font-size:13px;color:var(--text-muted);">
                        <?= t('no_account') ?> <a href="<?= BASE_URL ?>register"
                            style="color:var(--primary);"><?= t('nav_register') ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isLoggedIn()): ?>
    <script>
        (function () {
            const panel = document.getElementById('chatWidgetPanel');
            const btn = document.getElementById('chatWidgetBtn');
            const closeBtn = document.getElementById('chatWidgetClose');
            const msgContainer = document.getElementById('widgetMessages');
            const input = document.getElementById('widgetChatInput');
            const sendBtn = document.getElementById('widgetSendBtn');
            const badge = document.getElementById('chatUnreadBadge');
            let isOpen = false;
            let lastId = 0;
            let consultantId = 1; // Consultant user ID

            // Open/close handled in notification section below

            closeBtn.addEventListener('click', () => {
                isOpen = false;
                panel.style.display = 'none';
                btn.style.display = 'flex';
            });

            async function loadMessages() {
                try {
                    const res = await fetch(`<?= BASE_URL ?>api/chat?action=poll&after=0&partner=${consultantId}`);
                    const data = await res.json();
                    if (data.messages && data.messages.length) {
                        // Clear welcome message
                        msgContainer.innerHTML = '';
                        data.messages.forEach(m => {
                            appendMessage(m.message, m.sender_id == <?= currentUserId() ?> ? 'sent' : 'received', m.time);
                            lastId = Math.max(lastId, m.id);
                        });
                        msgContainer.scrollTop = msgContainer.scrollHeight;
                    }
                } catch (e) { }
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            function appendMessage(text, type, time) {
                const div = document.createElement('div');
                div.className = `chat-widget-msg ${type}`;
                const safeText = escapeHtml(text);
                const safeTime = escapeHtml(time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
                div.innerHTML = `<span>${safeText}</span><small>${safeTime}</small>`;
                msgContainer.appendChild(div);
                msgContainer.scrollTop = msgContainer.scrollHeight;
            }

            async function sendMessage() {
                const msg = input.value.trim();
                if (!msg) return;
                input.value = '';
                appendMessage(msg, 'sent');
                try {
                    const res = await fetch('<?= BASE_URL ?>api/chat', {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'send', receiver_id: consultantId, message: msg })
                    });
                    const data = await res.json();
                    if (data.id) lastId = data.id;
                } catch (e) { }
            }

            if (sendBtn) sendBtn.addEventListener('click', sendMessage);
            if (input) input.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

            // Notification sound using Web Audio API
            function playNotificationSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.setValueAtTime(1100, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.3);
                } catch (e) { }
            }

            function showUnreadNotification(count) {
                badge.textContent = count;
                badge.style.display = 'flex';
                btn.classList.add('has-unread');
                // Flash page title
                const origTitle = document.title;
                let flash = true;
                if (window._chatTitleInterval) clearInterval(window._chatTitleInterval);
                window._chatTitleInterval = setInterval(() => {
                    document.title = flash ? `(${count}) 💬 <?= $lang === 'hi' ? 'नया संदेश' : 'New message' ?>` : origTitle;
                    flash = !flash;
                }, 1500);
            }

            function clearNotification() {
                badge.style.display = 'none';
                badge.textContent = '0';
                btn.classList.remove('has-unread');
                if (window._chatTitleInterval) {
                    clearInterval(window._chatTitleInterval);
                    window._chatTitleInterval = null;
                }
            }

            // Poll for new messages
            setInterval(async () => {
                try {
                    const res = await fetch(`<?= BASE_URL ?>api/chat?action=poll&after=${lastId}&partner=${consultantId}`);
                    const data = await res.json();
                    if (data.messages) {
                        data.messages.forEach(m => {
                            if (m.sender_id != <?= currentUserId() ?>) {
                                if (isOpen) {
                                    appendMessage(m.message, 'received', m.time);
                                } else {
                                    const count = parseInt(badge.textContent || '0') + 1;
                                    showUnreadNotification(count);
                                    playNotificationSound();
                                }
                            }
                            lastId = Math.max(lastId, m.id);
                        });
                    }
                } catch (e) { }
            }, 4000);

            // Override open to clear notifications
            const origOpen = btn.addEventListener;
            btn.addEventListener('click', () => {
                isOpen = !isOpen;
                panel.style.display = isOpen ? 'flex' : 'none';
                btn.style.display = isOpen ? 'none' : 'flex';
                if (isOpen) { loadMessages(); clearNotification(); }
            });

            // Check unread on page load
            fetch('<?= BASE_URL ?>api/chat?action=unread').then(r => r.json()).then(d => {
                if (d.count > 0) { showUnreadNotification(d.count); }
            }).catch(() => { });
        })();
    </script>
<?php endif; ?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- About Column -->
            <div class="footer-about">
                <a href="<?= BASE_URL ?>" class="footer-brand">
                    <div class="nav-logo">वा</div>
                    <span class="footer-brand-text">
                        <?= SITE_NAME ?>
                    </span>
                </a>
                <p>
                    <?= t('footer_about') ?>
                </p>
                <div class="footer-social-links">
                    <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener" title="Facebook">📘</a>
                    <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" title="Instagram">📸</a>
                    <a href="<?= SOCIAL_YOUTUBE ?>" target="_blank" rel="noopener" title="YouTube">🎬</a>
                    <a href="<?= SOCIAL_TWITTER ?>" target="_blank" rel="noopener" title="Twitter">🐦</a>
                    <a href="<?= SOCIAL_TELEGRAM ?>" target="_blank" rel="noopener" title="Telegram">✈️</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3>
                    <?= t('footer_links') ?>
                </h3>
                <div class="footer-links">
                    <a href="<?= BASE_URL ?>about">
                        <?= t('nav_about') ?>
                    </a>
                    <a href="<?= BASE_URL ?>services">
                        <?= t('nav_services') ?>
                    </a>
                    <a href="<?= BASE_URL ?>blogs">
                        <?= t('nav_blogs') ?>
                    </a>
                    <a href="<?= BASE_URL ?>videos">
                        <?= t('nav_videos') ?>
                    </a>
                    <a href="<?= BASE_URL ?>ebooks">
                        <?= t('nav_ebooks') ?>
                    </a>
                    <a href="<?= BASE_URL ?>games">
                        <?= t('nav_games') ?>
                    </a>
                    <a href="<?= BASE_URL ?>questions">
                        <?= t('questions_title') ?>
                    </a>
                </div>
            </div>

            <!-- Services Links -->
            <div>
                <h3>
                    <?= t('nav_services') ?>
                </h3>
                <div class="footer-links">
                    <a href="<?= BASE_URL ?>services">
                        <?= t('service_vastu_home') ?>
                    </a>
                    <a href="<?= BASE_URL ?>services">
                        <?= t('service_vastu_office') ?>
                    </a>
                    <a href="<?= BASE_URL ?>services">
                        <?= t('service_numerology') ?>
                    </a>
                    <a href="<?= BASE_URL ?>services">
                        <?= t('service_remedies') ?>
                    </a>
                    <a href="<?= BASE_URL ?>services">
                        <?= t('service_name_correction') ?>
                    </a>
                    <a href="<?= BASE_URL ?>numerology-calculator">
                        <?= t('numerology_calc') ?>
                    </a>
                </div>
            </div>

            <!-- Contact & Newsletter -->
            <div>
                <h3>
                    <?= t('footer_contact') ?>
                </h3>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon">📧</div>
                    <div>
                        <strong>Email</strong><br>
                        <a href="mailto:<?= SITE_EMAIL ?>" style="color: rgba(255,255,255,0.6);">
                            <?= SITE_EMAIL ?>
                        </a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon">📞</div>
                    <div>
                        <strong>
                            <?= t('phone') ?>
                        </strong><br>
                        <a href="tel:<?= SITE_PHONE ?>" style="color: rgba(255,255,255,0.6);">
                            <?= SITE_PHONE ?>
                        </a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon">📍</div>
                    <div>
                        <strong>
                            <?= t('address') ?>
                        </strong><br>
                        <?= SITE_ADDRESS ?>
                    </div>
                </div>

                <div class="footer-newsletter">
                    <p style="margin-bottom: var(--space-3); font-size: var(--font-size-sm);">
                        <?= t('footer_newsletter_sub') ?>
                    </p>
                    <form class="footer-newsletter-form" id="newsletterForm">
                        <input type="email" placeholder="<?= t('footer_email_placeholder') ?>" required>
                        <button type="submit">
                            <?= t('footer_subscribe') ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>
                <?= t('footer_copyright') ?>
            </span>
            <span>Made with ❤️ by
                <?= CONSULTANT_NAME ?>
            </span>
        </div>
    </div>
</footer>

<!-- Core JS -->
<script src="<?= ASSETS_URL ?>js/app.js"></script>
<?php if (isset($extraJS)): ?>
    <script src="<?= ASSETS_URL ?>js/<?= $extraJS ?>"></script>
<?php endif; ?>
</body>

</html>