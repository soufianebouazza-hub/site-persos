/* Handle Supabase authentication for the E-Vitrine client portal. */
document.addEventListener("DOMContentLoaded", async () => {
    const configuration = window.CLIENT_PORTAL_CONFIG || {};
    const isConfigured = Boolean(configuration.supabaseUrl && configuration.supabasePublishableKey);
    const loginForm = document.querySelector("#login-form");
    const status = document.querySelector("#login-status");

    /* Stop the portal from pretending to authenticate until Supabase is configured. */
    if (!isConfigured) {
        if (status) {
            status.textContent = "L'espace client sera disponible dès que sa connexion sécurisée sera configurée.";
        }
        const submitButton = loginForm?.querySelector("button[type='submit']");
        if (submitButton) {
            submitButton.disabled = true;
        }
        return;
    }

    if (!window.supabase) {
        if (status) {
            status.textContent = "Le service de connexion est temporairement indisponible.";
        }
        return;
    }

    const supabase = window.supabase.createClient(
        configuration.supabaseUrl,
        configuration.supabasePublishableKey
    );

    /* Submit email and password credentials through Supabase Auth. */
    if (loginForm) {
        loginForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            const email = loginForm.elements.email.value.trim();
            const password = loginForm.elements.password.value;
            status.textContent = "Connexion en cours...";

            const { error } = await supabase.auth.signInWithPassword({ email, password });
            if (error) {
                status.textContent = "Impossible de vous connecter. Vérifiez vos identifiants.";
                return;
            }

            window.location.href = "espace-client.html";
        });
    }

    /* Restrict the dashboard to authenticated users and display their email address. */
    const clientEmail = document.querySelector("#client-email");
    if (clientEmail) {
        const { data, error } = await supabase.auth.getUser();
        if (error || !data.user) {
            window.location.href = "connexion.html";
            return;
        }
        clientEmail.textContent = data.user.email;
    }

    /* End the active Supabase session when the client signs out. */
    const logoutButton = document.querySelector("#logout-button");
    if (logoutButton) {
        logoutButton.addEventListener("click", async () => {
            await supabase.auth.signOut();
            window.location.href = "connexion.html";
        });
    }
});
