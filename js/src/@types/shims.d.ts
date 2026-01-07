import 'flarum/forum/states/DiscussionListState';

declare module 'flarum/forum/states/DiscussionListState' {
	export default interface DiscussionListState {
		dateFilterMap(): Record<string, string>;
	}
}
