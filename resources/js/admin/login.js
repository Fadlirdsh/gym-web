const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const emailInput = document.getElementById('email');
const loginForm = document.querySelector('form');
const loginBtn = document.getElementById('loginBtn');
const authWrapper = document.querySelector('.auth-wrapper');
const toast = document.getElementById('toast');
const backendError = document.getElementById('backendError');
const slideElements = document.querySelectorAll('.slide-element');

let isSubmitting = false;
let failedAttempts = parseInt(localStorage.getItem('admin_fail')) || 0;

/* ===== TOGGLE PASSWORD ===== */
togglePassword.addEventListener('click', () => {
    passwordInput.type =
        passwordInput.type === 'password' ? 'text' : 'password';
    togglePassword.classList.toggle('fa-eye');
    togglePassword.classList.toggle('fa-eye-slash');
});

/* ===== STAGGER ENTRANCE ===== */
window.addEventListener('DOMContentLoaded', () => {
    slideElements.forEach((el, i) =>
        setTimeout(() => el.classList.add('show'), 120 * i)
    );
});

/* ===== SUBMIT LOCK ===== */
loginForm.addEventListener('submit', () => {
    if (isSubmitting || loginBtn.classList.contains('locked')) return;

    isSubmitting = true;
    loginBtn.classList.add('loading');
    authWrapper.classList.add('loading');
});

/* ===== ON LOAD (ERROR & SUCCESS HANDLING) ===== */
window.addEventListener('load', () => {

    const emailError = emailInput.dataset.error === 'true';
    const passwordError = passwordInput.dataset.error === 'true';

    // PRIORITY FOCUS
    if (passwordError) {
        passwordInput.closest('.field-wrapper').classList.add('error');
        passwordInput.focus();
    } else if (emailError) {
        emailInput.closest('.field-wrapper').classList.add('error');
        emailInput.focus();
    } else {
        emailInput.focus();
    }

    // BACKEND ERROR → TOAST
    if (backendError) {
        showToast(backendError.dataset.message, 'error');
        failedAttempts++;
        localStorage.setItem('admin_fail', failedAttempts);
    }

    // SUCCESS LOGIN
    if (authWrapper.dataset.success === 'true') {
        handleLoginSuccess();
    }

    rateLimitUI();
});

/* ===== RESET ERROR ON INPUT ===== */
[emailInput, passwordInput].forEach(input => {
    input.addEventListener('input', () => {
        input.closest('.field-wrapper').classList.remove('error');
    });
});

/* ===== SUCCESS HANDLER ===== */
function handleLoginSuccess() {
    authWrapper.classList.remove('loading');
    authWrapper.classList.add('success');

    loginBtn.classList.remove('loading');
    loginBtn.classList.add('success');

    showToast('Login berhasil. Mengalihkan...', 'success');

    setTimeout(() => {
        window.location.href = '/admin/home';
    }, 900);
}

/* ===== TOAST ===== */
function showToast(message, type = 'error') {
    toast.className = `toast show ${type}`;
    toast.innerText = message;

    setTimeout(() => {
        toast.className = 'toast';
    }, 4000);
}

/* ===== RATE LIMIT UX ===== */
function rateLimitUI() {
    if (failedAttempts >= 5) {
        loginBtn.classList.add('locked');
        showToast('Terlalu banyak percobaan login. Tunggu 15 detik.', 'warning');

        setTimeout(() => {
            failedAttempts = 0;
            localStorage.removeItem('admin_fail');
            loginBtn.classList.remove('locked');
        }, 15000);
    }
}
