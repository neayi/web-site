<?php
/**
 * Modèle de configuration pour public/contact.php.
 *
 * Copier ce fichier sur O2switch sous le nom « neayi-contact-config.php »,
 * dans le dossier PARENT du dossier où le site est publié
 * (ex. site dans ~/neayi-astro/www/ → config dans ~/neayi-astro/neayi-contact-config.php).
 * Il n'est jamais publié par le déploiement et ne doit pas être commité une fois rempli.
 */

return [
    // Compte HubSpot de Neayi (même portail qu'Itinera).
    'hubspot_portal_id' => '5882962',
    // GUID du formulaire HubSpot dédié à neayi.com (Marketing > Formulaires).
    'hubspot_form_guid' => '',

    // Destinataire(s) des notifications, séparés par des virgules.
    'mail_to' => '',

    // Expéditeur : une adresse validée comme expéditeur dans Brevo (Senders, Domains),
    // idéalement sur le domaine neayi.com authentifié (SPF/DKIM) dans Brevo.
    'mail_from'      => 'noreply@neayi.com',
    'mail_from_name' => 'neayi.com',

    // SMTP Brevo : Brevo > SMTP & API > onglet SMTP.
    // Identifiant du type xxxx@smtp-brevo.com et clé SMTP (pas la clé API).
    'smtp_host'     => 'smtp-relay.brevo.com',
    'smtp_port'     => 587,
    'smtp_user'     => '',
    'smtp_password' => '',
];
