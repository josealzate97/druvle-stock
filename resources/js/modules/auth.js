document.addEventListener("DOMContentLoaded", () => {

    console.log("Auth Js Loaded!");

    const loginForm = document.getElementById("loginForm");

    if (loginForm) {
    
        loginForm.addEventListener("submit", (e) => {
    
            const email = document.getElementById("email");
            const password = document.getElementById("password");

            if (!email.value || !password.value) {
                e.preventDefault();
                alert("Todos los campos son obligatorios");
            }

        });

    }

});
