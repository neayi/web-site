<?php
/**
 * Modèle de configuration pour public/contact.php.
 *
 * Copier ce fichier sur O2switch sous le nom « neayi-contact-config.php »,
 * dans le dossier PARENT du dossier où le site est publié
 * (ex. site dans ~/neayi.com/ → config dans ~/neayi-contact-config.php).
 * Il n'est jamais publié par le déploiement et ne doit pas être commité une fois rempli.
 */

return [
    // Compte HubSpot de Neayi (même portail qu'Itinera).
    'hubspot_portal_id' => '5882962',
    // GUID du formulaire HubSpot dédié à neayi.com (Marketing > Formulaires).
    'hubspot_form_guid' => '',

    // Destinataire des notifications, et expéditeur (adresse du domaine, pour la délivrabilité).
    'mail_to'   => '',
    'mail_from' => 'neayi.com <noreply@neayi.com>',
];
