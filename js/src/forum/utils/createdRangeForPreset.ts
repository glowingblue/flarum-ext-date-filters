import DateFilter from '../../common/DateFilter';
import type { DatePresetId } from '../../common/DateFilter';
import dayjs from 'dayjs';

export const FORMAT = 'YYYY-MM-DD';

export function createdRangeForPreset(preset: DatePresetId): string | null {
	if (preset === DateFilter.DEFAULT_DATE_PRESET) return null;

	const today = dayjs().startOf('day');

	let start: string;
	let endExclusive: string;

	switch (preset) {
		case 'today':
			start = today.format(FORMAT);
			endExclusive = today.add(1, 'day').format(FORMAT);
			break;

		case 'yesterday':
			start = today.subtract(1, 'day').format(FORMAT);
			endExclusive = today.format(FORMAT);
			break;

		case 'past-7-days':
			start = today.subtract(6, 'day').format(FORMAT);
			endExclusive = today.add(1, 'day').format(FORMAT);
			break;

		case 'past-30-days':
			start = today.subtract(29, 'day').format(FORMAT);
			endExclusive = today.add(1, 'day').format(FORMAT);
			break;

		case 'past-365-days':
			start = today.subtract(364, 'day').format(FORMAT);
			endExclusive = today.add(1, 'day').format(FORMAT);
			break;

		default:
			return null;
	}

	return `${start}..${endExclusive}`;
}
