import 'flarum/forum/states/GlobalSearchState';
import 'flarum/forum/states/DiscussionListState';

declare module 'flarum/forum/states/DiscussionListState' {
	export default interface DiscussionListState {
		dateFilterMap(): Record<string, string>;
	}
}

declare module 'flarum/forum/states/GlobalSearchState' {
	export default interface GlobalSearchState {
		changeDate(date: string): void;
	}
}
