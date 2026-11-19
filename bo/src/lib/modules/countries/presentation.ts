import { generatedTranslation } from '../../i18n/generated-translations';

export type BoCountry = {
  iso_code: string; name: string; native_name: string; active: boolean;
  default_currency: string | null; official_languages: string[];
};
export type BoCountriesResponse = { data: BoCountry[]; can_manage: boolean };
const copy = {
  fr: {
    title: 'Pays', intro: 'Référentiel des pays et territoires.',
    name: 'Pays', currency: 'Devise', languages: 'Langues officielles',
    active: 'Disponible', yes: 'Oui', no: 'Non',
    enable: 'Activer', disable: 'Désactiver', search: 'Rechercher',
    searchHint: 'Nom, code ou devise', refresh: 'Actualiser',
    saved: 'Pays mis à jour.', empty: 'Aucun pays.', forbidden: 'Accès interdit.',
    session: 'Session expirée.', failure: 'Impossible de traiter les pays.'
  },
  en: {
    title: 'Countries', intro: 'Country and territory reference catalog.',
    name: 'Country', currency: 'Currency', languages: 'Official languages',
    active: 'Available', yes: 'Yes', no: 'No',
    enable: 'Enable', disable: 'Disable', search: 'Search',
    searchHint: 'Name, code or currency', refresh: 'Refresh',
    saved: 'Country updated.', empty: 'No countries.', forbidden: 'Access denied.',
    session: 'Session expired.', failure: 'Cannot process countries.'
  }
};
export type CountryCopy = typeof copy.fr;
export function countryCopy(locale: string | null): CountryCopy {
  const source = locale?.toLowerCase().startsWith('en') ? copy.en : copy.fr;
  return Object.fromEntries(Object.entries(source).map(([key, value]) =>
    [key, generatedTranslation(locale, 'bo.countries.' + key) ?? value])) as CountryCopy;
}
export function filterCountries(rows: BoCountry[], search: string, descending: boolean, locale: string): BoCountry[] {
  const term = search.trim().toLocaleLowerCase();
  const collator = new Intl.Collator(locale, { sensitivity: 'base' });
  return rows.filter((country) => !term ||
      [country.iso_code, country.name, country.native_name, country.default_currency ?? '']
        .some((value) => value.toLocaleLowerCase().includes(term)))
    .sort((a, b) => {
      const result = collator.compare(a.name, b.name) || a.iso_code.localeCompare(b.iso_code);
      return descending ? -result : result;
    });
}
