document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const loginErrorAlert = document.getElementById("loginErrorAlert");

    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            const username = document.getElementById("username");
            const password = document.getElementById("password");

            if (!username?.value || !password?.value) {
                e.preventDefault();
                alert("Todos los campos son obligatorios");
            }
        });
    }

    if (loginErrorAlert) {
        const closeButton = loginErrorAlert.querySelector(".login-alert__close");

        const hideAlert = () => {
            loginErrorAlert.classList.add("login-alert--hidden");
            window.setTimeout(() => {
                loginErrorAlert.remove();
            }, 260);
        };

        closeButton?.addEventListener("click", hideAlert);
        window.setTimeout(hideAlert, 7000);
    }
});
