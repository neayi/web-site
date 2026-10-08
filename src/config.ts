// Réglages globaux du site, modifiables sans toucher aux composants.

export const SITE = {
  name: 'Neayi',
  address: '8 place Roger Salengro, 31000 Toulouse',
};

export const LINKS = {
  tripleperformance: { fr: 'https://tripleperformance.fr', en: 'https://en.tripleperformance.ag' },
  itinera: 'https://itinera.ag',
  itineraApp: 'https://app.itinera.ag',
  cofarming: 'https://cofarming.info/',
};

// Matomo (instance partagée avec Triple Performance et Itinera).
// Renseigner l'identifiant du site neayi.com créé dans Matomo ; laisser null désactive le suivi.
export const MATOMO = {
  url: 'https://matomo.tripleperformance.fr/',
  siteId: 16 as number | null,
};

// Script de suivi HubSpot : pose le cookie hubspotutk que contact.php transmet au CRM.
export const HUBSPOT_PORTAL_ID = '5882962';

// Formulaire HubSpot « Newsletter » (le même que sur l'ancien site WordPress).
export const NEWSLETTER_FORM_GUID = '8844924d-a201-4991-991a-029e2d2d0b6e';
