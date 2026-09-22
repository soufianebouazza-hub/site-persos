<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Set the character encoding and make the layout responsive on all screen sizes. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | E-Vitrine</title>
    <meta name="description" content="Contactez E-Vitrine pour discuter de votre projet de site web professionnel et obtenir des informations sur nos offres.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#142b27">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_BE">
    <meta property="og:site_name" content="E-Vitrine">
    <meta property="og:title" content="Contact | E-Vitrine">
    <meta property="og:description" content="Parlons de votre projet de site web professionnel.">
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
                <li><a href="/services">Services</a></li>
                <li><a href="/connexion">Mes espaces</a></li>
                <li><a href="/contact" class="contact-link active">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <!-- Contact-page introduction and direct contact information. -->
        <section class="contact-hero">
            <p class="eyebrow">Parlons de votre projet</p>
            <h1>Votre prochain site commence ici.</h1>
            <p>Dites-nous ce que vous faites et ce que vous aimeriez améliorer. Pas besoin de tout avoir défini.</p>
            <div class="contact-details">
                <a href="mailto:info@e-vitrine.com">info@e-vitrine.com</a>
            </div>
        </section>

        <!-- Contact form collects the details needed to respond to a project request. -->
        <section class="contact-form-section" id="demande">
            <div class="form-intro">
                <p class="eyebrow">Votre demande</p>
                <h2>Faisons connaissance.</h2>
                <p>Quelques informations suffisent pour nous aider à comprendre votre besoin.</p>
            </div>
            <form class="contact-form" id="project-form" method="post" action="/contact">
                <?php $_SESSION['contact_nonce'] ??= bin2hex(random_bytes(24)); $old=$flash['old']??[]; ?>
 <input type="hidden" name="_csrf" value="<?= App\Core\Http::escape($_SESSION['csrf']) ?>"><input type="hidden" name="nonce" value="<?= App\Core\Http::escape($_SESSION['contact_nonce']) ?>">
 <div hidden aria-hidden="true"><label>Ne pas remplir<input name="website" tabindex="-1" autocomplete="off"></label></div>
 <?php if($flash): ?><p role="status" class="portal-status"><?= App\Core\Http::escape($flash['text']) ?></p><?php endif; ?>
 <div class="form-group"><label for="offer">Votre projet</label><select id="offer" name="offer"><?php foreach(['a-definir'=>'Je ne sais pas encore','essentiel'=>'Site Essentiel','professionnel'=>'Site Professionnel','sur-mesure'=>'Site Sur mesure'] as $value=>$label): ?><option value="<?= $value ?>" <?= ($old['offer']??'')===$value?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select></div>
                <div class="form-group">
                    <label for="company-name">Votre nom ou votre activité <span aria-hidden="true">*</span></label>
                    <input type="text" id="company-name" name="company_name" maxlength="250" value="<?= App\Core\Http::escape($old['company_name']??'') ?>" placeholder="Ex. Atelier Martin" autocomplete="organization" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Téléphone (facultatif)</label>
                        <input type="tel" id="phone" name="phone" maxlength="250" value="<?= App\Core\Http::escape($old['phone']??'') ?>" placeholder="Ex. 04 12 34 56 78" autocomplete="tel">
                    </div>
                    <div class="form-group">
                        <label for="email">Adresse e-mail <span aria-hidden="true">*</span></label>
                        <input type="email" id="email" name="email" maxlength="254" value="<?= App\Core\Http::escape($old['email']??'') ?>" placeholder="nom@societe.com" autocomplete="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="message">Votre message <span aria-hidden="true">*</span></label>
                    <textarea id="message" name="message" maxlength="5500" rows="6" placeholder="Décrivez votre activité, vos besoins ou vos questions..." required><?= App\Core\Http::escape($old['message']??'') ?></textarea>
                </div>
                <p class="contact-privacy">Ces informations servent à répondre à votre demande. Le téléphone est facultatif. Votre demande est enregistrée dans notre base et accessible au gestionnaire. <a href="/rgpd">Consulter la politique de confidentialité</a>.</p>
                <button type="submit" class="form-submit">Envoyer ma demande <span aria-hidden="true">→</span></button>
                <p class="form-note">Les champs * sont obligatoires. Votre demande sera transmise directement à E-Vitrine.</p>
            </form>
        </section>

        <!-- Frequently asked questions shown before the contact form. -->
        <section class="faq-section faq-top">
            <div class="faq-heading">
                <p class="eyebrow">Avant de nous écrire</p>
                <h2>Questions fréquentes</h2>
            </div>
            <div class="faq-list">
                <details class="faq-item">
                    <summary>À qui s'adressent vos sites web ?</summary>
                    <p>Nos offres s'adressent aux indépendants, petites entreprises et professionnels qui souhaitent présenter leur activité avec un site clair et soigné.</p>
                </details>
                <details class="faq-item">
                    <summary>Est-ce que le site fonctionne sur téléphone ?</summary>
                    <p>Oui. Chaque site est pensé pour être confortable à parcourir sur ordinateur, tablette et smartphone.</p>
                </details>
                <details class="faq-item">
                    <summary>Puis-je demander un site adapté à mon activité ?</summary>
                    <p>Bien sûr. Nous échangeons d'abord sur vos besoins afin de proposer une structure et un design adaptés à votre projet.</p>
                </details>
            </div>
        </section>

        <!-- Additional frequently asked questions shown after the contact form. -->
        <section class="faq-section faq-bottom">
            <div class="faq-heading">
                <p class="eyebrow">Encore une question ?</p>
                <h2>Autres questions fréquentes</h2>
            </div>
            <div class="faq-list">
                <details class="faq-item">
                    <summary>Combien de temps faut-il pour créer un site ?</summary>
                    <p>Le délai dépend du nombre de pages et du contenu à préparer. Nous établissons un calendrier clair dès le début du projet.</p>
                </details>
                <details class="faq-item">
                    <summary>Puis-je faire évoluer mon site plus tard ?</summary>
                    <p>Oui. Votre vitrine peut évoluer avec votre activité : nouvelle offre, nouvelle page, actualisation de texte ou de visuels.</p>
                </details>
                <details class="faq-item">
                    <summary>Comment obtenir un devis ?</summary>
                    <p>Envoyez votre demande directement via le formulaire. Nous vous recontacterons pour comprendre votre besoin et préparer une proposition adaptée.</p>
                </details>
            </div>
        </section>
    </main>
<!-- Shared site footer. -->
<footer>
        <p>&copy; 2026 E-Vitrine. Tous droits réservés.</p>
<p class="footer-privacy"><a href="/rgpd">Confidentialité & RGPD</a></p></footer>
</body>
</html>
