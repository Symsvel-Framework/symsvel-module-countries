export const boNavigation = {
  path: '/countries',
  label: { fr: 'Pays', en: 'Countries' },
  resource: 'countries',
  action: 'list',
  // A 404 when multilingual mode is disabled keeps the module out of the BO menu.
  probe: '/apibo/countries'
} as const;
