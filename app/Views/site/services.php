<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Set the character encoding and make the layout responsive on all screen sizes. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | E-Vitrine</title>
    <meta name="description" content="Découvrez les services E-Vitrine : conseil, création visuelle et accompagnement pour votre site web professionnel.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#142b27">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_BE">
    <meta property="og:site_name" content="E-Vitrine">
    <meta property="og:title" content="Services | E-Vitrine">
    <meta property="og:description" content="Conseil, création et accompagnement pour construire une présence en ligne claire et professionnelle.">
    <!-- Load the site's custom styles before the Bootstrap framework. -->
    <!-- Load shared page animations after the document has been parsed. -->
    <script src="/assets/script.js" defer></script>
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
                <li><a href="/services" class="active">Services</a></li>
                <li><a href="/connexion">Mes espaces</a></li>
                <li><a href="/contact" class="contact-link">Parlons de votre projet</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main content for the service offerings page. -->
    <main>
        <!-- Page introduction. -->
        <section class="page-hero">
            <h1>Nos services</h1>
            <p>Un accompagnement simple, humain et structuré pour donner forme à votre présence en ligne.</p>
        </section>

        <!-- Service options shown as reusable cards. -->
        <section class="content-section">
            <h2>De l'idée à la mise en ligne</h2>
            <p class="section-intro">Nous avançons avec vous à chaque étape afin de construire un site cohérent, facile à comprendre et agréable à parcourir.</p>
            <div class="card-grid">
                <article class="content-card">
                    <h3>Conseil &amp; stratégie</h3>
                    <p>Nous clarifions votre message, vos objectifs et les informations les plus utiles pour vos visiteurs.</p>
                </article>
                <article class="content-card">
                    <h3>Création visuelle</h3>
                    <p>Nous composons une interface sobre et moderne, fidèle à votre identité et à votre domaine d'activité.</p>
                </article>
                <article class="content-card">
                    <h3>Accompagnement</h3>
                    <p>Après la mise en ligne, nous restons disponibles pour vous guider dans les évolutions de votre vitrine.</p>
                </article>
            </div>
        </section>

        <!-- Three-step overview of the team's working process. -->
        <section class="content-section process">
            <div class="process-inner">
                <h2>Notre méthode</h2>
                <div class="steps">
                    <article><span class="step-number">01</span><h3>Écouter</h3><p>Comprendre votre activité et votre public.</p></article>
                    <article><span class="step-number">02</span><h3>Créer</h3><p>Construire une vitrine claire qui valorise votre savoir-faire.</p></article>
                    <article><span class="step-number">03</span><h3>Faire évoluer</h3><p>Vous aider à garder un site utile et actuel.</p></article>
                </div>
            </div>
        </section>
<section class="homepage-cta"><div><p class="eyebrow">De l’idée au concret</p><h2>Parlons de ce qui compte pour vos clients.</h2></div><a class="cta-button" href="/contact#demande">Présenter mon projet ↗</a></section>
    </main>

    <!-- Shared site footer. -->
    <footer><p>&copy; 2026 E-Vitrine. Tous droits réservés.</p><p class="footer-privacy"><a href="/rgpd">Confidentialité & RGPD</a></p></footer>
</body>
</html>
