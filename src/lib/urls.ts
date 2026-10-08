import { getCollection, type CollectionEntry } from 'astro:content';
import type { Locale } from '../i18n/ui';

// Pages construites directement dans src/pages/ (hors collection).
const fixedRoutes: Record<string, Record<Locale, string>> = {
  home: { fr: '/', en: '/en/' },
  contact: { fr: '/contact/', en: '/en/contact/' },
  newsletter: { fr: '/newsletter/', en: '/en/newsletter/' },
};

export function pageLang(entry: CollectionEntry<'pages'>): Locale {
  return entry.id.split('/')[0] as Locale;
}

export function pageSlug(entry: CollectionEntry<'pages'>): string {
  return entry.id.split('/').slice(1).join('/');
}

export function pagePath(entry: CollectionEntry<'pages'>): string {
  const lang = pageLang(entry);
  return lang === 'fr' ? `/${pageSlug(entry)}/` : `/${lang}/${pageSlug(entry)}/`;
}

// URL d'une page à partir de sa clé de traduction, ou undefined si la page n'existe pas dans cette langue.
export async function urlFor(key: string, lang: Locale): Promise<string | undefined> {
  if (fixedRoutes[key]) return fixedRoutes[key][lang];
  const [entry] = await getCollection('pages', (p) => p.data.key === key && pageLang(p) === lang);
  return entry ? pagePath(entry) : undefined;
}

export async function alternatesFor(key: string): Promise<Partial<Record<Locale, string>>> {
  return { fr: await urlFor(key, 'fr'), en: await urlFor(key, 'en') };
}
