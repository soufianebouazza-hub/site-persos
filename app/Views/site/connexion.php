<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Set the character encoding and make the layout responsive on all screen sizes. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes espaces | E-Vitrine</title>
    <meta name="description" content="Accédez à votre espace client E-Vitrine pour suivre votre projet et consulter vos documents.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#142b27">
    <!-- Load the site's custom styles and shared animation script. -->
    <script src="/assets/script.js" defer></script>
    <!-- Load the official Supabase browser client before the portal scripts. -->
    <script src="/assets/espace-data.js" defer></script>
    <script src="/assets/client-auth.js" defer></script>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <!-- Shared site navigation; this page is marked as active. -->
    <header class="site-header">
        <a class="brand" href="/" aria-label="E-Vitrine - Accueil">
            <img src="/assets/img/logo-evitrine-vert.svg" alt="Logo E-Vitrine" class="logo">
            <span>E-Vitrine</span>
        </a>
        <!-- Toggle the mobile navigation menu on small screens. -->
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="Ouvrir le menu">
            <span></span><span></span><span></span>
        </button>
        <nav id="site-navigation" aria-label="Navigation principale">
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/offres">Nos offres</a></li>
                <li><a href="/services">Services</a></li>
                <li><a href="/connexion" class="active">Mes espaces</a></li>
                <li><a href="/contact" class="contact-link">Parlons de votre projet</a></li>
            </ul>
        </nav>
    </header>

    <main class="portal-page">
        <!-- Explain the purpose of the client portal before the sign-in form. -->
        <section class="portal-intro">
            <p class="eyebrow">Gestion · Organisation · Suivi client</p>
            <h1>Tout votre travail, au même endroit.</h1>
            <p>Gérez votre activité, organisez vos tâches ou suivez votre projet client depuis votre espace.</p>
        </section>

        <!-- Collect client credentials for Supabase authentication. -->
        <section class="portal-login-section">
            <form class="portal-login" id="login-form" method="post" action="/api/login">
                <input type="hidden" name="_csrf" value="<?= App\Core\Http::escape($_SESSION['csrf']) ?>"><h2>Accéder à mon espace</h2><noscript>Activez JavaScript pour accéder à votre espace.</noscript>
                <p>Votre compte ouvre automatiquement l’espace correspondant à vos droits.</p>
                <div class="form-group">
                    <label for="portal-email">Adresse e-mail</label>
                    <input type="email" id="portal-email" name="email" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label for="portal-password">Mot de passe</label>
                    <input type="password" id="portal-password" name="password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="form-submit">Se connecter <span aria-hidden="true">→</span></button>
                <p id="login-status" class="portal-status" aria-live="polite"></p>
                <p class="portal-help">Vous n'avez pas encore vos accès ? <a href="/contact">Contactez-nous</a>.</p>
            </form>
        </section>
<section class="spaces-preview"><h2>Vos espaces privés</h2><p>Gestion des demandes, projets clients et organisation personnelle : connectez-vous avec vos accès.</p><?php if (App\Core\Config::get("env")==="local" && !App\Core\Database::query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetch()): ?><p><a href="/activation">Créer mon compte gestionnaire →</a></p><?php endif; ?></section>
    </main>

    <!-- Shared site footer. -->
    <footer><p>&copy; 2026 E-Vitrine. Tous droits réservés.</p><p class="footer-privacy"><a href="/rgpd">Confidentialité & RGPD</a></p></footer>
</body>
</html>
