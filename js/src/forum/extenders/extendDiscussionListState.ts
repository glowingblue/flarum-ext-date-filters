import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionListState from 'flarum/forum/states/DiscussionListState';

export default function extendDiscussionListState() {
	DiscussionListState.prototype.dateFilterMap = function (): Record<DatePresetId, string> {
		return {
			pastDay: 'pastDay',
			pastWeek: 'pastWeek',
			pastMonth: 'pastMonth',
			pastYear: 'pastYear',
			custom: 'custom',
		};
	};
}
