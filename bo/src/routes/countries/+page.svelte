<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { resolve } from '$app/paths';
  import Button from '../../../../components/Button.svelte';
  import Alert from '../../../../components/Alert.svelte';
  import DataTable from '../../../../components/DataTable.svelte';
  import type { DataColumn, DataAction, SortOrder } from '../../../../components/data-table';
  import type { PageSize } from '../../../../components/pagination';
  import { ApiError } from '../../../../api/client';
  import { boApi } from '../../../../api/session';
  import { uiLanguage } from '../../../../api/locales';
  import { countryCopy, filterCountries, type BoCountry, type BoCountriesResponse } from '../../presentation';

  let countries = $state<BoCountry[]>([]);
  let canManage = $state(false);
  let loading = $state(false);
  let saving = $state('');
  let search = $state('');
  let sortOrder = $state<SortOrder>('asc');
  let page = $state(1);
  let limit = $state<PageSize>(20);
  let error = $state('');
  let notice = $state('');
  const locale = $derived($uiLanguage ?? (typeof navigator === 'undefined' ? 'fr' : navigator.language));
  const t = $derived(countryCopy(locale));
  const filtered = $derived(filterCountries(countries, search, sortOrder === 'desc', locale));
  const visible = $derived(filtered.slice((page - 1) * limit, page * limit));

  const columns = $derived<DataColumn<BoCountry>[]>([
    { key: 'name', label: t.name, sortable: true,
      format: (_, country) => `${country.name} (${country.iso_code})` },
    { key: 'default_currency', label: t.currency, format: (value) => value ? String(value) : '—' },
    { key: 'official_languages', label: t.languages,
      format: (_, country) => country.official_languages.join(', ') || '—' },
    { key: 'active', variant: (country) => country.active ? 'warning' : 'success', label: t.active, format: (value) => value ? t.yes : t.no }
  ]);
  const actions = $derived<DataAction<BoCountry>[]>(canManage ? [{
    key: 'active', variant: (country) => country.active ? 'warning' : 'success', label: (country) => country.active ? t.disable : t.enable,
    disabled: () => saving !== '',
    onClick: (country) => { void toggle(country); }
  }] : []);

  function explain(cause: unknown): string {
    if (cause instanceof ApiError) {
      if (cause.status === 401) { void goto(resolve('/login'), { replaceState: true }); return t.session; }
      if (cause.status === 403 || cause.status === 404) return t.forbidden;
    }
    return t.failure;
  }
  async function load(): Promise<void> {
    loading = true;
    error = '';
    try {
      const response = await boApi<BoCountriesResponse>('/apibo/countries');
      countries = Array.isArray(response.data) ? response.data : [];
      canManage = response.can_manage === true;
    } catch (cause) {
      error = explain(cause);
    } finally {
      loading = false;
    }
  }
  onMount(() => { void load(); });

  async function toggle(country: BoCountry): Promise<void> {
    if (!canManage || saving) return;
    saving = country.iso_code;
    error = '';
    notice = '';
    const active = !country.active;
    try {
      await boApi('/apibo/countries/' + encodeURIComponent(country.iso_code), {
        method: 'PATCH', body: JSON.stringify({ active })
      });
      countries = countries.map((item) => item.iso_code === country.iso_code ? { ...item, active } : item);
      notice = t.saved;
    } catch (cause) {
      error = explain(cause);
    } finally {
      saving = '';
    }
  }
</script>

<svelte:head><title>{t.title} | SymSvel BO</title></svelte:head>
<div class="countries-page">
  <header class="page-heading">
    <div><h1>{t.title}</h1><p>{t.intro}</p></div>
    <Button type="button" variant="secondary" onclick={load} disabled={loading}>{t.refresh}</Button>
  </header>
  {#if error}<Alert kind="error">{error}</Alert>{/if}
  {#if notice}<Alert kind="success">{notice}</Alert>{/if}
  <section class="panel">
    <DataTable rows={visible} {columns} {actions} {loading} rowKey="iso_code" emptyMessage={t.empty}
      filters={[{ key: 'search', label: t.search, type: 'search', placeholder: t.searchHint }]}
      filterValues={{ search }}
      onFilterChange={(_, value) => { search = value; page = 1; }}
      sortKey="name" {sortOrder}
      onSortChange={(_, order) => { sortOrder = order; page = 1; }}
      pagination={{ page, limit, total: filtered.length,
        onPageChange: (value) => page = value,
        onLimitChange: (value) => { limit = value; page = 1; }
      }}
    />
  </section>
</div>

<style>
  .countries-page { max-width: 1200px; margin: auto; display: grid; gap: 20px; }
  .page-heading { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
  .page-heading h1 { margin: 0; }
  .page-heading p { color: var(--bo-muted); }
  .panel { padding: 24px; background: var(--bo-surface); color: var(--bo-text);
    border: 1px solid var(--bo-border); border-radius: 14px; min-width: 0; }
</style>
