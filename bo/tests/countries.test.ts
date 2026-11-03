import { describe, expect, it } from 'vitest';
import { moduleId } from '../src/lib/modules/countries';

describe('countries module', () => {
  it('exports its identifier', () => {
    expect(moduleId).toBe('countries');
  });
});
