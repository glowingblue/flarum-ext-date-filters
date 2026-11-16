import DateFilter from '../../common/DateFilter';

import type { DatePresetId } from '../../common/DateFilter';

const DAY_PATTERN = /^\d{4}-\d{2}-\d{2}$/;

/**
 * Days are UTC days: discussion timestamps are stored in UTC, core's
 * `created` filter compares against them as-is, and the server works out
 * the same presets in UTC when it renders a page. Using the browser's
 * local day here would give different results from a page load.
 */
export function utcToday(): string {
	return new Date().toISOString().slice(0, 10);
}

export function addDays(day: string, days: number): string {
	const date = new Date(`${day}T00:00:00Z`);
	date.setUTCDate(date.getUTCDate() + days);

	return date.toISOString().slice(0, 10);
}

/**
 * Whether the value is a real calendar day written as YYYY-MM-DD.
 */
export function isValidDay(value?: string): value is string {
	if (!value || !DAY_PATTERN.test(value)) return false;

	const date = new Date(`${value}T00:00:00Z`);

	return !isNaN(date.getTime()) && date.toISOString().slice(0, 10) === value;
}

export function createPresetDateRange(preset: DatePresetId): string | null {
	if (preset === DateFilter.DEFAULT_DATE_PRESET) return null;

	const today = utcToday();

	let start: string;
	let endExclusive: string;

	switch (preset) {
		case 'today':
			start = today;
			endExclusive = addDays(today, 1);
			break;

		case 'yesterday':
			start = addDays(today, -1);
			endExclusive = today;
			break;

		case 'past-7-days':
			start = addDays(today, -6);
			endExclusive = addDays(today, 1);
			break;

		case 'past-30-days':
			start = addDays(today, -29);
			endExclusive = addDays(today, 1);
			break;

		case 'past-365-days':
			start = addDays(today, -364);
			endExclusive = addDays(today, 1);
			break;

		default:
			return null;
	}

	return `${start}..${endExclusive}`;
}

export function createCustomDateRange(start?: string, end?: string): string | null {
	if (!isValidDay(start) || !isValidDay(end)) return null;

	// YYYY-MM-DD strings sort in date order.
	if (end < start) return null;

	return `${start}..${addDays(end, 1)}`;
}
