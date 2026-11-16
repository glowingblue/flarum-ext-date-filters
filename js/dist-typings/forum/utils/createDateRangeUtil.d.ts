import type { DatePresetId } from '../../common/DateFilter';
/**
 * Days are UTC days: discussion timestamps are stored in UTC, core's
 * `created` filter compares against them as-is, and the server works out
 * the same presets in UTC when it renders a page. Using the browser's
 * local day here would give different results from a page load.
 */
export declare function utcToday(): string;
export declare function addDays(day: string, days: number): string;
/**
 * Whether the value is a real calendar day written as YYYY-MM-DD.
 */
export declare function isValidDay(value?: string): value is string;
export declare function createPresetDateRange(preset: DatePresetId): string | null;
export declare function createCustomDateRange(start?: string, end?: string): string | null;
