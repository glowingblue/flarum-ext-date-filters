export type DatePresetId = 'allTime' | 'today' | 'yesterday' | 'past-7-days' | 'past-30-days' | 'past-365-days';

export default class DateFilter {
	static DEFAULT_DATE_PRESET: DatePresetId = 'allTime';
	static CUSTOM_DATE_PRESET = 'custom';
}
