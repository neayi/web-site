export const locales = ['fr', 'en'] as const;
export type Locale = (typeof locales)[number];
export const defaultLocale: Locale = 'fr';

export const htmlLang: Record<Locale, string> = { fr: 'fr-FR', en: 'en-GB' };

// Clés des pages présentes dans le menu principal, dans l'ordre d'affichage.
// Chaque clé correspond au champ `key` d'un fichier de src/content/pages/{fr,en}/.
export const mainNav = ['triple-performance', 'itinera', 'services', 'about'] as const;
export const footerNav = ['triple-performance', 'itinera', 'services', 'about', 'legal'] as const;

export const ui = {
  fr: {
    'nav.triple-performance': 'Triple Performance',
    'nav.itinera': 'Itinera',
    'nav.services': 'Accompagnement',
    'nav.about': 'À propos',
    'nav.legal': 'Mentions légales',
    'nav.contact': 'Parlons-en',
    'nav.menu': 'Menu',
    'nav.home': 'Accueil Neayi',
    'lang.label': 'Langue',
    'footer.cofarming': 'Membre du réseau Cofarming',
    'footer.products': 'Nos produits',
    'footer.site': 'Le site',
    'form.firstname': 'Prénom',
    'form.lastname': 'Nom',
    'form.email': 'Email',
    'form.company': 'Structure',
    'form.subject': 'Sujet',
    'form.message': 'Message',
    'form.send': 'Envoyer',
    'form.sending': 'Envoi en cours…',
    'form.success': 'Merci, votre message est bien parti. Nous vous répondons rapidement.',
    'form.error': "Le message n'a pas pu être envoyé. Réessayez, ou écrivez-nous directement par email.",
    'form.invalid': 'Vérifiez les champs signalés.',
    'form.optional': 'facultatif',
    'form.subject.partnership': 'Partenariat',
    'form.subject.services': 'Accompagnement',
    'form.subject.itinera': 'Itinera',
    'form.subject.press': 'Presse',
    'form.subject.other': 'Autre',
    'page.by': 'Par',
    'newsletter.title': 'Newsletter',
    'newsletter.link': "S'inscrire à la newsletter",
    'newsletter.description': "Les nouvelles de Neayi, de Triple Performance et d'Itinera, environ une fois par mois.",
    'newsletter.text': "Recevez les nouvelles de Neayi, de Triple Performance et d'Itinera, environ une fois par mois. Nous ne transmettons vos coordonnées à personne et vous pouvez vous désinscrire à tout moment.",
    'newsletter.submit': "S'inscrire",
    'newsletter.success': 'Merci pour votre inscription à notre newsletter !',
    'newsletter.error': "L'inscription n'a pas pu être enregistrée. Réessayez dans un instant.",
    'page.children': 'Pour aller plus loin',
    'page.read': "Lire l'article",
  },
  en: {
    'nav.triple-performance': 'Triple Performance',
    'nav.itinera': 'Itinera',
    'nav.services': 'Services',
    'nav.about': 'About',
    'nav.legal': 'Legal notice',
    'nav.contact': "Let's talk",
    'nav.menu': 'Menu',
    'nav.home': 'Neayi home',
    'lang.label': 'Language',
    'footer.cofarming': 'Member of the Cofarming network',
    'footer.products': 'Our products',
    'footer.site': 'This site',
    'form.firstname': 'First name',
    'form.lastname': 'Last name',
    'form.email': 'Email',
    'form.company': 'Organisation',
    'form.subject': 'Subject',
    'form.message': 'Message',
    'form.send': 'Send',
    'form.sending': 'Sending…',
    'form.success': 'Thank you, your message has been sent. We will get back to you shortly.',
    'form.error': 'Your message could not be sent. Please try again, or email us directly.',
    'form.invalid': 'Please check the highlighted fields.',
    'form.optional': 'optional',
    'form.subject.partnership': 'Partnership',
    'form.subject.services': 'Services',
    'form.subject.itinera': 'Itinera',
    'form.subject.press': 'Press',
    'form.subject.other': 'Other',
    'page.by': 'By',
    'newsletter.title': 'Newsletter',
    'newsletter.link': 'Subscribe to our newsletter',
    'newsletter.description': 'News from Neayi, Triple Performance and Itinera, about once a month.',
    'newsletter.text': 'Get news from Neayi, Triple Performance and Itinera, about once a month (in French). We never share your details and you can unsubscribe at any time.',
    'newsletter.submit': 'Subscribe',
    'newsletter.success': 'Thank you for subscribing to our newsletter!',
    'newsletter.error': 'Your subscription could not be saved. Please try again in a moment.',
    'page.children': 'Further reading',
    'page.read': 'Read the article',
  },
} as const;

export type UiKey = keyof (typeof ui)['fr'];

export function t(lang: Locale, key: UiKey): string {
  return ui[lang][key];
}
