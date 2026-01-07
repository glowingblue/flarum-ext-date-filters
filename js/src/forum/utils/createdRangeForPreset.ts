import DateFilter from '../../common/DateFilter';
import type { DatePresetId } from '../../common/DateFilter';
import dayjs from 'dayjs';

export const FORMAT = 'YYYY-MM-DD';

export function createdRangeForPreset(preset: DatePresetId): string | null {
	if (preset === DateFilter.DEFAULT_DATE_PRESET) return null;

	const endExclusive = dayjs().add(1, 'day').format(FORMAT);

	let start: string;

	switch (preset) {
		case 'pastDay':
			start = dayjs().subtract(1, 'day').format(FORMAT);
			break;
		case 'pastWeek':
			start = dayjs().subtract(7, 'day').format(FORMAT);
			break;
		case 'pastMonth':
			start = dayjs().subtract(1, 'month').format(FORMAT);
			break;
		case 'pastYear':
			start = dayjs().subtract(1, 'year').format(FORMAT);
			break;
		default:
			return null;
	}

	return `${start}..${endExclusive}`;
}
