document.addEventListener('DOMContentLoaded', () => {

    /* ==========================
       STAGGER ANIMATION
    ========================== */
    document.querySelectorAll('.stagger-item').forEach((el, i) => {
        el.style.animationDelay = `${0.3 + i * 0.06}s`;
    });

    const form     = document.getElementById('loginForm');
    const button   = document.getElementById('loginBtn');
    const card     = document.getElementById('loginCard');
    const errorBox = document.querySelector('.error-box');

    const emailInput    = form?.querySelector('input[name="email"]');
    const passwordInput = form?.querySelector('input[name="password"]');

    if (!form || !button || !card) return;

    let isSubmitting = false;
    let failedCount  = 0;

    /* ==========================
       DISABLE SUBMIT IF EMPTY
    ========================== */
    const validateForm = () => {
        button.disabled = !emailInput.value || !passwordInput.value;
    };

    emailInput?.addEventListener('input', validateForm);
    passwordInput?.addEventListener('input', validateForm);

    validateForm();

    /* ==========================
       CLEAR ERROR ON INPUT
    ========================== */
    [emailInput, passwordInput].forEach(input => {
        input?.addEventListener('input', () => {
            errorBox?.classList.add('hide');
            card.classList.remove('has-error');
        });
    });

    /* ==========================
       CAPS LOCK DETECTOR
    ========================== */
    const capsWarning = document.createElement('div');
    capsWarning.className = 'caps-warning';
    capsWarning.textContent = 'Caps Lock is ON';

    passwordInput?.after(capsWarning);

    passwordInput?.addEventListener('keyup', (e) => {
        capsWarning.classList.toggle('show', e.getModifierState('CapsLock'));
    });

    /* ==========================
       TOGGLE SHOW PASSWORD
    ========================== */
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'toggle-password';
    toggleBtn.innerHTML = '👁';

    passwordInput?.parentNode.appendChild(toggleBtn);

    toggleBtn.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        toggleBtn.innerHTML = isHidden ? '🙈' : '👁';
    });

    /* ==========================
       REMEMBER ME (LOCAL)
    ========================== */
    if (localStorage.getItem('remember_email')) {
        emailInput.value = localStorage.getItem('remember_email');
    }

    emailInput?.addEventListener('blur', () => {
        localStorage.setItem('remember_email', emailInput.value);
    });

    /* ==========================
       SUBMIT HANDLER
    ========================== */
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        if (isSubmitting || button.disabled) return;

        isSubmitting = true;

        button.classList.add('loading');
        button.disabled = true;

        form.submit();
    });

    /* ==========================
       ERROR STATE (AFTER RELOAD)
    ========================== */
    if (errorBox) {

        failedCount++;

        card.classList.add('has-error');

        button.classList.remove('loading');
        button.disabled = false;
        isSubmitting = false;

        /* auto hide error */
        setTimeout(() => {
            errorBox.classList.add('hide');
        }, 2800);

        /* reset shake */
        setTimeout(() => {
            card.classList.remove('has-error');
        }, 600);

        /* ==========================
           BRUTE FORCE COOLDOWN
        ========================== */
        if (failedCount >= 3) {
            let cooldown = 10;

            card.classList.add('is-cooldown');
            button.textContent = `Try again in ${cooldown}s`;

            const timer = setInterval(() => {
                cooldown--;
                button.textContent = `Try again in ${cooldown}s`;

                if (cooldown <= 0) {
                    clearInterval(timer);
                    card.classList.remove('is-cooldown');
                    button.innerHTML = `
                        <span class="btn-text">Sign In</span>
                        <span class="btn-spinner"></span>
                    `;
                    validateForm();
                    failedCount = 0;
                }
            }, 1000);
        }
    }

    /* ==========================
       SUCCESS TRANSITION
       (Laravel redirect)
    ========================== */
    if (!errorBox && isSubmitting) {
        card.classList.add('success');
    }

});
