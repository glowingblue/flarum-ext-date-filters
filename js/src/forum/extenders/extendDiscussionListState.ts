import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionListState from 'flarum/forum/states/DiscussionListState';
import DateFilter from '../../common/DateFilter';
import { createdRangeForPreset } from '../utils/createdRangeForPreset';

import type { PaginatedListParams } from 'flarum/common/states/PaginatedListState';
import type { DatePresetId } from '../../common/DateFilter';

export default function extendDiscussionListState() {
	DiscussionListState.prototype.dateFilterMap = function (): Record<DatePresetId, string> {
		return {
			allTime: 'allTime',
			pastDay: 'pastDay',
			pastWeek: 'pastWeek',
			pastMonth: 'pastMonth',
			pastYear: 'pastYear',
		};
	};

	extend(DiscussionListState.prototype, 'requestParams', function (params: PaginatedListParams) {
		const selectedPreset = app.search.params().date ?? DateFilter.DEFAULT_DATE_PRESET;
		const preset = (selectedPreset in app.discussions.dateFilterMap() ? selectedPreset : DateFilter.DEFAULT_DATE_PRESET) as DatePresetId;

		const created = createdRangeForPreset(preset);

		params.filter ||= {};

		if (created) {
			params.filter.created = created;
		} else {
			delete params.filter.created;
		}
	});
}
