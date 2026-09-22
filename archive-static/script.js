/* Animate site sections while respecting reduced-motion preferences. */
document.addEventListener("DOMContentLoaded", () => {
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const heroElements = document.querySelectorAll(".hero-content > *, .hero-preview");
    const revealElements = document.querySelectorAll(
        ".site-header, .benefits-intro, .benefit-card, .homepage-cta, .page-hero > *, .content-section > h2, .section-intro, .content-card, .steps > article, .contact-hero > *, .faq-heading, .faq-item, .contact-form-section, .contact-form, .portal-intro > *, .portal-login, .dashboard-welcome > *, .dashboard-card, .dashboard-support, footer"
    );
    const menuToggle = document.querySelector(".menu-toggle");
    const navigation = document.querySelector("#site-navigation");

    /* Open and close the compact navigation menu on tablets and phones. */
    const closeMenu = () => {
        if (!menuToggle || !navigation) return;
        navigation.classList.remove("is-open");
        menuToggle.setAttribute("aria-expanded", "false");
        menuToggle.setAttribute("aria-label", "Ouvrir le menu");
    };

    if (menuToggle && navigation) {
        menuToggle.addEventListener("click", () => {
            const isOpen = navigation.classList.toggle("is-open");
            menuToggle.setAttribute("aria-expanded", String(isOpen));
            menuToggle.setAttribute("aria-label", isOpen ? "Fermer le menu" : "Ouvrir le menu");
        });

        navigation.querySelectorAll("a").forEach((link) => link.addEventListener("click", closeMenu));
        window.addEventListener("resize", () => {
            if (window.innerWidth > 850) closeMenu();
        });
        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") closeMenu();
        });
    }

    /* Make the hero content visible in a short, staggered sequence. */
    heroElements.forEach((element, index) => {
        element.classList.add("js-reveal");
        element.style.transitionDelay = `${index * 120}ms`;
    });

    requestAnimationFrame(() => {
        heroElements.forEach((element) => element.classList.add("is-visible"));
    });

    /* Keep the homepage mock website panel gently moving after its entrance. */
    const preview = document.querySelector(".hero-preview");
    if (preview && !prefersReducedMotion) {
        window.setTimeout(() => preview.classList.add("is-floating"), 700);
    }

    /* Reveal content on every page only when it enters the viewport. */
    revealElements.forEach((element, index) => {
        element.classList.add("js-reveal");
        element.style.transitionDelay = `${(index % 3) * 110}ms`;
    });

    if (prefersReducedMotion || !("IntersectionObserver" in window)) {
        revealElements.forEach((element) => element.classList.add("is-visible"));
        return;
    }

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.16 });

    revealElements.forEach((element) => revealObserver.observe(element));
});

/* Le visiteur choisit son exemple : aucun défilement automatique ne gêne la lecture. */
document.addEventListener('DOMContentLoaded', () => {
    const styles = {
        artisan: ['ATELIER / BOIS', 'ARTISAN • CRÉATIONS SUR MESURE', 'Du caractère.\nDans chaque détail.'],
        studio: ['STUDIO / FORME', 'CRÉATION • IDENTITÉ VISUELLE', 'Des idées.\nQui prennent vie.'],
        conseil: ['CAP / CONSEIL', 'CONSEIL • ACCOMPAGNEMENT', 'Votre ambition.\nUn cap clair.']
    };
    document.querySelectorAll('[data-demo]').forEach(button => {
        button.addEventListener('click', () => {
            const key = button.dataset.demo;
            const style = styles[key];
            document.querySelector('.demo-window').dataset.theme = key;
            document.querySelector('#demo-brand').textContent = style[0];
            document.querySelector('#demo-kicker').textContent = style[1];
            const title = document.querySelector('#demo-title');
            title.textContent = style[2];
            title.style.whiteSpace = 'pre-line';
            document.querySelectorAll('[data-demo]').forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        });
    });
    const form = document.querySelector('#project-form');
    if (!form) return;
    const params = new URLSearchParams(window.location.search);
    const offer = params.get('offre');
    const concepts = {'atelier-bois':'Atelier Bois — artisan','table-saison':'Table Saison — restaurant','studio-eclat':'Studio Éclat — beauté','snack-pause':'Pause Snack — site une page'};
    const concept = concepts[params.get('concept')];
    if (concept && !form.elements.message.value) form.elements.message.value = 'Bonjour, le concept ' + concept + ' me plaît. Je souhaite un site dans cet esprit pour mon activité : ';
    if (Array.from(form.elements.offer.options).some(option => option.value === offer)) form.elements.offer.value = offer;
    form.addEventListener('submit', event => {
        event.preventDefault();
        const data = new FormData(form);
        const message = `Bonjour E-Vitrine,\n\nMon nom / activité : ${data.get('company_name')}\nMon e-mail : ${data.get('email')}\nTéléphone : ${data.get('phone') || 'Non renseigné'}\nProjet : ${form.elements.offer.selectedOptions[0].textContent}\n\n${data.get('message')}`;
        document.querySelector('#message-draft').value = message;
        document.querySelector('#email-send').href = `mailto:info@e-vitrine.com?subject=${encodeURIComponent('Projet de site — ' + form.elements.offer.selectedOptions[0].textContent)}&body=${encodeURIComponent(message)}`;
        document.querySelector('#message-result').hidden = false;
        document.querySelector('#message-draft').focus();
    });
});
