/* Animate site sections while respecting reduced-motion preferences. */
document.addEventListener("DOMContentLoaded", () => {
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const heroElements = document.querySelectorAll(".hero-content > *, .hero-preview");
    const revealElements = document.querySelectorAll(
        ".header, .benefits-intro, .benefit-card, .homepage-cta, .page-hero > *, .content-section > h2, .section-intro, .content-card, .steps > article, .contact-hero > *, .faq-heading, .faq-item, .contact-form-section, .contact-form, footer"
    );

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
