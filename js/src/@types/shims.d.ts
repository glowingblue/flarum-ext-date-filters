import 'flarum/forum/states/GlobalSearchState';
import 'flarum/forum/states/DiscussionListState';

import type { DatePresetId } from '../../common/DateFilter';

declare module 'flarum/forum/states/DiscussionListState' {
	export default interface DiscussionListState {
		dateFilterMap(): Record<DatePresetId, string>;
	}
}

declare module 'flarum/forum/states/GlobalSearchState' {
	export default interface GlobalSearchState {
		changeDate(date: string): void;
	}
}
