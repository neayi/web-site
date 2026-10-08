import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const link = z.object({ label: z.string(), href: z.string() });

// Page d'accueil : un fichier par langue (fr.md, en.md). Tous les textes et
// chiffres de l'accueil sont ici, à mettre à jour à la main.
const home = defineCollection({
  loader: glob({ pattern: '*.md', base: './src/content/home' }),
  schema: z.object({
    title: z.string(),
    description: z.string(),
    hero: z.object({
      title: z.string(),
      highlight: z.string(),
      text: z.string(),
      primary: link,
      secondary: link,
      imageAlt: z.string(),
    }),
    tripleperformance: z.object({
      title: z.string(),
      text: z.string(),
      stats: z.array(z.object({ value: z.string(), label: z.string() })),
      link,
    }),
    itinera: z.object({
      badge: z.string().nullish(),
      title: z.string(),
      features: z.array(z.object({ tag: z.string(), text: z.string() })),
      link,
    }),
    services: z.object({
      title: z.string(),
      text: z.string(),
      items: z.array(z.string()),
      link,
    }),
    contact: z.object({ title: z.string(), text: z.string() }),
  }),
});

// Pages éditoriales : src/content/pages/{fr,en}/<slug>.md
// L'URL vient du chemin du fichier (fr/a-propos/nos-convictions.md → /a-propos/nos-convictions/) ;
// `key` relie les traductions d'une même page, `parent` rattache une sous-page à la clé de sa page mère.
const pages = defineCollection({
  loader: glob({ pattern: '{fr,en}/**/*.md', base: './src/content/pages' }),
  schema: ({ image }) =>
    z.object({
      key: z.string(),
      title: z.string(),
      description: z.string(),
      eyebrow: z.string().optional(),
      cta: link.optional(),
      parent: z.string().optional(),
      date: z.coerce.date().optional(),
      author: z.string().optional(),
      image: image().optional(),
      imageAlt: z.string().optional(),
    }),
});

export const collections = { home, pages };
