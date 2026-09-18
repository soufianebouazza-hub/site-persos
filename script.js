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
