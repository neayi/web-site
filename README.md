# neayi.com

Site institutionnel de Neayi, construit avec [Astro](https://astro.build) et publié en statique sur O2switch.

## Démarrer

```sh
npm install
npm run dev      # http://localhost:4321
npm run build    # génère dist/
npm run preview  # sert dist/ en local
```

Node 22 ou plus récent.

## Modifier le contenu

Tout se fait dans VS Code, en Markdown :

| Contenu | Fichier |
|---|---|
| Accueil (textes, chiffres, liens) | `src/content/home/fr.md`, `src/content/home/en.md` |
| Pages | `src/content/pages/fr/*.md`, `src/content/pages/en/*.md` |
| Libellés (menu, formulaire, pied de page) | `src/i18n/ui.ts` |
| Adresse, liens produits, Matomo, HubSpot | `src/config.ts` |
| Redirections | `public/.htaccess` |

Les chiffres de Triple Performance (visiteurs, pages, retours d'expérience…) sont saisis à la main dans le bloc `stats` de `src/content/home/*.md`.

### Ajouter une page

1. Créer `src/content/pages/fr/mon-slug.md` : l'URL sera `/mon-slug/`.
2. Créer la traduction `src/content/pages/en/my-slug.md` (URL `/en/my-slug/`) avec **la même `key`**. Le sélecteur FR | EN relie les deux pages grâce à cette clé.
3. Pour l'afficher dans le menu, ajouter la clé dans `mainNav` (`src/i18n/ui.ts`) et son libellé `nav.<clé>` dans les deux langues.

```md
---
key: ma-page
title: Titre de la page
description: Phrase d'introduction, reprise dans les moteurs de recherche.
eyebrow: Surtitre (facultatif)
cta: { label: Parlons-en, href: /contact/ }   # facultatif
---

Texte en Markdown…
```

## Formulaire de contact

Le formulaire (`src/components/ContactForm.astro`) envoie les données à `public/contact.php`. Ce script :

- filtre le spam (champ piège, 5 envois maximum par IP toutes les 10 minutes) ;
- crée ou met à jour le contact dans HubSpot via l'API Forms ;
- envoie une notification par email.

Sa configuration n'est pas dans le dépôt : copier `deploy/neayi-contact-config.example.php` sur le serveur sous le nom `neayi-contact-config.php`, **dans le dossier parent** du dossier publié, puis renseigner le GUID du formulaire HubSpot et les adresses email. PHP 8.1 minimum.

## Déploiement

Chaque push sur `main` lance `.github/workflows/deploy.yml` : build, puis envoi de `dist/` par FTPS vers O2switch. Tant que les secrets FTP ne sont pas renseignés, le build tourne mais l'envoi est ignoré.

### Organisation sur O2switch

```
~/neayi-astro/                      ← racine du compte FTP dédié au déploiement
├── neayi-contact-config.php        ← configuration de contact.php (hors du site publié)
└── www/                            ← racine web du domaine neayi.com (contenu de dist/)
```

### Secrets GitHub

Settings > Secrets and variables > Actions > New repository secret :

| Secret | Valeur |
|---|---|
| `FTP_HOST` | nom du serveur O2switch (celui du certificat TLS, visible dans cPanel) |
| `FTP_USER` | compte FTP dédié, ex. `deploy@neayi.com` |
| `FTP_PASSWORD` | mot de passe de ce compte |
| `FTP_REMOTE_DIR` | `/www/` |

Le miroir supprime sur le serveur les fichiers absents de `dist/`. Le workflow refuse donc un `FTP_REMOTE_DIR` vide ou égal à `/`, et le dossier visé ne doit jamais être celui de l'actuel WordPress.
