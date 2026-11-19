import { describe, expect, it } from 'vitest';
import { filterCountries } from '../src/lib/modules/countries/presentation';

describe('Countries BO', () => {
  const countries = [
    { iso_code: 'BE', name: 'Belgique', native_name: 'België', active: true,
      default_currency: 'EUR', official_languages: ['fr', 'nl'] },
    { iso_code: 'JP', name: 'Japon', native_name: '日本', active: false,
      default_currency: 'JPY', official_languages: ['ja'] }
  ];
  it('searches codes, native names and currency', () => {
    expect(filterCountries(countries, 'JPY', false, 'fr').map((c) => c.iso_code)).toEqual(['JP']);
    expect(filterCountries(countries, 'Belg', false, 'fr').map((c) => c.iso_code)).toEqual(['BE']);
  });
  it('keeps the source list unchanged', () => {
    filterCountries(countries, '', true, 'fr');
    expect(countries.map((c) => c.iso_code)).toEqual(['BE', 'JP']);
  });
});
