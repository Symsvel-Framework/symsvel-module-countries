import { describe, expect, it } from 'vitest';
import { moduleId } from '../src/lib/modules/symsvel-module-countries';

describe('symsvel-module-countries module', () => {
  it('exports its identifier', () => {
    expect(moduleId).toBe('symsvel-module-countries');
  });
});
