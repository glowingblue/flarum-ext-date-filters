export type DatePresetId = 'allTime' | 'pastDay' | 'pastWeek' | 'pastMonth' | 'pastYear';

export default class DateFilter {
	static DEFAULT_DATE_PRESET: DatePresetId = 'allTime';
}
